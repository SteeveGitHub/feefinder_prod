<?php
header("Content-Type: application/json");

function extractToken()
{
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

function verify_token($id, $database)
{
    $token = extractToken();

    if (!$token) {
        throw new Exception("User ID or token not provided", 400);
    }

    $requete = "SELECT * FROM visiteur WHERE id = ? and token = ?";
    $sql = $database->prepare($requete);
    $sql->execute([$id, $token]);
    $utilisateur = $sql->fetch(PDO::FETCH_ASSOC);


    if ($utilisateur && $utilisateur['token'] == $token) {
        $currentTimestamp = time();
        $tokenAge = $currentTimestamp - strtotime($utilisateur['token_timestamp']);
        $time = 7200;  // 1HOUR
        if ($tokenAge > $time) {
            throw new Exception("Token expired", 401);
        }
    } else {
        throw new Exception("User ID or token not provided", 400);
    }
}

function updateTimeStamp($id, $database)
{
    $updateTokenRequest = $database->prepare("UPDATE visiteur SET token_timestamp = CURRENT_TIMESTAMP WHERE id = ?");
    $updateTokenRequest->execute([$id]);
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