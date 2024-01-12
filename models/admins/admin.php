<?php
include('../../database.php');

$requete = $dbh->prepare("SELECT id, nom, prenom, numero, cv_car, status FROM visiteur");
$requete->execute();
$row = $requete->fetchAll(PDO::FETCH_ASSOC);
$requete->closeCursor();
