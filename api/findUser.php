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
    $token = extractToken();
    $userId = $_POST['userId'] ?? null;

    if (!$userId || !$token) {
        throw new Exception("User ID or token not provided", 400);
    }

    // Token validation
    $tokenCheck = $dbh->prepare("SELECT token, token_timestamp FROM visiteur WHERE id = :id");
    $tokenCheck->execute(['id' => $userId]);
    $tokenData = $tokenCheck->fetch();

    if ($tokenData && $tokenData['token'] === $token) {
        $currentTimestamp = time();
        $tokenAge = $currentTimestamp - strtotime($tokenData['token_timestamp']);

        if ($tokenAge <= 3600) {  // 1HOUR
           
            $userQuery = $dbh->prepare("SELECT id, numero, adresse, cp, email, cv_car FROM visiteur WHERE id = :id");
            $userQuery->execute(['id' => $userId]);
            $user = $userQuery->fetch();

            updateTimestamp($dbh, $userId);

            if ($user) {
                $json = json_encode(['status' => 200, 'message' => "User found", 'user' => $user]);
            } else {
                $json = json_encode(['status' => 404, 'message' => "User not found"]);
            }
        } else {
            $json = json_encode(['status' => 401, 'message' => 'Token expired']);
        }
    } else {
        $json = json_encode(['status' => 401, 'message' => 'Invalid token']);
    }
} catch (Exception $e) {
    $json = json_encode(['status' => $e->getCode(), 'message' => $e->getMessage()]);
}

echo $json;

$dbh = null;
?>