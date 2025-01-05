<?php
	$title = "OOTD- Ma garde robe"; 
	$description = "L'inventaire de vos vetements"; 
	include 'include/header.inc.php'; 
	include 'util/database.php';
	include 'include/function.inc.php';

	if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}
?>
</main>
<?php include 'include/footer.inc.php'; 
?>