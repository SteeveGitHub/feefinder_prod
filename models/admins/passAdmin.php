<?php
include('../../database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $action = $_POST['action'];
        $id = $_POST['id'];

        if ($action === 'admin') {
            $requete = $dbh->prepare("UPDATE visiteur SET status = 2 WHERE id = ?");
            $requete->execute([$id]);
        } else if ($action === 'comptable') {
            $requete = $dbh->prepare("UPDATE visiteur SET status = 3 WHERE id = ?");
            $requete->execute([$id]);
        } else if ($action === 'visiteur') {
            $requete = $dbh->prepare("UPDATE visiteur SET status = 1 WHERE id = ?");
            $requete->execute([$id]);
        } else if ($action === 'bloqué') {
            $requete = $dbh->prepare("UPDATE visiteur SET status = 4 WHERE id = ?");
            $requete->execute([$id]);
        }

        echo "Success";
        header('Location: ../../views/admin/adminView.php');
    } else {
        echo "Paramètres manquants.";
    }
} else {
    echo "Méthode non autorisée.";
}
