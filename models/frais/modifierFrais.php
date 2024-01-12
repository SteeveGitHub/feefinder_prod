<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Récupérez l'identifiant de la fiche de frais à modifier
    $id = $_GET['id'];
    
    // Placez ici la logique pour récupérer les données de la fiche de frais à partir de la base de données
    // Remplissez le formulaire avec les données actuelles

    // Affichez le formulaire de modification
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Modifier Frais</title>
    </head>
    <body>
    <h1>Modifier Frais</h1>
    
    <form method="post" action="modifierFrais.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <!-- Les champs du formulaire (date, employé, montant) avec les valeurs actuelles -->
        
        <input type="submit" value="Enregistrer les modifications">
    </form>
    
    </body>
    </html>
    <?php
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Traitez ici la soumission du formulaire de modification
    // Récupérez les données du formulaire et mettez à jour la base de données

    // Redirigez l'utilisateur vers la page de liste des frais après la modification
    header('Location: comptable.php');
}
?>
