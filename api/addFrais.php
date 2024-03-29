<?php
header("Content-Type: application/json");
session_start();

try {
    include('../database.php');

    include("./verify_token.php");

    $token = extractToken();

    $user_id = $_POST["user_id"];
    $date_debut = $_POST["date_debut"];
    $total_night_price = $_POST["total_night_price"];
    $night_quantity = $_POST["night_quantity"];
    $total_meal_price = $_POST["total_meal_price"];
    $meal_quantity = $_POST["meal_quantity"];
    $km = $_POST["km"];
    $transport_type = $_POST["transport_type"];
    // $valideComptable = $_POST["valideComptable"];
    $montantRestant = $_POST["montantRestant"];
    // $comment = $_POST["comment"];

    $currentUserId = $_SESSION['user'];

    verify_token($currentUserId, $dbh);

    $insertFrais = "INSERT INTO frais (user_id, date_debut, total_night_price, night_quantity, total_meal_price, meal_quantity, km, transport_type, montantRestant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $requestFrais = $dbh->prepare($insertFrais);
    $requestFrais->execute([$user_id, $date_debut, $total_night_price, $night_quantity, $total_meal_price, $meal_quantity, $km, $transport_type, $montantRestant]);

    updateTimeStamp($currentUserId, $dbh);


    $json = array("status" => 200, 'message' => "Success");
} catch (PDOException $e) {
    $json = array("status" => 400, 'message' => "Error", 'error' => $e->getMessage());
}

echo json_encode($json);
