<?php
session_start();
include 'util/database.php';  // Connexion à la base de données

if (isset($_SESSION['user_id']) && isset($_POST['pseudo'])) {
    // Récupérer l'ID de l'utilisateur et le nouveau pseudo
    $userId = $_SESSION['user_id'];
    $newPseudo = htmlspecialchars($_POST['pseudo']);  // S'assurer que le pseudo est sécurisé

    // Préparer et exécuter la requête de mise à jour
    $stmt = $conn->prepare("UPDATE users SET pseudo = :pseudo WHERE id = :id");
    $stmt->bindParam(':pseudo', $newPseudo);
    $stmt->bindParam(':id', $userId);

    if ($stmt->execute()) {
        echo 'Pseudo mis à jour avec succès';
    } else {
        echo 'Erreur lors de la mise à jour du pseudo';
    }
} else {
    echo 'Données invalides';
}
?>
