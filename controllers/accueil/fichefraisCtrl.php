<?php
session_start();

if (isset($_SESSION['status'])) {
    
    header('Location: ../../views/frais/ajouterFraisView.php');

} else {
    header('Location: ../verifUserSessionCtrl.php');
}
