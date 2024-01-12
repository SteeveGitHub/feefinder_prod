<?php
session_start();
include '../../database.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../connexion/loginView.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table'];
    $id = $_POST['id'];
    $user_id = $_SESSION['user'];

    $sql = "";
    $params = [];

    // Construire la requête SQL et les paramètres en fonction de la table
    if ($table === 'frais') {
         // Traitement du formulaire "Fiche Forfait"
         $user_id = $_SESSION['user'];
         $date_debut = date('Y-m-d', strtotime($_POST["date"]));
 
         // Traitement des hébergements
         $price_night = $_POST["priceNight"] ? $_POST["priceNight"] : 0;
         $number_night = $_POST["numberNight"] ? $_POST["numberNight"] : 0;
         // $total_nuit_user = ($price_night * $number_night)/$number_night;
         $total_nuit_user = ($number_night != 0) ? $price_night : 0;
         $total_nuit_user = intval($total_nuit_user);
 
         // Traitement des repas
         $number_meal = $_POST["numberMeal"] ? $_POST["numberMeal"] : 0;
         $price_meal = $_POST["priceMeal"] ? $_POST["priceMeal"] : 0;
         $total_meal_user = ($number_meal != 0) ? $price_meal : 0;
         $total_meal_user = intval($total_meal_user);
 
         $total_user = $total_nuit_user + $total_meal_user;
         $total_user = intval($total_user);
 
         // Traitement des trajets
         $km = $_POST["km"] ? $_POST["km"] : 0;
         $transport_type = $_POST["transport"];
 
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
 
    
        $sql = "UPDATE frais SET date_debut = ?, total_night_price = ?, night_quantity = ?, total_meal_price = ?, meal_quantity = ?, km = ?, transport_type = ?, montantRestant = ? WHERE id = ?";
        $params = [
            $_POST['date'],
            $_POST['priceNight'],
            $_POST['numberNight'],
            $_POST['priceMeal'],
            $_POST['numberMeal'],
            $_POST['km'],
            $_POST['transport'],
            $total_charge,
            $id
        ];
    } elseif ($table === 'hors_forfait') {
         // Traitement du formulaire "Fiche Hors Forfait"
         $date_debut = date('Y-m-d', strtotime($_POST["date"]));
         $user_id = $_SESSION['user'];
         $number_days = $_POST["number_days"];
         $description = $_POST["description-area"];
         $total_price = $_POST["hors-forfait-prix"]; 
         
        $sql = "UPDATE hors_forfait SET description = ?, total_price = ?, number_days = ? WHERE id = ?";
        $params = [
            $_POST['description-area'],
            $_POST['hors-forfait-prix'],
            $_POST['number_days'],
            $id
        ];
    }

    // Exécuter la requête SQL
    $requete = $dbh->prepare($sql);
    $requete->execute($params);

    header('Location: consulterFraisView.php');
    exit();
} else {
    echo "Erreur : méthode de requête incorrecte.";
}
