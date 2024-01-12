<?php
session_start();

if (isset($_POST['login']) && isset($_POST['name']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST["phone"]) && isset($_POST['adress']) && isset($_POST['postal']) && isset($_POST['city']) && isset($_POST['password'])) {
    try {
        include('../../database.php');
        $login = strip_tags($_POST['login']);
        $name = strip_tags($_POST['name']);
        $prenom = strip_tags($_POST['prenom']);
        $email = strip_tags($_POST['email']);
        $phone = strip_tags($_POST['phone']);
        $adress = strip_tags($_POST['adress']);
        $postal = strip_tags($_POST['postal']);
        $city = strip_tags($_POST['city']);
        $passwordHash = password_hash(strip_tags($_POST['password']), PASSWORD_DEFAULT);
        $status = 1;        

        $checkPasswordQuery = $dbh->prepare("SELECT COUNT(1) FROM visiteur WHERE mdp = ?");
        $checkPasswordQuery->execute([$passwordHash]);
        $resultPassword = $checkPasswordQuery->fetch();
        if ($resultPassword[0] != 0) {
            echo "<script>alert('Mot de passse déjà utilsé');
                window.location.href='../../views/inscription/registerView.php';
            </sc>";
        }

        $checkLoginQuery = "SELECT COUNT(1) FROM visiteur WHERE login = ?";
        $checkLoginQuery = $dbh->prepare("SELECT COUNT(1) FROM visiteur WHERE login = ?");
        $checkLoginQuery->execute([$login]);
        $resultLogin = $checkLoginQuery->fetch();
        if ($resultLogin[0] != 0) {
            echo "<script>alert('login déjà utilsé');
            window.location.href='../../views/inscription/registerView.php';
        </script>";
        }

        $checkEmailQuery = "SELECT COUNT(1) FROM visiteur WHERE email = ?";
        $checkEmailQuery = $dbh->prepare("SELECT COUNT(1) FROM visiteur WHERE email = ?");
        $checkEmailQuery->execute([$email]);
        $resultEmail = $checkEmailQuery->fetch();
        if ($resultEmail[0] != 0) {
            echo "<script>alert('email déjà utilsé');
            window.location.href='../../views/inscription/registerView.php';
        </script>";
        }

        // Utilise des marqueurs de paramètres dans la requête préparée
        $requete = $dbh->prepare("INSERT INTO visiteur (nom, prenom, numero, login, mdp, adresse, cp, ville, email, status) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $requete->execute([$name, $prenom, $phone, $login, $passwordHash, $adress, $postal, $city, $email, $status]);

        // Lie les valeurs aux marqueurs de paramètres

        header('Location: ../../views/connexion/loginView.php?success=1');
        exit();
    } catch (PDOException $e) {
        echo 'erreur' . $e;
    }
}
