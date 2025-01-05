<?php
$title = "OOTD - Connexion";
$description = "Connectez-vous à votre compte OOTD";
include 'include/header.inc.php';
include 'util/database.php'; 
include 'include/function.inc.php'; 

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $authResult = login($conn, $email, $password);

    if (is_array($authResult)) {
        // Connexion réussie, création de la session
        $_SESSION['user_id'] = $authResult['id'];
        $_SESSION['firstname'] = $authResult['firstname'];
        $_SESSION['lastname'] = $authResult['lastname'];
        $_SESSION['email'] = $authResult['email'];
    } else {
        $message = $authResult;
    }
}
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'log') {
        header("Location: moncompte.php");
        exit();
    }
}
?>
<!-- Formulaire de connexion -->
<div class="container">
    <div class="form-container">
        <h1>Connexion</h1>
        <?php if (!empty($message)): ?>
            <div class="notification"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
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
                >
            </div>
            <button type="submit" class="btn" value="log"  name="action">Se connecter</button>
            <button type="button" class="btn" onclick="window.location.href='index.php'">Retour</button>
        </form>
    </div>
</div>
</main>
<?php include 'include/footer.inc.php'; ?>
