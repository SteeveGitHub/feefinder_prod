<?php
include('../../database.php');

$table = $_POST['table'];
$id = $_POST['id'];
$value = $_POST['value'];
$comment = $_POST['comment'];
$prisEnCharge = is_numeric($_POST['prisEnCharge']) ? $_POST['prisEnCharge'] : 0;

$sql = "";
$params = [];

if ($table === "frais") {
    $sql = "UPDATE $table SET valideComptable = ?, comment = ? WHERE id = ?";
    $params = [$value, $comment, $id];
} else {
    $createdDate = new DateTime($horsForfait["created_at"]);
    $difference = $currentDate->diff($createdDate);

    $sql = "UPDATE $table SET valideComptable = ?, comment = ?, pris_en_charge = ? WHERE id = ?";
    $params = [$value, $comment, $prisEnCharge, $id];
}

$requete = $dbh->prepare($sql);
$requete->execute($params);
