<?php
 include('./database.php');

// Étape 2 : Vérification de la réception des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous que les données du formulaire sont bien présentes et valides
    $champ1 = $_POST["champ1"]; // Remplacez par le nom de votre champ 1
    $champ2 = $_POST["champ2"]; // Remplacez par le nom de votre champ 2

    // Étape 3 : Insertion dans la table
    $requete = "INSERT INTO nom_de_votre_table (champ1, champ2) VALUES ('$champ1', '$champ2')";

    if (mysqli_query($connexion, $requete)) {
        // Étape 4 : Vérification du succès de l'insertion
        $nombreDeLignesAffectees = mysqli_affected_rows($connexion);

        if ($nombreDeLignesAffectees > 0) {
            // Redirection vers comptable.php
            header("Location: comptable.php");
            exit();
        } else {
            echo "Erreur : Aucune ligne n'a été insérée dans la base de données.";
        }
    } else {
        echo "Erreur lors de l'insertion dans la base de données : " . mysqli_error($connexion);
    }
}

// Fermer la connexion à la base de données
mysqli_close($connexion);
?>
