<?php
header("Content-Type: application/json");

include('../database.php');

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
    return null; // Fallback if no token is found
}

function updateTimestamp($database, $id) {
    $newTimestamp = $database->prepare("UPDATE visiteur SET token_timestamp = CURRENT_TIMESTAMP WHERE id = :id");
    $newTimestamp->execute(['id' => $id]);
}

$token = extractToken();

try {
    if (!$token) {
        throw new Exception("Token not provided", 400);
    }

    // Validate token and get user data
    $tokenCheck = $dbh->prepare("SELECT id, nom, prenom, numero, adresse, cp, ville, email, cv_car FROM visiteur WHERE token = :token");
    $tokenCheck->execute(['token' => $token]);
    $user = $tokenCheck->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Invalid token", 401);
    }

    $userId = $user['id'];

    // Assuming the token is valid, update the timestamp
    updateTimestamp($dbh, $userId);

    // Fetch frais forfaitisés and hors forfait
    $fraisQuery = $dbh->prepare("SELECT * FROM frais WHERE user_id = :id");
    $fraisQuery->execute(['id' => $userId]);
    $fraisForfait = $fraisQuery->fetchAll(PDO::FETCH_ASSOC);

    $horsForfaitQuery = $dbh->prepare("SELECT * FROM hors_forfait WHERE user_id = :id");
    $horsForfaitQuery->execute(['id' => $userId]);
    $fraisHorsForfait = $horsForfaitQuery->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'status' => 200,
        'message' => "Frais récupérés avec succès",
        'user' => $user, // Directly include user information
        'fraisForfait' => $fraisForfait,
        'fraisHorsForfait' => $fraisHorsForfait
    ];

} catch (Exception $e) {
    $response = ['status' => $e->getCode(), 'message' => $e->getMessage()];
}

echo json_encode($response);
$dbh = null;
?>
