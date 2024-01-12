<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /views/connexion/loginView.php');
    exit;
} else {
    header('Location: /views/postLogin/postLoginView.php');
    exit;
}
