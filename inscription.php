<?php
$title = "OOTD - Inscription";
$description = "Bienvenue sur OOTD. Inscrivez-vous pour découvrir nos fonctionnalités !";
include 'include/header.inc.php';
include 'util/database.php';
include 'include/function.inc.php';
$message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $message = register($conn, $firstname, $lastname, $email, $password, $confirm_password);
    };
    if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'signin') {
        header("Location: connection.php");
        exit();
    }
}
?>

    <div class="form-container">
    <h1>Inscription</h1>
    <?php if (!empty($message)): ?>
        <div class="notification"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="firstname">Prénom :</label>
            <input type="text" name="firstname" id="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Nom :</label>
            <input type="text" name="lastname" id="lastname" required>
        </div>
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                required 
                onmouseover="togglePassword('password', true)" 
                onmouseout="togglePassword('password', false)"
                placeholder="Votre mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial." 
            >
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirmez le mot de passe :</label>
            <input 
                type="password" 
                name="confirm_password" 
                id="confirm_password" 
                required 
                onmouseover="togglePassword('confirm_password', true)" 
                onmouseout="togglePassword('confirm_password', false)"
            >
        </div>
        <button type="submit" name="action"  value="signin" class="btn">S'inscrire</button>
        <button type="button" class="btn" onclick="window.location.href='index.php'">Retour</button>
    </form>
</div>
</main>
<?php include 'include/footer.inc.php'; ?>
