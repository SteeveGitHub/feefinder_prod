<?php
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['status'])) {
        // Inclure votre fichier de configuration de la base de données ici
        include '../../database.php';

        $commercialId = $_SESSION['user'];

        // Effectuer la requête pour récupérer les données
        $queryTransportRepas = $dbh->prepare("SELECT SUM(transport_type='Transport') AS totalTransport, SUM(transport_type='Repas') AS totalRepas FROM frais WHERE user_id = ?");
        $queryTransportRepas->execute([$commercialId]);
        $transportRepasData = $queryTransportRepas->fetch(PDO::FETCH_ASSOC);

        // Renvoyer les données au format JSON
        header('Content-Type: application/json');
        echo json_encode($transportRepasData);
    } else {
        // Renvoyer une erreur si l'utilisateur n'est pas connecté
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'Unauthorized']);
    }

    session_destroy();
?>
