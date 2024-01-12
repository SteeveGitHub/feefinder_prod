<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Vérifiez l'autorisation de l'utilisateur à supprimer la fiche de frais (ajoutez votre propre logique d'autorisation)
    $userIsAuthorized = true;  // Par exemple, vérifiez si l'utilisateur a le droit de supprimer cette fiche

    if ($userIsAuthorized) {
        // Récupérez l'identifiant de la fiche de frais à supprimer
        $id = $_GET['id'];
;

        include('./database.php');

        // if ($dbh->connect_error) {
        //     die("Erreur de connexion à la base de données : " . $conn->connect_error);
        // }

        // Utilisez une requête préparée pour supprimer la fiche de frais
        $requete = $dbh->prepare("INSERT INTO fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat) VALUES (?,?, ?, ?, ?, ?, ?)");

        $requete->execute([])
            // La fiche de frais a été supprimée avec succès
            $requete->close();
            $conn->close();
            header('Location: comptable.php');  // Redirigez l'utilisateur vers la page de liste des frais
        } else {
            echo "Erreur lors de la suppression de la fiche de frais : " . $conn->error;
        }
    } else {
        // L'utilisateur n'est pas autorisé à supprimer la fiche de frais
        echo "Vous n'êtes pas autorisé à supprimer cette fiche de frais.";
    }

?>
