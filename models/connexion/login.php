<?php
session_start();

if (isset($_POST['email']) && isset($_POST['password'])) {
    include('../../database.php');
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // A RAJOUTER : CRYPTAGE DU MDP ENVOYE
    $requete = $dbh->prepare("SELECT id, status, mdp FROM visiteur where email = ?");
    $result = $requete->execute([$email]);
    $row = $requete->fetch();

    if ($row && password_verify($password, $row["mdp"])) {
        if($row["status"] === 4){
            header('Location: ../../views/connexion/loginView.php?error=1');
            exit();
        }

        $idUser = $row["id"];
        $status = $row["status"];
        $_SESSION['user'] = $idUser;
        $_SESSION['status'] = $status;
        header('Location: ../../index.php');
    } else {
        header('Location: ../../views/connexion/loginView.php?error=2');
    }
}
