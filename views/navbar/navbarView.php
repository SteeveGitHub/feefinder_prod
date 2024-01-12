<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

$navbarContent = ''; 

if (isset($_SESSION['status'])) {
    if ($_SESSION['status'] === 1) {
        // Visiteur
        $navbarContent = '
            <li><a href="../../controllers/accueil/profileCtrl.php"><i class="fa-solid fa-house-user"></i>Profil</a></li>
            <li><a href="../../controllers/accueil/accueilCtrl.php"><i class="fa-solid fa-house-user"></i>Consulter</a></li>
            <li><a href="../../controllers/accueil/fichefraisCtrl.php"><i class="fa-solid fa-euro-sign"></i>Ajouter</a></li>
            <li><a href="../../controllers/accueil/contactCtrl.php"><i class="fa-solid fa-address-book"></i>Contact</a></li>
            <li><a href="../connexion/loginView.php"><i class="fa-solid fa-sign-in"></i>Deconnexion</a></li>';
    } else if ($_SESSION['status'] === 2) {
        // Admin
        $navbarContent = '
            <li><a href="../../controllers/accueil/profileCtrl.php"><i class="fa-solid fa-house-user"></i>Profil</a></li>
            <li><a href="../admin/adminView.php"><i class="fa-solid fa-users"></i>Employés</a></li>
            <li><a href="../connexion/loginView.php"><i class="fa-solid fa-sign-in"></i>Deconnexion</a></li>';
    } else if ($_SESSION['status'] === 3) {
        // Comptable
        $navbarContent = '
            <li><a href="../../controllers/accueil/profileCtrl.php"><i class="fa-solid fa-house-user"></i>Profil</a></li>
            <li><a href="../../views/comptable/comptableView.php"><i class="fa-solid fa-euro-sign"></i>Validation</a></li>
            <li><a href="../../views/comptable/dashboardView.php"><i class="fa-solid fa-user"></i>Dashboard</a></li>
            <li><a href="../../controllers/accueil/contactCtrl.php"><i class="fa-solid fa-address-book"></i>Contact</a></li>
            <li><a href="../connexion/loginView.php"><i class="fa-solid fa-sign-in"></i>Deconnexion</a></li>';
    
        }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../../styles/index.css">
</head>

<body>
    <div class="burger-container">
        <img id="burger-icon" src="../../assets/images/hamburger.png" alt="Burger Icon" onclick="toggleBurger()">
        <nav class="<?= $classname ?>">
            <ul class="nav-links">
                <a href="#" class="logo">
                    <img class="logo-img" src="../../assets/images/LogoFeeFinder.png" alt="feefinderlogo">
                </a>
                <?= $navbarContent ?>
                <div class="active"></div>
            </ul>
        </nav>
    </div>

    <script>
        const nav = document.querySelector('nav');
        nav.className = "navbar-closed";
        let classname = "";
        let isOpen = false;

        function toggleBurger() {
            if (isOpen) {
                isOpen = false;
                classname = "navbar-closed";
            } else {
                isOpen = true;
                classname = "navbar-open";
            }
            nav.className = classname;
        }
    </script>
</body>

</html>
