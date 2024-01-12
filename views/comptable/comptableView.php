<?php
include('../../database.php');

function updateValideComptable($table, $id, $value, $comment)
{
    global $dbh;
    $requete = $dbh->prepare("UPDATE $table SET valideComptable = ?, comment = ? WHERE id = ?");
    $requete->execute([$value, $comment, $id]);
}

// Récupérer les données de la table frais
$requeteFrais = $dbh->prepare("SELECT * FROM frais");
$requeteFrais->execute();
$fraisData = $requeteFrais->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les données de la table hors_forfait
$requeteHorsForfait = $dbh->prepare("SELECT * FROM hors_forfait");
$requeteHorsForfait->execute();
$horsForfaitData = $requeteHorsForfait->fetchAll(PDO::FETCH_ASSOC);

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
    <?php
    include "../../views/navbar/navbarView.php"
    ?>
    <section class="comptable">
        <h1 class="comptableH1">Frais Forfait</h1>
        <table class="renderer comptable">
            <input type="button" title="Valider toutes les fiches" onclick="validAllFiche()" value="Valider les Fiches">

            <tr>
                <th>Date de début</th>
                <th>Coût total nuit</th>
                <th>Nb nuits</th>
                <th>Coût total repas</th>
                <th>Nb repas</th>
                <th>KM</th>
                <th>Transport Type</th>
                <th>Valide Comptable</th>
                <th>Montant Restant</th>
                <th>Commentaire</th>
                <th>Action</th>
            </tr>

            <?php
            foreach ($fraisData as $frais) {
                $currentDate = new DateTime();
                $createdDate = DateTime::createFromFormat('Y-m-d H:i:s', $frais["date_debut"]);
                $difference = $currentDate->diff($createdDate);


                echo "<tr style='background-color: #01C372;'>";
                echo "<td>" . $frais["date_debut"] . "</td>";
                echo "<td>" . $frais["total_night_price"] . "€</td>";
                echo "<td>" . $frais["night_quantity"] . "</td>";
                echo "<td>" . $frais["total_meal_price"] . "€</td>";
                echo "<td>" . $frais["meal_quantity"] . "</td>";
                echo "<td>" . $frais["km"] . "</td>";
                echo "<td>" . $frais["transport_type"] . "</td>";
                if ($frais["valideComptable"] == 0) {
                    echo "<td>En attente</td>";
                } else if ($frais["valideComptable"] == 1) {
                    echo "<td>Validé</td>";
                } else {
                    echo "<td>Refusé</td>";
                }
                echo "<td>" . $frais["montantRestant"] . "€</td>";

                $commentaireDB = $frais["comment"]; // Assurez-vous d'adapter cela à votre structure de base de données
                $ficheRefusee = $frais["valideComptable"] == 2;
                $ficheValidee = $frais["valideComptable"] == 1;

                if (($commentaireDB != "" && !$ficheValidee && !$ficheRefusee) || ($ficheValidee || $ficheRefusee)) {
                    echo "<td>$commentaireDB</td>";
                } else {
                    // Sinon, on affiche l'input
                    echo "<td><input type='text' name='comment_frais_" . $frais["id"] . "' id='comment_frais_" . $frais["id"] . "'></td>";
                }


                // echo "<td><input type='text' name='comment_frais_" . $frais["id"] . "' id='comment_frais_" . $frais["id"] . "'></td>";
                echo "<td>";
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='id_frais' value='" . $frais["id"] . "'>";
                if (!$frais["valideComptable"]) {
                    if ($difference->days < 7) {
                        echo "<button type='button' onclick='showAlert(\"Attention, vous ne pouvez ni valider ni refuser cette fiche car elle existe depuis moins de 7 jours\")'>Valider</button>";
                        echo "<button type='button' onclick='showAlert(\"Attention, vous ne pouvez ni valider ni refuser cette fiche car elle existe depuis moins de 7 jours\")'>Refuser</button>";                

                    }else{
                        echo '<button type="button" onclick="updateValideComptable(\'frais\', ' . $frais["id"] . ', 1)">Valider</button>';
                        echo '<button type="button" onclick="updateValideComptable(\'frais\', ' . $frais["id"] . ', 2)">Refuser</button>';    
                    }
                      }
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>

        </table>

        <h1>Hors Forfait</h1>
        <table class="renderer comptable">
            <tr>
                <th>Description</th>
                <th>Coût total</th>
                <th>Justificatif</th>
                <th>Valide Comptable</th>
                <th>Montant Restant</th>
                <th>Nb jours</th>
                <th>Pris en charge</th>
                <th>Commentaire</th>
                <th>Action</th>
            </tr>

            <?php
            foreach ($horsForfaitData as $horsForfait) {
                $currentDate = new DateTime();
                $createdDate = DateTime::createFromFormat('Y-m-d H:i:s', $horsForfait["created_at"]);
                $difference = $currentDate->diff($createdDate);

                echo "<tr style='background-color: #ffb6b6;'>";
                echo "<td>" . $horsForfait["description"] . "</td>";
                echo "<td>" . $horsForfait["total_price"] . "€</td>";
                echo "<td>" . $horsForfait["justificatif"] . "</td>";
                if ($horsForfait["valideComptable"] == 0) {
                    echo "<td>En attente</td>";
                } else if ($horsForfait["valideComptable"] == 1) {
                    echo "<td>Validé</td>";
                } else {
                    echo "<td>Refusé</td>";
                }
                // echo $horsForfait["valideComptable"] == 0 ? "<td>" . "En attente" . "</td>" : "<td>" . "Validé" . "</td>";
                echo "<td>" . $horsForfait["montantRestant"] . "€</td>";
                echo "<td>" . $horsForfait["number_days"] . "</td>";
                if ($horsForfait["pris_en_charge"] != "") {
                    echo "<td>" . $horsForfait["pris_en_charge"] . "€</td>";
                } else {
                    echo "<td><input type='number' name='pris_en_charge_hors_forfait_" . $horsForfait["id"] . "' id='pris_en_charge_hors_forfait_" . $horsForfait["id"] . "'></td>";
                }
                $commentaireDB = $horsForfait["comment"]; // Assurez-vous d'adapter cela à votre structure de base de données
                $ficheRefusee = $horsForfait["valideComptable"] == 2;
                $ficheValidee = $horsForfait["valideComptable"] == 1;

                if (($commentaireDB != "" && !$ficheValidee && !$ficheRefusee) || ($ficheValidee || $ficheRefusee)) {
                    echo "<td>$commentaireDB</td>";
                } else {
                    // Sinon, on affiche l'input
                    echo "<td><input type='text' name='comment_hors_frais_" . $horsForfait["id"] . "' id='comment_hors_frais_" . $horsForfait["id"] . "'></td>";
                }
                // echo "<td><input type='text' name='comment_hors_frais_" . $horsForfait["id"] . "' id='comment_hors_frais_" . $horsForfait["id"] . "'></td>";
                echo "<td>";
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='id_hors_forfait' value='" . $horsForfait["id"] . "'>";
                if (!$horsForfait["valideComptable"]) {

                    if ($difference->days < 7) {
                        echo "<button type='button' onclick='showAlert(\"Attention, vous ne pouvez ni valider ni refuser cette fiche car elle existe depuis moins de 7 jours\")'>Valider</button>";
                        echo "<button type='button' onclick='showAlert(\"Attention, vous ne pouvez ni valider ni refuser cette fiche car elle existe depuis moins de 7 jours\")'>Refuser</button>";                

                    }else{
                    echo "<button type='button' onclick='updateValideComptable(\"hors_forfait\", {$horsForfait["id"]}, 1)'>Valider</button>";
                    echo "<button type='button' onclick='updateValideComptable(\"hors_forfait\", {$horsForfait["id"]}, 2)'>Refuser</button>";
                }
                
                }

                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </section>
    <script>
        function validAllFiche(table, id, value) {
            $.ajax({
                url: '../../models/comptable/updateAllFiche.php',
                method: 'POST',
                success: function(response) {
                    alert('Opération réussie');
                    location.reload();
                },
                error: function(error) {
                    alert('Erreur lors de l\'opération');
                }
            });
        }

        function updateValideComptable(table, id, value) {
            var commentInput = $("#comment_frais_" + id);
            var commentInputHors = $("#comment_hors_frais_" + id);
            var prisEnChargeInput = $("#pris_en_charge_hors_forfait_" + id);
            $.ajax({
                url: '../../models/comptable/comptable.php',
                method: 'POST',
                data: {
                    table: table,
                    id: id,
                    value: value,
                    comment: commentInput.val() ? commentInput.val() : commentInputHors.val(),
                    prisEnCharge: prisEnChargeInput.val()
                },
                success: function(response) {
                    alert('Opération réussie');
                    location.reload();
                },
                error: function(error) {
                    alert('Erreur lors de l\'opération');
                }
            });
        }

        function showAlert(){
            alert('Attention, vous ne pouvez ni valider ni refuser cette fiche car elle existe depuis moins de 7 jours');
        }
    </script>

</body>

</html>