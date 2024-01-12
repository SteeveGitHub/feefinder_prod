<?php
// getDynamicValues.php

require_once("../../database.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["type"])) {
    $type = $_GET["type"];
    
    // Assurez-vous que $type est valide (repas, nuit, transport)
    // Vous pouvez ajouter une validation supplémentaire si nécessaire
    
    $sqlSelect = "SELECT montant FROM fraisforfait WHERE id = ?";
    $stmtSelect = $dbh->prepare($sqlSelect);

    switch ($type) {
        case 'repas':
            $stmtSelect->execute([2]); // Remplacez 2 par l'ID approprié pour les repas
            break;
        case 'nuit':
            $stmtSelect->execute([1]); // Remplacez 1 par l'ID approprié pour les nuitées
            break;
        case 'transport':
            $stmtSelect->execute([3]); // Remplacez 3 par l'ID approprié pour les transports
            break;
        default:
            // Gérez le cas où le type n'est pas valide
            echo json_encode(['error' => 'Type invalide']);
            exit;
    }

    $montant = $stmtSelect->fetchColumn();

    // Retournez le résultat au format JSON
    echo json_encode(['montant' => $montant]);
} else {
    // Gérez le cas où la requête n'est pas valide
    echo json_encode(['error' => 'Requête non valide']);
}
?>
