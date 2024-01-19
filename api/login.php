<?php
header("Content-Type:application/json");
session_start();

function generateToken($length = 32) {
    return bin2hex(openssl_random_pseudo_bytes($length));
}

try {
    if (isset($_POST['email']) && isset($_POST['password'])) {

        include('../database.php');

        $email = $_POST['email'];
        $password = $_POST['password'];

        $requete = $dbh->prepare("SELECT id, status, mdp, numero, adresse, cp, email, cv_car FROM visiteur WHERE email = :email");
        $requete->execute(['email' => $email]);
        $row = $requete->fetch();

        if ($row) {
            if ($row["status"] === 4) {
                $json = array('status' => 400, 'message' => "DEACTIVATED ACCOUNT");
            } elseif (password_verify($password, $row["mdp"])) {
                $token = generateToken();
                
                $updateToken = $dbh->prepare("UPDATE visiteur SET token = :token, token_timestamp = CURRENT_TIMESTAMP WHERE id = :id");
                $updateToken->execute(['token' => $token, 'id' => $row["id"]]);

                $_SESSION['user'] = $row["id"];
                $_SESSION['status'] = $row["status"];

                $json = array('status' => 200, 'id' => $row["id"], 'message' => "SUCCESS", 'token' => $token);
            } else {
                $json = array('status' => 202, 'message' => "Invalid Credentials");
            }
            echo json_encode($json);
        } else {
            echo json_encode(array('status' => 404, 'message' => "EMAIL NOT FOUND"));
        }
    }
} catch (PDOException $e) {
    echo json_encode(array('status' => 500, 'message' => "DATABASE ERROR: " . $e->getMessage()));
}
?>
