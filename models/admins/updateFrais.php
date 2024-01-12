<?php
include('../../database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fraisUpdates = $_POST;
    foreach ($fraisUpdates as $key => $value) {
        if (strpos($key, 'frais_') === 0 && is_numeric(substr($key, 6))) {
            $fraisId = substr($key, 6);
            $montant = floatval($value);

            // Mettez à jour la base de données
            $requete = $dbh->prepare("UPDATE fraisforfait SET montant = ? WHERE id = ?");
            $requete->execute([$montant, $fraisId]);
        }
    }
    echo "Mise à jour des frais forfaitaires réussie.";
} else {
    echo "Méthode non autorisée.";
}
