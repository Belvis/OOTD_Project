<?php
header('Content-Type: application/json');

try {
    // Logique pour enregistrer le "like" ou "dislike"
    // Supposons que tu utilises PDO pour insérer dans la base de données
    $imageId = $_POST['image_id'];
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    // Exemple de logique SQL simplifiée
    $stmt = $conn->prepare("INSERT INTO user_preferences (user_id, image_id, action) VALUES (:user_id, :image_id, :action)");
    $stmt->execute([
        ':user_id' => $userId,
        ':image_id' => $imageId,
        ':action' => $action
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
