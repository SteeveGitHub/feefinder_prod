<?php
session_start();
include '../../database.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../connexion/loginView.php');
    exit();
}

$ficheId = $_GET['id'];
$table = $_GET['table'];

$query = $dbh->prepare("SELECT * FROM $table WHERE id = :ficheId");
$query->bindParam(':ficheId', $ficheId, PDO::PARAM_INT);
$query->execute();

$fiche = $query->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails et Modification de la Fiche</title>
    <link href="../../styles/index.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php
    include '../navbar/navbarView.php';
    ?>
    <?php if ($table === 'frais') : ?>
        <div id="forfaitFormContainer">
            <h1>Informations pour la fiche "Frais"</h1>

            <h2>Fiche de frais</h2>

            <form id="forfaitForm" method="POST" action="updateFiche.php">
                <input type="hidden" name="table" value="<?php echo $table; ?>">
                <input type="hidden" name="id" value="<?php echo $fiche['id']; ?>">

                <label class="label-element" for="date">Date de début:</label>
                <input class="input-element" type="date" id="date" name="date" required value="<?php echo date('Y-m-d', strtotime($fiche['date_debut'])); ?>"><br><br>

                <div class="hebergement">
                    <h2>Hébergements</h2>
                    <p id="nuitInfo">Informations: Nous prenons en charge 50€/nuit maximum</p>
                    <input class="input-element" type="text" name="priceNight" placeholder="Prix total" value="<?php echo $fiche['total_night_price']; ?>" />
                    <input class="input-element" type="number" name="numberNight" placeholder="Nombre de nuits" value="<?php echo $fiche['night_quantity']; ?>" />
                </div>

                <div class="repas">
                    <h2>Repas</h2>
                    <p id="repasInfo">Informations: Nous prenons en charge 10€/repas maximum</p>
                    <input class="input-element" type="text" name="priceMeal" placeholder="Prix Total" value="<?php echo $fiche['total_meal_price']; ?>" />
                    <input class="input-element" type="number" name="numberMeal" placeholder="Nombre de repas" value="<?php echo $fiche['meal_quantity']; ?>" />
                </div>

                <div class="trajet">
                    <h2>Trajets</h2>
                    <p id="transportInfo">Informations: Nous prenons en charge 50€/jour maximum</p>
                    <label class="label-element" for="cars">Voitures</label>
                    <input type="checkbox" name="cars" <?php echo $fiche['transport_type'] === 'voiture' ? 'checked' : ''; ?> />
                    <label class="label-element" for="transports">Transports</label>
                    <input type="checkbox" name="transports" <?php echo $fiche['transport_type'] === 'transport' ? 'checked' : ''; ?> />

                    <div class="cars-container" style="<?php echo $fiche['transport_type'] === 'voiture' ? 'display: block;' : 'display: none;'; ?>">
                        <p>Nombre de kilomètres</p>
                        <input class="input-element" type="number" name="km" placeholder="Nombre de KM" value="<?php echo $fiche['km']; ?>" />
                        <br />
                    </div>

                    <div class="transports-container" id="transports-container" style="<?php echo $fiche['transport_type'] === 'transport' ? 'display: block;' : 'display: none;'; ?>">
                        <label class="label-element" for="transportType">Transports</label>
                        <select id="transportType" name="transport">
                            <option value="">-- Choisir --</option>
                            <option value="train" <?php echo $fiche['transport_type'] === 'train' ? 'selected' : ''; ?>>Train</option>
                            <option value="bus" <?php echo $fiche['transport_type'] === 'bus' ? 'selected' : ''; ?>>Bus</option>
                            <option value="taxi" <?php echo $fiche['transport_type'] === 'taxi' ? 'selected' : ''; ?>>Taxi</option>
                            <option value="metro&tram" <?php echo $fiche['transport_type'] === 'metro&tram' ? 'selected' : ''; ?>>Métro / Tramway</option>
                        </select><br><br>
                        <br />
                    </div>
                </div>

                <input type="submit" value="Envoyer" name="forfaitSubmit">
            </form>

        </div>

    <?php elseif ($table === 'hors_forfait') : ?>
        <div id="horsForfaitFormContainer" style="display: block">
            <h2>Fiche Hors Forfait</h2>

            <form id="horsForfaitForm" method="POST" action="updateFiche.php">
                <input type="hidden" name="table" value="<?php echo $table; ?>">
                <input type="hidden" name="id" value="<?php echo $fiche['id']; ?>">

                <label class="label-element" for="description">Votre hors forfait:</label>
                <textarea class="input-element" rows="5" cols="20" name="description-area"><?php echo $fiche['description']; ?></textarea>

                <label class="label-element" for="totalPrice">Prix total:</label>
                <input class="input-element" type="text" name="hors-forfait-prix" value="<?php echo $fiche['total_price']; ?>" />

                <label class="label-element" for="number_days">Nombre de jours :</label>
                <input class="input-element" type="number" name="number_days" required value="<?php echo $fiche['number_days']; ?>" />

                <label class="label-element" for="justificatif">Justificatif:</label>
                <input type="file" name="justificatif" accept=".pdf" />

                <input type="submit" value="Envoyer" name="horsForfaitSubmit">
            </form>

        </div>
    <?php endif; ?>

    <script>
        function handleCheckboxClick(clickedCheckbox) {
            // Get the checkbox elements
            var carsCheckbox = document.getElementsByName('cars')[0];
            var transportsCheckbox = document.getElementsByName('transports')[0];

            // Get the container elements
            var carsContainer = document.querySelector('.cars-container');
            var transportsContainer = document.querySelector('.transports-container');

            // Check if the containers exist before accessing their properties
            if (carsContainer && transportsContainer) {
                // Hide both containers initially
                carsContainer.style.display = 'none';
                transportsContainer.style.display = 'none';

                // Determine which checkbox was clicked
                if (clickedCheckbox === carsCheckbox && carsCheckbox.checked) {
                    // If "Voitures" is clicked, show the cars container and hide the transports container
                    carsContainer.style.display = 'block';
                    transportsContainer.style.display = 'none';
                } else if (clickedCheckbox === transportsCheckbox && transportsCheckbox.checked) {
                    // If "Transports" is clicked, show the transports container and hide the cars container
                    transportsContainer.style.display = 'block';
                    carsContainer.style.display = 'none';
                }
            }
        }

        // Attach the handleCheckboxClick function to the click events of both checkboxes
        document.getElementsByName('cars')[0].addEventListener('click', function() {
            handleCheckboxClick(document.getElementsByName('cars')[0]);
        });
        document.getElementsByName('transports')[0].addEventListener('click', function() {
            handleCheckboxClick(document.getElementsByName('transports')[0]);
        });

        function updateFormValues(type, elementId) {
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
        updateFormValues('repas', 'repasInfo');
        updateFormValues('nuit', 'nuitInfo');
        updateFormValues('transport', 'transportInfo');
        document.getElementById('forfaitFormContainer').style.display = 'block';
        document.getElementById('horsForfaitFormContainer').style.display = 'block';
    </script>

</body>

</html>