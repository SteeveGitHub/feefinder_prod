<?php
include('../../database.php');

function getUtilisateurData($id)
{
    global $dbh;
    $requeteUtilisateur = $dbh->prepare("SELECT * FROM visiteur WHERE id = ?");
    $requeteUtilisateur->execute([$id]);
    return $requeteUtilisateur->fetch(PDO::FETCH_ASSOC);
}

function getFraisData($idUtilisateur)
{
    global $dbh;
    $requeteFrais = $dbh->prepare("SELECT * FROM frais WHERE user_id = ?");
    $requeteFrais->execute([$idUtilisateur]);
    return $requeteFrais->fetchAll(PDO::FETCH_ASSOC);
}

function getHorsForfaitData($idUtilisateur)
{
    global $dbh;
    $requeteHorsForfait = $dbh->prepare("SELECT * FROM hors_forfait WHERE user_id = ?");
    $requeteHorsForfait->execute([$idUtilisateur]);
    return $requeteHorsForfait->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalFraisForfait($fraisData)
{
    $totalFraisForfait = 0;
    foreach ($fraisData as $frais) {
        $totalFraisForfait += $frais["montantRestant"];
    }
    return $totalFraisForfait;
}

function getTotalHorsForfait($horsForfaitData)
{
    $totalHorsForfait = 0;
    foreach ($horsForfaitData as $horsForfait) {
        $totalHorsForfait += $horsForfait["montantRestant"];
    }
    return $totalHorsForfait;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../../styles/index.css">
</head>

<body>
    <?php include "../../views/navbar/navbarView.php" ?>
    <section class="comptable">
        <?php
        $idUtilisateur = isset($_GET['id']) ? $_GET['id'] : null;

        if (!$idUtilisateur) {
            header("Location: chemin/vers/la/page/d-erreur");
            exit();
        }

        $utilisateurData = getUtilisateurData($idUtilisateur);
        $fraisData = getFraisData($idUtilisateur);
        $horsForfaitData = getHorsForfaitData($idUtilisateur);
        $totalFraisForfait = getTotalFraisForfait($fraisData);
        $totalHorsForfait = getTotalHorsForfait($horsForfaitData);
        ?>

        <h1 class="comptableH1">Détails de l'Utilisateur</h1>

        <div>
            <p>Nom: <?= $utilisateurData["nom"] ?></p>
            <p>Prénom: <?= $utilisateurData["prenom"] ?></p>
            <p>Numéro: <?= $utilisateurData["numero"] ?></p>
            <p>Email: <?= $utilisateurData["email"] ?></p>
            <p>Statut: <?= ($utilisateurData["status"] == 1 ? "Visiteur" : ($utilisateurData["status"] == 2 ? "Admin" : "Comptable")) ?></p>
        </div>

        <div>
            <h2>Total Frais Forfait: <?= $totalFraisForfait ?>€</h2>
        </div>

        <div>
            <h2>Total Hors Forfait: <?= $totalHorsForfait ?>€</h2>
        </div>

        <h1>Frais Forfait</h1>
        <table class="renderer comptable">
            <tr>
                <!-- ... (Entête du tableau) ... -->
            </tr>
            <?php foreach ($fraisData as $frais) { ?>
                <!-- ... (Lignes du tableau pour les frais forfait) ... -->
            <?php } ?>
        </table>

        <h1>Hors Forfait</h1>
        <table class="renderer comptable">
            <tr>
                <!-- ... (Entête du tableau) ... -->
            </tr>
            <?php foreach ($horsForfaitData as $horsForfait) { ?>
                <!-- ... (Lignes du tableau pour les hors forfait) ... -->
            <?php } ?>
        </table>
    </section>

    <script>
        // Vos fonctions JavaScript restent les mêmes
    </script>

</body>

</html>
