<?php
try {
    $dbh = new PDO('mysql:host=mysql-trincal.alwaysdata.net;dbname=trincal_feefinder', "trincal", "StevSand2");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("erreur :" . $e);
}
