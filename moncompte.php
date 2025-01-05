<?php
$title = "OOTD- Mon Compte";
$description = "Gérez vos informations personnelles et vos favoris sur OOTD.";
include 'include/header.inc.php';
include 'util/database.php';
include 'include/function.inc.php';

// Vérification de la session
if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['user_id'];

// Récupérer l'image de profil
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$imagePath = $user['profile_image'] ? $user['profile_image'] : 'https://via.placeholder.com/100';

// Récupérer le pseudo de l'utilisateur
$stmt = $conn->prepare("SELECT pseudo FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$pseudo = $user['pseudo'] ? htmlspecialchars($user['pseudo']) : 'Pseudo';

// Récupérer les informations personnelles
$stmt = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE id = :id");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$userInfo) {
    die("Utilisateur non trouvé.");
}

$firstname = $userInfo['firstname'];
$lastname = $userInfo['lastname'];
$email = $userInfo['email'];

// Gestion des actions POST (mise à jour des informations ou suppression du compte)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'logout') {
        // Déconnexion
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } elseif ($action === 'delete') {
        // Suppression du compte
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: connection.php");
            exit();
        } else {
            echo "Erreur lors de la suppression du compte.";
        }
    }
}

// Mise à jour des informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Vérifier la correspondance des mots de passe
    if (!empty($new_password) && $new_password !== $confirm_new_password) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Vérification du mot de passe actuel
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current_password, $user['password'])) {
        echo "Le mot de passe actuel est incorrect.";
        exit;
    }

    // Préparer la mise à jour des informations
    $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email";
    
    if (!empty($new_password)) {
        // Si un nouveau mot de passe est fourni, le hacher
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
    }

    $sql .= " WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Lier les paramètres à la requête préparée
    if (!empty($new_password)) {
        $stmt->bindValue(':password', $new_password_hashed, PDO::PARAM_STR);
    }

    $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

    // Exécuter la mise à jour
    if ($stmt->execute()) {
        $_SESSION['update_message'] = "Les informations ont été mises à jour avec succès.";
    } else {
        $_SESSION['update_message'] = "Erreur lors de la mise à jour : " . $stmt->errorInfo()[2];
    }

    // Redirection pour éviter la soumission multiple du formulaire
    header("Location: moncompte.php");
    exit();
}

// Afficher les messages de succès ou d'erreur
if (isset($_SESSION['update_message'])) {
    echo "<script>alert('" . $_SESSION['update_message'] . "');</script>";
    unset($_SESSION['update_message']);
}
?>


<script src="include/function.inc.js"></script>
		<section class="form-container form-container-main">
			<section class="sidebar">
				<h2>Profil</h2>
				<figure class="profile-container">
    <img src="<?= htmlspecialchars($imagePath) ?>" alt="Photo de profil" class="profile-img" id="profileImg">
    <i class="fas fa-pencil-alt edit-icon" onclick="document.getElementById('fileInput').click();"></i>
    <input type="file" id="fileInput" style="display:none;" onchange="uploadImage(event)">
</figure>


				<div class="editable-field">
    <span id="pseudo"><?= htmlspecialchars($pseudo) ?><i class="fas fa-pencil-alt edit-icon" onclick="enableEdit()"></i></span>
</div>

				<span>23°C, ensoleillé</span>
				<?php
                        $apiKey = 'AIzaSyDsx2L_wAyUAZIMJuTN2HwNCB9_090WdDs';
                        echo getUserLocation($apiKey);
                ?>

				<div class="container">
        			<span>Mode Sombre&nbsp;&nbsp;&nbsp;&nbsp;</span>
        			<label class="switch">
            			<input type="checkbox" id="modeSwitch">
            			<span class="slider"></span>
        			</label>
    			</div>
				<section class="section-container">
                

  		<form method="POST" action="">
    		<button class="btn" name="action" value="logout">Déconnexion</button>
            <button class="btn" name="action" value="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?');">Supprimer le compte</button>
        </form>
				</section>
			</section>
			<section class="sidebar-second">
				<section class="info-section">
       				<h2>Mes Informations</h2>



                <div class="form-container">
       				<form method="POST" action="">
    <div class="form-groupe">
        <label for="firstname"></label>
        <input 
            type="text" 
            name="firstname" 
            id="firstname" 
            value="<?php echo htmlspecialchars($firstname); ?>" 
            required 
            readonly>
        <span class="edit-icone" onclick="editField('firstname')"><i class="fas fa-pencil-alt"></i></span>
    </div>

    <div class="form-groupe">
        <label for="lastname"></label>
        <input 
            type="text" 
            name="lastname" 
            id="lastname" 
            value="<?php echo htmlspecialchars($lastname); ?>" 
            required 
            readonly>
        <span onclick="editField('lastname')"><i class="fas fa-pencil-alt edit-icone"></i></span>
    </div>

    <div class="form-groupe">
        <label for="email"></label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            value="<?php echo htmlspecialchars($email); ?>" 
            required 
            readonly>
        <span onclick="editField('email')"><i class="fas fa-pencil-alt edit-icone"></i></span>
    </div>

    <div class="form-groupe">
        <label for="current_password"></label>
        <input 
            type="password" 
            name="current_password" 
            id="current_password" 
            placeholder="Entrez votre mot de passe actuel" 
            required 
            readonly>
        <span onclick="editPasswordField('current_password')"><i class="fas fa-pencil-alt edit-icone"></i></span>
    </div>

    <div class="form-groupe">
        <label for="new_password"></label>
        <input 
            type="password" 
            name="new_password" 
            id="new_password" 
            placeholder="Entrez votre nouveau mot de passe" 
            readonly>
        <span onclick="editPasswordField('new_password')"><i class="fas fa-pencil-alt edit-icone"></i></span>
    </div>

    <div class="form-groupe">
        <label for="confirm_new_password"></label>
        <input 
            type="password" 
            name="confirm_new_password" 
            id="confirm_new_password" 
            placeholder="Confirmez votre nouveau mot de passe" 
            readonly>
        <span onclick="editPasswordField('confirm_new_password')"><i class="fas fa-pencil-alt edit-icone"></i></span>
    </div>

    <button type="submit" class="btn">Enregistrer les modifications</button>
</form>
                </div>
    			</section>
    			<section class="favorites-section">
        				<h2>Mes Favoris</h2>
                        <div class="form-container">
                            <div class="carousel-container">
                                    <div class="carousel" id="carousel">
        <!-- Vignettes -->
        <div class="carousel-item">1</div>
        <div class="carousel-item">2</div>
        <div class="carousel-item">3</div>
        <div class="carousel-item">4</div>
        <div class="carousel-item">5</div>
        <div class="carousel-item">6</div>
        <div class="carousel-item">7</div>
        <div class="carousel-item">8</div>
    </div>
    <!-- Flèches avec icônes -->
    <div class="arrow left-arrow" id="left-arrow" onclick="scrollCarousel('left')">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="arrow right-arrow" id="right-arrow" onclick="scrollCarousel('right')">
        <i class="fas fa-chevron-right"></i>
    </div>
</div>
                        </div>
    			</section>
			</section>
		</section>

</main>
<?php include 'include/footer.inc.php'; 
?>