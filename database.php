<?php
try {
    $dbh = new PDO('mysql:host=mysql-trincal.alwaysdata.net;dbname=trincal_feefinder', "trincal", "StevSand2");
} catch (PDOException $e) {
    die("erreur :" . $e);
}
