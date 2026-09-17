<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer les données JSON envoyées par Vue
$input = file_get_contents("php://input");
$data = json_decode($input);

// Si les données sont vides, on renvoie une erreur
if ($data === null) {
    echo json_encode(["message" => "Erreur : Aucune donnée reçue", "raw" => $input]);
    exit;
}

// Vérifier les identifiants (insensible à la casse + espaces ignorés)
$receivedUser = isset($data->username) ? trim((string) $data->username) : '';
$receivedPass = isset($data->password) ? trim((string) $data->password) : '';

if (strcasecmp($receivedUser, 'rajaonaira') === 0 && $receivedPass === 'bakay@@2005') {
    echo json_encode(["message" => "Connexion réussie", "token" => "123456"]);
} else {
    echo json_encode([
        "message" => "Échec de la connexion",
        "received" => ["username" => $receivedUser, "password" => $receivedPass]
    ]);
}
?>