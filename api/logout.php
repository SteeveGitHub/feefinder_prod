<?php
header("Content-Type:application/json");
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (!isset($_SESSION['user'])) {
    echo json_encode(array('status' => 401, 'message' => 'Utilisateur non connecté'));
    exit;
}

// Inclure le fichier de connexion à la base de données
include('../database.php');


try {
    // Récupérer l'ID de l'utilisateur depuis la session
    $userId = $_SESSION['user'];

    // Préparer la requête pour mettre à jour le token à NULL pour l'utilisateur
    $updateToken = $dbh->prepare("UPDATE visiteur SET token = NULL, token_timestamp = NULL WHERE id = :id");
    $updateToken->execute(['id' => $userId]);

    // Nettoyer les variables de session
    $_SESSION = array();

    // Détruire la session
    session_destroy();

    // Réponse en cas de succès
    echo json_encode(array('status' => 200, 'message' => 'Déconnexion réussie'));
} catch (PDOException $e) {
    // Gérer les erreurs de base de données
    echo json_encode(array('status' => 500, 'message' => "Erreur de base de données : " . $e->getMessage()));
}
?>
