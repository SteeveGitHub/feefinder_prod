<?php
header("Content-Type: application/json");
session_start();
try {
    include('../database.php');


    $frais_id = $_POST["frais_id"];
    $total_night_price = $_POST["total_night_price"];
    $night_quantity = $_POST["night_quantity"];
    $total_meal_price = $_POST["total_meal_price"];
    $meal_quantity = $_POST["meal_quantity"];
    $km = $_POST["km"];
    $transport_type = $_POST["transport_type"];
    $valideComptable = $_POST["valideComptable"];
    $montantRestant = $_POST["montantRestant"];
    $comment = $_POST["comment"];

    $user_id = $_POST["user_id"];

    include("./verify_token.php");

    $currentUserId = $_SESSION['user'];

    verify_token($currentUserId, $dbh);

    $updateFrais = "UPDATE frais SET total_night_price = ?, night_quantity = ?, total_meal_price = ?, meal_quantity = ?, km = ?, transport_type = ?, valideComptable = ?, montantRestant = ?, comment = ? WHERE id = ?";
    $requestUpdateFrais = $dbh->prepare($updateFrais);
    $requestUpdateFrais->execute([$total_night_price, $night_quantity, $total_meal_price, $meal_quantity, $km, $transport_type, $valideComptable, $montantRestant, $comment, $frais_id]);

    updateTimeStamp($currentUserId, $dbh);

    $json = array("status" => 200, 'message' => "Success");
} catch (PDOException $e) {
    $json = array("status" => 400, 'message' => "Error", 'error' => $e->getMessage());
}

echo json_encode($json);
