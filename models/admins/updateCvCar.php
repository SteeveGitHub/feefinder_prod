<?php
include('../../database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cvValue = $_POST['cv_car'];
    $userId = $_POST['user_id'];

    $requete = $dbh->prepare("UPDATE visiteur SET cv_car = ? WHERE id = ?");
    $requete->execute([$cvValue, $userId]);

    echo "Mise à jour de CV Car réussie.";
} else {
    echo "Méthode non autorisée.";
}
