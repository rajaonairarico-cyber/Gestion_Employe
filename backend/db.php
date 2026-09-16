<?php
$driver   = getenv('DB_DRIVER')   ?: 'mysql';
$host     = getenv('DB_HOST')     ?: 'localhost';
$db_name  = getenv('DB_NAME')     ?: 'gestion_employe';
$username = getenv('DB_USER')     ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'bakay@@2005';

try {
    if ($driver === 'pgsql') {
        $port = getenv('DB_PORT') ?: '5432';
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
    } else {
        $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    }
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Auto-create table if missing (safe: IF NOT EXISTS)
    if ($driver === 'pgsql') {
        $conn->exec('CREATE TABLE IF NOT EXISTS employe (
            id SERIAL PRIMARY KEY,
            "numEmp" VARCHAR(20) NOT NULL,
            nom VARCHAR(255) NOT NULL,
            emploi VARCHAR(100),
            nb_jours INT NOT NULL,
            taux_journalier NUMERIC(12,2) NOT NULL,
            date_ajout TIMESTAMP,
            date_fin_contrat DATE,
            statut_paiement VARCHAR(20) DEFAULT \'Non payé\',
            date_suppression TIMESTAMP NULL
        )');
    } else {
        $conn->exec('CREATE TABLE IF NOT EXISTS employe (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numEmp VARCHAR(20) NOT NULL,
            nom VARCHAR(255) NOT NULL,
            emploi VARCHAR(100),
            nb_jours INT NOT NULL,
            taux_journalier DECIMAL(12,2) NOT NULL,
            date_ajout DATETIME,
            date_fin_contrat DATE,
            statut_paiement VARCHAR(20) DEFAULT "Non payé",
            date_suppression DATETIME NULL
        )');
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur de connexion à la base de données"]);
    exit;
}
?>