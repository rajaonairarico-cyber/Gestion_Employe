<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require 'db.php';

// Calcul du bilan
$stmt = $conn->prepare("SELECT SUM(nb_jours * taux_journalier) as total, MIN(nb_jours * taux_journalier) as min, MAX(nb_jours * taux_journalier) as max FROM employe");
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($stats);
?>