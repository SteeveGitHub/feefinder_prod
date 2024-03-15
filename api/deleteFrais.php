<?php
header("Content-Type: application/json");
session_start();

try {
    include('../database.php');

    $frais_id = $_POST["frais_id"];
    $user_id = $_POST["user_id"];

    if(isset($_POST["frais_id"]) && isset($_POST["user_id"])){

    include("./verify_token.php");
    $currentUserId = $_SESSION['user'];

    verify_token($currentUserId, $dbh);

    $deleteFrais = "DELETE FROM frais WHERE id = ? AND user_id = ?";

    $requestDeleteFrais = $dbh->prepare($deleteFrais);
    $requestDeleteFrais->execute([$frais_id, $user_id]);

    updateTimeStamp($currentUserId, $dbh);

    $json = array("status" => 200, 'message' => "Success");
    } else {
        $json = array("status" => 400, 'message' => "Error", 'error' => "Missing parameters");
    }
} catch (PDOException $e) {
    $json = array("status" => 400, 'message' => "Error", 'error' => $e->getMessage());
}

echo json_encode($json);
