<?php
include('../../database.php');

$sql = "UPDATE frais SET valideComptable = 1 WHERE valideComptable = 0";

$requete = $dbh->prepare($sql);
$requete->execute();
