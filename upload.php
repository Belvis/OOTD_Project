<?php
session_start();
include 'util/database.php';  // Connexion à la base de données

// Vérification si un fichier a été envoyé
if (isset($_FILES['profileImage'])) {
    $file = $_FILES['profileImage'];

    // Vérifier s'il n'y a pas d'erreur d'upload
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Définir un chemin pour le fichier
        $uploadDirectory = 'uploads/';
        $fileName = basename($file['name']);
        $filePath = $uploadDirectory . $fileName;

        // Vérifier si le fichier est une image
        if (getimagesize($file['tmp_name']) !== false) {
            // Déplacer le fichier dans le dossier 'uploads'
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Récupérer l'ID de l'utilisateur connecté
                $userId = $_SESSION['user_id'];

                // Mettre à jour la base de données avec le chemin du fichier
                $stmt = $conn->prepare("UPDATE users SET profile_image = :profile_image WHERE id = :id");
                $stmt->bindParam(':profile_image', $filePath);
                $stmt->bindParam(':id', $userId);
                if ($stmt->execute()) {
                    // Retourner le chemin de l'image en JSON
                    echo json_encode(["status" => "success", "imagePath" => $filePath]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Erreur lors de l'enregistrement dans la base de données."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Erreur lors du déplacement du fichier."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Le fichier n'est pas une image valide."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors de l'upload de l'image."]);
    }
}


// Limiter la taille du fichier à 5MB
if ($file['size'] > 5000000) {
    echo "Le fichier est trop volumineux.";
    exit;
}

// Vérifier que le fichier est bien une image
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowedTypes)) {
    echo "Seules les images JPG, PNG et GIF sont autorisées.";
    exit;
}

?>

