<?php
header('Content-Type: application/json');
echo json_encode([
    "service" => "Gestion_Employe API",
    "status"  => "online",
    "endpoints" => [
        "test"      => "/test.php",
        "login"     => "/login.php",
        "employes"  => "/employes.php",
        "stats"     => "/stats.php"
    ]
]);