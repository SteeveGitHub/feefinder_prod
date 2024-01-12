<?php
session_start();

if (isset($_SESSION['status'])) {
    header('Location: ../../views/frais/consulterFraisView.php');
} else {
    header('Location: controllers/verifUserSessionCtrl.php');
}
