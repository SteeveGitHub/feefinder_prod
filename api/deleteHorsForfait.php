<?php
header("Content-Type: application/json");
session_start();
try {
    include('../database.php');

    $hors_forfait_id = $_POST["hors_forfait_id"];
    $user_id = $_POST["user_id"];

    include("./verify_token.php");
    $currentUserId = $_SESSION['user'];

    verify_token($currentUserId, $dbh);

    $deleteHorsForfait = "DELETE FROM hors_forfait WHERE id = ? and user_id = ?";

    $requestDeleteHorsForfait = $dbh->prepare($deleteHorsForfait);
    $requestDeleteHorsForfait->execute([$hors_forfait_id, $user_id]);

    updateTimeStamp($currentUserId, $dbh);

    $json = array("status" => 200, 'message' => "Success");
} catch (PDOException $e) {
    $json = array("status" => 400, 'message' => "Error", 'error' => $e->getMessage());
}

echo json_encode($json);
