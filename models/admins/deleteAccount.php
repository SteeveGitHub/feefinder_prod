
<?php
include('../../database.php');

$id = $_GET['id'];
$requete = $dbh->prepare("DELETE FROM visiteur WHERE id = ?");
$requete->execute([$id]);
$row = $requete->fetchAll(PDO::FETCH_ASSOC);
$requete->closeCursor();
header('Location: ../../views/admin/adminView.php');
?>
