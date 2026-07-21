<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    // ---------------------------------------------------------
    // GET : Récupérer les employés (actifs ou archivés)
    // ---------------------------------------------------------
    case 'GET':
        if (isset($_GET['archived'])) {
            // Récupérer les employés archivés (date_suppression non NULL)
            $stmt = $conn->prepare("SELECT * FROM employe WHERE date_suppression IS NOT NULL ORDER BY date_suppression DESC");
            $stmt->execute();
            $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($employes);
        } 
        elseif (isset($_GET['numEmp'])) {
            // Vérifier si un numéro spécifique existe (pour les doublons)
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM employe WHERE numEmp = ? AND date_suppression IS NULL");
            $stmt->execute([$_GET['numEmp']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['exists' => $result['count'] > 0]);
        } 
        else {
            // Récupérer les employés actifs
            $stmt = $conn->prepare("SELECT * FROM employe WHERE date_suppression IS NULL ORDER BY date_ajout DESC");
            $stmt->execute();
            $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($employes);
        }
        break;

    // ---------------------------------------------------------
    // POST : Ajouter, restaurer, payer ou renouveler
    // ---------------------------------------------------------
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        // 1. Restauration d'un employé archivé
        if (isset($_GET['restore'])) {
            $stmt = $conn->prepare("UPDATE employe SET date_suppression = NULL WHERE id = ?");
            $stmt->execute([$data->id]);
            echo json_encode(["message" => "Employé restauré avec succès"]);
            break;
        }

        // 2. Paiement : passer le statut à "Payé" si la date de fin est dépassée
        if (isset($_GET['pay'])) {
            $stmt = $conn->prepare("UPDATE employe SET statut_paiement = 'Payé' WHERE id = ? AND date_fin_contrat <= CURDATE()");
            $stmt->execute([$data->id]);
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                echo json_encode(["message" => "Paiement effectué avec succès !"]);
            } else {
                echo json_encode(["message" => "Impossible de payer : la date de fin n'est pas encore dépassée ou l'employé n'existe pas."]);
            }
            break;
        }

        // 3. Renouvellement de contrat (ajouter des jours)
        if (isset($_GET['renew'])) {
            // Vérifier que les données sont présentes
            if (!isset($data->id) || !isset($data->jours_supplementaires)) {
                echo json_encode(["message" => "Données manquantes pour le renouvellement"]);
                break;
            }
            // Récupérer l'employé
            $stmt = $conn->prepare("SELECT date_fin_contrat, statut_paiement FROM employe WHERE id = ?");
            $stmt->execute([$data->id]);
            $emp = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$emp) {
                echo json_encode(["message" => "Employé non trouvé"]);
                break;
            }
            // Nouvelle date de fin = ancienne date de fin + jours_supplementaires
            $new_date_fin = new DateTime($emp['date_fin_contrat']);
            $new_date_fin->modify('+ ' . (int)$data->jours_supplementaires . ' days');
            $date_fin_contrat = $new_date_fin->format('Y-m-d');
            // Mettre à jour la date de fin et remettre le statut paiement à "Non payé"
            $stmt = $conn->prepare("UPDATE employe SET date_fin_contrat = ?, statut_paiement = 'Non payé' WHERE id = ?");
            $stmt->execute([$date_fin_contrat, $data->id]);
            echo json_encode(["message" => "Contrat renouvelé jusqu'au $date_fin_contrat"]);
            break;
        }

        // 4. Ajout normal d'un employé (avec calcul de la date de fin)
        if (!isset($data->numEmp, $data->nom, $data->nb_jours, $data->taux_journalier, $data->date_ajout, $data->emploi)) {
            echo json_encode(["message" => "Données incomplètes pour l'ajout"]);
            break;
        }
        $date_ajout = new DateTime($data->date_ajout);
        $date_ajout->modify('+ ' . (int)$data->nb_jours . ' days');
        $date_fin_contrat = $date_ajout->format('Y-m-d');

        $stmt = $conn->prepare("INSERT INTO employe (numEmp, nom, nb_jours, taux_journalier, date_ajout, emploi, date_fin_contrat, statut_paiement) VALUES (?, ?, ?, ?, ?, ?, ?, 'Non payé')");
        $stmt->execute([
            $data->numEmp,
            $data->nom,
            $data->nb_jours,
            $data->taux_journalier,
            $data->date_ajout,
            $data->emploi,
            $date_fin_contrat
        ]);
        echo json_encode(["message" => "Insertion réussie"]);
        break;

    // ---------------------------------------------------------
    // PUT : Modifier un employé (recalcule la date de fin)
    // ---------------------------------------------------------
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id, $data->nom, $data->nb_jours, $data->taux_journalier)) {
            echo json_encode(["message" => "Données incomplètes pour la modification"]);
            break;
        }
        // Récupérer la date d'ajout
        $stmt = $conn->prepare("SELECT date_ajout FROM employe WHERE id = ?");
        $stmt->execute([$data->id]);
        $employe = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$employe) {
            http_response_code(404);
            echo json_encode(["message" => "Employé non trouvé"]);
            break;
        }
        $date_ajout = $employe['date_ajout'];

        // Recalculer la date de fin en fonction du nouveau nb_jours
        $new_date_fin = new DateTime($date_ajout);
        $new_date_fin->modify('+ ' . (int)$data->nb_jours . ' days');
        $date_fin_contrat = $new_date_fin->format('Y-m-d');

        // Mise à jour
        $stmt = $conn->prepare("UPDATE employe SET nom = ?, nb_jours = ?, taux_journalier = ?, date_fin_contrat = ? WHERE id = ?");
        $stmt->execute([$data->nom, $data->nb_jours, $data->taux_journalier, $date_fin_contrat, $data->id]);
        echo json_encode(["message" => "Modification réussie, nouvelle date de fin calculée"]);
        break;

    // ---------------------------------------------------------
    // DELETE : Archiver ou supprimer définitivement
    // ---------------------------------------------------------
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id)) {
            echo json_encode(["message" => "ID manquant"]);
            break;
        }
        if (isset($_GET['permanent'])) {
            // Suppression définitive (uniquement depuis l'archive)
            $stmt = $conn->prepare("DELETE FROM employe WHERE id = ?");
            $stmt->execute([$data->id]);
            echo json_encode(["message" => "Suppression définitive réussie"]);
        } else {
            // Archivage (marque la date de suppression)
            $stmt = $conn->prepare("UPDATE employe SET date_suppression = NOW() WHERE id = ?");
            $stmt->execute([$data->id]);
            echo json_encode(["message" => "Employé archivé avec succès"]);
        }
        break;

    // ---------------------------------------------------------
    // OPTIONS : Gestion CORS pour les requêtes préflight
    // ---------------------------------------------------------
    case 'OPTIONS':
        http_response_code(200);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>