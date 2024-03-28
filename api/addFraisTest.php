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

if (($_SERVER['REQUEST_METHOD'] === 'POST') &&
    isset($_POST["date"]) &&
    isset($_POST["priceNight"]) &&
    isset($_POST["numberNight"]) &&
    isset($_POST["priceMeal"]) &&
    isset($_POST["numberMeal"]) &&
    isset($_POST["km"]) &&
    isset($_POST["transport"])) { 

    $token = extractToken();

    if($token != null){
    $user_id = verifyTokenAndGetUserId($dbh, $token); // Récupère l'ID utilisateur basé sur le token
    updateTimestamp($dbh, $user_id); // Met à jour le timestamp du token

    // params nécessaires à fournir sur PostMan : 
    // date, priceNight, numberNight, priceMeal, numberMeal, km, transport

    $date_debut = date('Y-m-d', strtotime($_POST["date"]));
    $price_night = $_POST["priceNight"] ?: 0;
    $number_night = $_POST["numberNight"] ?: 0;
    $price_meal = $_POST["priceMeal"] ?: 0;
    $number_meal = $_POST["numberMeal"] ?: 0;
    $km = $_POST["km"] ?: 0;
    $transport_type = $_POST["transport"];
    
    
        // $total_nuit_user = ($price_night * $number_night)/$number_night;
        $total_nuit_user = ($number_night != 0) ? $price_night : 0;
        $total_nuit_user = intval($total_nuit_user);

        // Traitement des repas
        $total_meal_user = ($number_meal != 0) ? $price_meal : 0;
        $total_meal_user = intval($total_meal_user);

        $total_user = $total_nuit_user + $total_meal_user;
        $total_user = intval($total_user);

        // Récupération des montants depuis la table "fraisforfait"
        $sqlSelect = "SELECT montant FROM fraisforfait WHERE id = ?";
        $stmtSelect = $dbh->prepare($sqlSelect);
        $id_night = 1;
        $id_meal = 2;
        $id_transport = 3;

        // Calcul du montant à charge restant pour chaque type de frais
        $stmtSelect->execute([$id_night]);
        $montant_night = $stmtSelect->fetchColumn();

        $stmtSelect->execute([$id_meal]);
        $montant_meal = $stmtSelect->fetchColumn();

        // $stmtSelect->execute([$id_transport]);
        // $montant_transport = $stmtSelect->fetchColumn();

        $refund_night = intval($number_night) * intval($montant_night);
        $refund_meal = intval($number_meal) * intval($montant_meal);
        // $refund_transport = $km * $montant_transport;

        // Calcul du montant total à charge restant
        $total_refund = intval($refund_night) + intval($refund_meal);
        // $total_refund = intval($total_refund);

        if ($total_user < $total_refund) {
            // si ce que paie l'utilisteur est inférieur au montant remboursé,
            // on rembourse ce que l'utilisateur a payé
            $total_refund = $total_user;
        }

        $sqlSelectCV = "SELECT cv_car FROM visiteur WHERE id = ?";
        $stmtSelectCV = $dbh->prepare($sqlSelectCV);
        $stmtSelectCV->execute([$user_id]);
        $cv_fiscal = $stmtSelectCV->fetchColumn();

        function calculerMontantRembourse($distance, $table, $puissance, $dbhvar)
        //calcul du remboursement en fonction des frais kilométriques
        {
            $remboursement = 0;
            $sqlSelectKm = "SELECT distance_jusqu_5000_km, distance_5001_a_20000_km_coefficient, distance_5001_a_20000_km_fixe, distance_plus_20000_km FROM frais_kilometrique_gouvernement WHERE puissance_administrative = ?";
            $stmtSelectKm = $dbhvar->prepare($sqlSelectKm);
            $stmtSelectKm->execute([$puissance]);
            $donneesKm = $stmtSelectKm->fetch(PDO::FETCH_ASSOC);

            // Calculer le montant remboursé en fonction de la distance
            if ($distance <= 5000) {
                $remboursement = $distance * floatval($donneesKm['distance_jusqu_5000_km']);
            } elseif ($distance <= 20000) {
                $remboursement = $distance * floatval($donneesKm['distance_5001_a_20000_km_coefficient']) + intval($donneesKm['distance_5001_a_20000_km_fixe']);
            } else {
                $remboursement = $distance * floatval($donneesKm['distance_plus_20000_km']);
            }
            return $remboursement;
        }

        // Calculer et afficher le montant remboursé
        $montantRembourse = calculerMontantRembourse(intval($km), 'frais_kilometrique_gouvernement', $cv_fiscal, $dbh);
        $total_refund += $montantRembourse;
        // ajout au remboursement du montant des frais kilométriques

        // Calculer le montant à charge restant
        $total_charge = 0;
        // ce que l'utilisateur va payer au final ou non
        $total_charge = $total_user - $total_refund;

        // Exécuter la requête d'insertion pour "Fiche Forfait" avec le montant à charge restant
        $sql = "INSERT INTO frais (user_id, date_debut, total_night_price, night_quantity, total_meal_price, meal_quantity, km, transport_type, montantRestant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id, $date_debut, $price_night, $number_night, $price_meal, $number_meal, $km, $transport_type, $total_charge]);

        // Téléchargement des justificatifs
        $targetDir = "../../justificatifs/";
    }else{
        echo json_encode(["status" => "error", "message" => "Token not provided"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
}

?>
