<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérez les données du formulaire
    $date = $_POST['date'];
    $employe = $_POST['employe'];
    $montant = $_POST['montant'];

    // Placez ici la logique pour ajouter ces données dans la base de données

    // Redirigez l'utilisateur vers la page de liste des frais après l'ajout
    header('Location: comptable.php');
}
?>
