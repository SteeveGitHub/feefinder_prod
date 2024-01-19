<?php
header("Content-Type: application/json");
session_start();

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
    
    return $_POST["token"] ?? null;
}

function updateTimestamp($database, $id){
    $newTimestamp = $database->prepare("UPDATE visiteur SET token_timestamp = CURRENT_TIMESTAMP WHERE id = :id");
    $newTimestamp->execute(['id' => $id]);
}

try {
    $currentUser = $_SESSION['user'];
    $token = extractToken();
    $userId = $_POST['userId'] ?? null;

    if (!$userId || !$token) {
        throw new Exception("User ID or token not provided", 400);
    }

    $tokenCheck = $dbh->prepare("SELECT token, token_timestamp FROM visiteur WHERE id = :id");
    $tokenCheck->execute(['id' => $currentUser]);
    $tokenData = $tokenCheck->fetch();

    $json = json_encode(['status' => 401, 'message' => 'Invalid token']);

    if ($tokenData && $tokenData['token'] === $token) {

        $currentTimestamp = time();
        $tokenAge = $currentTimestamp - strtotime($tokenData['token_timestamp']);

        if ($tokenAge <= 3600) {  // 1 heure

            // Récupération des frais forfaitisés
            $fraisQuery = $dbh->prepare("SELECT * FROM frais WHERE user_id = :id");
            $fraisQuery->execute(['id' => $userId]);
            $fraisForfait = $fraisQuery->fetchAll(PDO::FETCH_ASSOC);

            // Récupération des frais hors forfait
            $horsForfaitQuery = $dbh->prepare("SELECT * FROM hors_forfait WHERE user_id = :id");
            $horsForfaitQuery->execute(['id' => $userId]);
            $fraisHorsForfait = $horsForfaitQuery->fetchAll(PDO::FETCH_ASSOC);

            updateTimestamp($dbh, $userId);

            $json = json_encode([
                'status' => 200, 
                'message' => "Frais récupérés avec succès", 
                'fraisForfait' => $fraisForfait,
                'fraisHorsForfait' => $fraisHorsForfait
            ]);
        } else {
            $json = json_encode(['status' => 401, 'message' => 'Token expired']);
        }
    } else {
        // $json = json_encode(['status' => 401, 'message' => 'Invalid token']);
    }
} catch (Exception $e) {
    $json = json_encode(['status' => $e->getCode(), 'message' => $e->getMessage()]);
}

echo $json;

$dbh = null;
?>
