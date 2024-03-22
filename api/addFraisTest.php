<?php
require_once("../database.php");

function extractToken() {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return preg_replace('/^Bearer\s/', '', $_SERVER['HTTP_AUTHORIZATION']);
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        return preg_replace('/^Bearer\s/', '', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    } else {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        if (isset($headers['Authorization'])) {
            return preg_replace('/^Bearer\s/', '', $headers['Authorization']);
        }
    }
    return null;
}

function verifyTokenAndGetUserId($dbh, $token) {
    if (!$token) {
        throw new Exception("Token not provided", 400);
    }

    $tokenCheck = $dbh->prepare("SELECT id FROM visiteur WHERE token = :token");
    $tokenCheck->execute(['token' => $token]);
    $userId = $tokenCheck->fetchColumn();

    if (!$userId) {
        throw new Exception("Invalid token", 401);
    }

    return $userId;
}

function updateTimestamp($dbh, $userId) {
    $newTimestamp = $dbh->prepare("UPDATE visiteur SET token_timestamp = CURRENT_TIMESTAMP WHERE id = :id");
    $newTimestamp->execute(['id' => $userId]);
}


header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $token = extractToken();
    $user_id = verifyTokenAndGetUserId($dbh, $token); // Récupère l'ID utilisateur basé sur le token
    updateTimestamp($dbh, $user_id); // Met à jour le timestamp du token
  
    // Extraction et validation des données reçues de Postman
    $date_debut = isset($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : null;
    $price_night = $_POST['priceNight'] ?? 0;
    $number_night = $_POST['numberNight'] ?? 0;
    $price_meal = $_POST['priceMeal'] ?? 0;
    $number_meal = $_POST['numberMeal'] ?? 0;
    $km = $_POST['km'] ?? 0;
    $transport_type = $_POST['transport'] ?? '';

    // Récupération des montants fixes depuis la table "fraisforfait"
    $sqlSelect = "SELECT id, montant FROM fraisforfait";
    $stmtSelect = $dbh->prepare($sqlSelect);
    $stmtSelect->execute();
    $fraisForfaits = $stmtSelect->fetchAll(PDO::FETCH_KEY_PAIR);

    // Calcul des montants à rembourser pour les nuits et les repas
    $montant_night = $fraisForfaits[1] * $number_night;
    $montant_meal = $fraisForfaits[2] * $number_meal;

    // Calcul du montant à rembourser pour les kilomètres
    // Supposons que vous ayez une logique similaire pour calculer ce montant
    $cv_fiscal = $_POST['cv_fiscal'] ?? 0; // Supposons que cette info soit envoyée depuis Postman
    $sqlKm = "SELECT * FROM frais_kilometrique_gouvernement WHERE puissance_administrative = ?";
    $stmtKm = $dbh->prepare($sqlKm);
    $stmtKm->execute([$cv_fiscal]);
    $tarifsKm = $stmtKm->fetch(PDO::FETCH_ASSOC);

    $montant_km = 0;
    if ($km <= 5000) {
        $montant_km = $km * $tarifsKm['distance_jusqu_5000_km'];
    } elseif ($km <= 20000) {
        $montant_km = $km * $tarifsKm['distance_5001_a_20000_km_coefficient'] + $tarifsKm['distance_5001_a_20000_km_fixe'];
    } else {
        $montant_km = $km * $tarifsKm['distance_plus_20000_km'];
    }

    // Calcul du montant total à charge restant
    $montant_total = $montant_night + $montant_meal + $montant_km;

    // Insérer les calculs dans la base de données
    $sqlInsert = "INSERT INTO frais (user_id, date_debut, total_night_price, night_quantity, total_meal_price, meal_quantity, km, transport_type, montantRestant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute([$user_id, $date_debut, $price_night, $number_night, $price_meal, $number_meal, $km, $transport_type, $montant_total]);

    echo json_encode(["status" => "success", "message" => "Fiche de frais ajoutée avec succès"]);
} else {
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
}
?>
