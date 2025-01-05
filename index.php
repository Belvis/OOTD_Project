<?php
	$title = "OOTD- Dress easy"; 
	$description = "Bienvenue sur OOTD. Découvrez ce que nous proposons et trouvez ce que vous cherchez."; 
	include 'include/header.inc.php'; 
?>
<h1>OOTD - Dress up the way you like </h1>
	<div class="container">
		<div class="form-container">
			<p>Pour profitez pleinement des fonctionnalités du site vous devez d'abord vous connectez</p>
			<button type="button" class="btn" onclick="window.location.href='connection.php'">Se connecter</button>
			<p>Vous n'avez pas de compte ? Pas de souci inscrivez vous </p>
			<button type="button" class="btn" onclick="window.location.href='inscription.php'">S'inscrire</button>
			
		</div>
	</div>
</main>
<?php include 'include/footer.inc.php'; 
?>