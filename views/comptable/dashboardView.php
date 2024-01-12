<?php
include('../../database.php');

// Récupérer les données des utilisateurs
$requeteUtilisateurs = $dbh->prepare("SELECT * FROM visiteur");
$requeteUtilisateurs->execute();
$utilisateursData = $requeteUtilisateurs->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link rel="stylesheet" href="../../styles/index.css">
</head>

<body>
    <?php
    include "../../views/navbar/navbarView.php"
    ?>
    <section class="utilisateurs">
        <h1>Liste des Utilisateurs</h1>
        <table class="renderer comptable">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Numéro</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>

            <?php
            foreach ($utilisateursData as $utilisateur) {
                echo "<tr>";
                echo "<td>" . $utilisateur["id"] . "</td>";
                echo "<td>" . $utilisateur["nom"] . "</td>";
                echo "<td>" . $utilisateur["prenom"] . "</td>";
                echo "<td>" . $utilisateur["numero"] . "</td>";
                echo "<td>" . $utilisateur["email"] . "</td>";
                echo "<td>" . ($utilisateur["status"] == 1 ? "Visiteur" : ($utilisateur["status"] == 2 ? "Admin" : "Comptable")) . "</td>";
                echo "<td><a href='detailUser.php?id=" . $utilisateur["id"] . "'>Voir détails</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </section>
</body>

</html>
