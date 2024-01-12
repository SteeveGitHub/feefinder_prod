<?php
require_once("../../database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['forfaitSubmit'])) {

        // Traitement du formulaire "Fiche Forfait"
        $user_id = $_SESSION['user'];
        $date_debut = date('Y-m-d', strtotime($_POST["date"]));

        // Traitement des hébergements
        $price_night = $_POST["priceNight"] ? $_POST["priceNight"] : 0;
        $number_night = $_POST["numberNight"] ? $_POST["numberNight"] : 0;
        // $total_nuit_user = ($price_night * $number_night)/$number_night;
        $total_nuit_user = ($number_night != 0) ? $price_night : 0;
        $total_nuit_user = intval($total_nuit_user);

        // Traitement des repas
        $number_meal = $_POST["numberMeal"] ? $_POST["numberMeal"] : 0;
        $price_meal = $_POST["priceMeal"] ? $_POST["priceMeal"] : 0;
        $total_meal_user = ($number_meal != 0) ? $price_meal : 0;
        $total_meal_user = intval($total_meal_user);

        $total_user = $total_nuit_user + $total_meal_user;
        $total_user = intval($total_user);

        // Traitement des trajets
        $km = $_POST["km"] ? $_POST["km"] : 0;
        $transport_type = $_POST["transport"];

        // Récupération des montants depuis la table "fraisforfait"
        $sqlSelect = "SELECT montant FROM fraisforfait WHERE id = ?";
        $stmtSelect = $dbh->prepare($sqlSelect);
        $id_night = 1;
        $id_meal = 2;
        $id_transport = 3;

        // Calcul du montant à charge restant pour chaque type de frais
        $stmtSelect->execute([$id_night]);
        $montant_night = $stmtSelect->fetchColumn();

        $stmtSelect->execute([$id_meal]);
        $montant_meal = $stmtSelect->fetchColumn();

        // $stmtSelect->execute([$id_transport]);
        // $montant_transport = $stmtSelect->fetchColumn();

        $refund_night = intval($number_night) * intval($montant_night);
        $refund_meal = intval($number_meal) * intval($montant_meal);
        // $refund_transport = $km * $montant_transport;

        // Calcul du montant total à charge restant
        $total_refund = intval($refund_night) + intval($refund_meal);
        // $total_refund = intval($total_refund);

        if ($total_user < $total_refund) {
            // si ce que paie l'utilisteur est inférieur au montant remboursé,
            // on rembourse ce que l'utilisateur a payé
            $total_refund = $total_user;
        }

        $sqlSelectCV = "SELECT cv_car FROM visiteur WHERE id = ?";
        $stmtSelectCV = $dbh->prepare($sqlSelectCV);
        $stmtSelectCV->execute([$user_id]);
        $cv_fiscal = $stmtSelectCV->fetchColumn();

        function calculerMontantRembourse($distance, $table, $puissance, $dbhvar)
        //calcul du remboursement en fonction des frais kilométriques
        {
            $remboursement = 0;
            $sqlSelectKm = "SELECT distance_jusqu_5000_km, distance_5001_a_20000_km_coefficient, distance_5001_a_20000_km_fixe, distance_plus_20000_km FROM frais_kilometrique_gouvernement WHERE puissance_administrative = ?";
            $stmtSelectKm = $dbhvar->prepare($sqlSelectKm);
            $stmtSelectKm->execute([$puissance]);
            $donneesKm = $stmtSelectKm->fetch(PDO::FETCH_ASSOC);

            // Calculer le montant remboursé en fonction de la distance
            if ($distance <= 5000) {
                $remboursement = $distance * floatval($donneesKm['distance_jusqu_5000_km']);
            } elseif ($distance <= 20000) {
                $remboursement = $distance * floatval($donneesKm['distance_5001_a_20000_km_coefficient']) + intval($donneesKm['distance_5001_a_20000_km_fixe']);
            } else {
                $remboursement = $distance * floatval($donneesKm['distance_plus_20000_km']);
            }
            return $remboursement;
        }

        // Calculer et afficher le montant remboursé
        $montantRembourse = calculerMontantRembourse(intval($km), 'frais_kilometrique_gouvernement', $cv_fiscal, $dbh);
        $total_refund += $montantRembourse;
        // ajout au remboursement du montant des frais kilométriques

        // Calculer le montant à charge restant
        $total_charge = 0;
        // ce que l'utilisateur va payer au final ou non
        $total_charge = $total_user - $total_refund;

        // Exécuter la requête d'insertion pour "Fiche Forfait" avec le montant à charge restant
        $sql = "INSERT INTO frais (user_id, date_debut, total_night_price, night_quantity, total_meal_price, meal_quantity, km, transport_type, montantRestant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id, $date_debut, $price_night, $number_night, $price_meal, $number_meal, $km, $transport_type, $total_charge]);

        // Téléchargement des justificatifs
        $targetDir = "../../justificatifs/";
    } elseif (isset($_POST['horsForfaitSubmit'])) {
        // Traitement du formulaire "Fiche Hors Forfait"
        $date_debut = date('Y-m-d', strtotime($_POST["date"]));
        $user_id = $_SESSION['user'];
        $number_days = $_POST["number_days"];
        $description = $_POST["description-area"];
        $total_price = $_POST["hors-forfait-prix"];
        $justificatif = isset($_FILES["justificatif"]["name"]) ? $_FILES["justificatif"]["name"] : "";

        // Exécuter la requête d'insertion pour "Fiche Hors Forfait"
        $sql = "INSERT INTO hors_forfait (user_id, description, total_price, justificatif, number_days, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id, $description, $total_price, $justificatif, $number_days, $date_debut]);

        // Téléchargement du justificatif pour "Fiche Hors Forfait"
        $targetDir = "../../justificatifs/";
        move_uploaded_file($_FILES["justificatif"]["tmp_name"], $targetDir . basename($justificatif));
    }

    header("Location: ../postLogin/postLoginView.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un frais</title>
    <link href="../../styles/index.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php include "../navbar/navbarView.php" ?>
    <!-- <input type="button" value="Afficher la modal" onclick="toggleModal()"> -->
    <!-- <div id="modal" class="modal"> -->
    <div class="modal-content">
        <!-- <span class="close" onclick="toggleModal()">&times;</span> -->

        <div id="formSelectorContainer">

            <input type="button" value="Fiche Forfait" onclick="showForfaitForm()">
            <input type="button" value="Fiche Hors Forfait" onclick="showHorsForfaitForm()">
        </div>

        <div id="forfaitFormContainer">
            <h2>Fiche de frais</h2>

            <form id="forfaitForm" method="POST" action="ajouterFraisView.php">
                <label for="date">Date de début:</label>
                <input type="date" id="date" name="date" required><br><br>
                <div class="hebergement">
                    <h2>Hébergements</h2>
                    <p id="nuitInfo">Informations: Nous prenons en charge 50€/nuit maximum</p>
                    <input type="text" name="priceNight" placeholder="Prix total" />
                    <input type="number" name="numberNight" placeholder="Nombre de nuits" />
                </div>
                <div class="repas">
                    <h2>Repas</h2>

                    <p id="repasInfo">Informations: Nous prenons en charge 10€/repas maximum</p>
                    <input type="text" name="priceMeal" placeholder="Prix Total" />
                    <input type="number" name="numberMeal" placeholder="Nombre de repas" />
                </div>
                <div class="trajet">
                    <h2>Trajets</h2>
                    <p id="transportInfo">Informations: Nous prenons en charge 50€/jour maximum</p>
                    <label for="cars">Voitures</label>
                    <input checked type="checkbox" name="cars" />
                    <label for="transports">Transports</label>
                    <input type="checkbox" name="transports" />
                    <div class="cars-container">
                        <p>Frais kilométriques</p>
                        <input type="number" name="km" placeholder="Nombre de KM" />
                        <br />
                    </div>
                    <div class="transports-container" id="transports-container">
                        <label for="transports">Transports</label>
                        <select id="transportType" name="transport">
                            <option value="">-- Choisir --</option>
                            <option value="train">Train</option>
                            <option value="bus">bus</option>
                            <option value="taxi">Taxi</option>
                            <option value="metro&tram">Métro / Tramway</option>
                        </select><br><br>
                        <br />
                    </div>
                </div>
                <input type="submit" value="Envoyer" name="forfaitSubmit">
            </form>
        </div>

        <div id="horsForfaitFormContainer">
            <h2>Fiche Hors Forfait</h2>

            <form id="horsForfaitForm" method="POST" action="ajouterFraisView.php">
                <label for="date">Date de début:</label>
                <input type="date" id="date" name="date" required><br><br>
                <label for="description">Votre hors forfait:</label>
                <textarea rows="5" cols="20" name="description-area"></textarea>
                <label for="totalPrice">Prix total:</label>
                <input type="text" name="hors-forfait-prix" />
                <label for="number_days">Nombre de jours :</label>
                <input type="number" name="number_days" required />
                <label for="justificatif">Justificatif:</label>
                <input type="file" name="justificatif" accept=".pdf" />
                <input type="submit" value="Envoyer" name="horsForfaitSubmit">
            </form>
        </div>
    </div>

    <script>
        function updateFormValues(type, elementId) {
            console.log('toto')
            $.ajax({
                url: 'getDynamicValues.php',
                method: 'GET',
                data: {
                    type: type
                },
                dataType: 'json',
                success: function(response) {
                    $('#' + elementId).html('Informations: Nous prenons en charge ' + response.montant +
                        '€ maximum selon le nombre de jours ou repas');
                },
                error: function(error) {
                    console.error('Erreur lors de la récupération des valeurs :', error);
                }
            });
        }

        document.getElementById('forfaitFormContainer').style.display = 'block';
        document.getElementById('horsForfaitFormContainer').style.display = 'none';
        document.getElementById('transports-container').style.display = 'none'

        updateFormValues('repas', 'repasInfo');
        updateFormValues('nuit', 'nuitInfo');
        updateFormValues('transport', 'transportInfo');

        function showForfaitForm() {
            document.getElementById('forfaitFormContainer').style.display = 'block';
            document.getElementById('horsForfaitFormContainer').style.display = 'none';
            document.getElementById('transports-container').style.display = 'none'
        }

        function showHorsForfaitForm() {
            document.getElementById('forfaitFormContainer').style.display = 'none';
            document.getElementById('horsForfaitFormContainer').style.display = 'block';
        }

        function handleCheckboxClick(clickedCheckbox) {

            // Get the checkbox elements
            var carsCheckbox = document.getElementsByName('cars')[0];
            var transportsCheckbox = document.getElementsByName('transports')[0];

            // Get the container elements
            var carsContainer = document.querySelector('.cars-container');
            var transportsContainer = document.querySelector('.transports-container');

            // Hide both containers initially
            carsContainer.style.display = 'none';
            transportsContainer.style.display = 'none';

            // Determine which checkbox was clicked
            if (clickedCheckbox === carsCheckbox && carsCheckbox.checked) {
                // If "Voitures" is clicked, show the cars container and uncheck the transports checkbox
                carsContainer.style.display = 'block';
                transportsCheckbox.checked = false;
            } else if (clickedCheckbox === transportsCheckbox && transportsCheckbox.checked) {
                // If "Transports" is clicked, show the transports container and uncheck the cars checkbox
                transportsContainer.style.display = 'block';
                carsCheckbox.checked = false;
            }
        }

        // Attach the handleCheckboxClick function to the click events of both checkboxes
        document.getElementsByName('cars')[0].addEventListener('click', function() {
            handleCheckboxClick(document.getElementsByName('cars')[0]);
        });
        document.getElementsByName('transports')[0].addEventListener('click', function() {
            handleCheckboxClick(document.getElementsByName('transports')[0]);
        });
    </script>

</body>

</html>