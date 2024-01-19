<?php
header("Content-Type: application/json");
session_start();
try {
    include('../database.php');


    $hors_forfait_id = $_POST["hors_forfait_id"];
    $description = $_POST["description"];
    $total_price = $_POST["total_price"];
    $justificatif = $_POST["justificatif"];
    $valideComptable_hf = $_POST["valideComptable_hf"];
    $montantRestant_hf = $_POST["montantRestant_hf"];
    $number_days = $_POST["number_days"];
    $pris_en_charge = $_POST["pris_en_charge"];
    $comment_hf = $_POST["comment_hf"];
    $user_id = $_POST["user_id"];

    include("./verify_token.php");
    $currentUserId = $_SESSION['user'];

    verify_token($currentUserId, $dbh);

    $updateHorsForfait = "UPDATE hors_forfait SET description = ?, total_price = ?, justificatif = ?, valideComptable = ?, montantRestant = ?, number_days = ?, pris_en_charge = ?, comment = ? WHERE id = ?";

    $requestUpdateHorsForfait = $dbh->prepare($updateHorsForfait);
    $requestUpdateHorsForfait->execute([$description, $total_price, $justificatif, $valideComptable_hf, $montantRestant_hf, $number_days, $pris_en_charge, $comment_hf, $hors_forfait_id]);


    updateTimeStamp($currentUserId, $dbh);

    
    $json = array("status" => 200, 'message' => "Success");
} catch (PDOException $e) {
    $json = array("status" => 400, 'message' => "Error", 'error' => $e->getMessage());
}

echo json_encode($json);
