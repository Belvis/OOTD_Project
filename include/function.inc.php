<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}else if 
(!isset($_SESSION['user_id'])) {
    // Redirige les utilisateurs non connectés
    header("Location: connection.php");
    exit();
};

function login($conn, $email, $password) {
    try {
        // Requête pour trouver l'utilisateur par email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Si un utilisateur est trouvé
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Démarrer une session pour l'utilisateur
                $_SESSION['user_id'] = $user['id'];  // Stocke uniquement l'ID de l'utilisateur dans la session
  // Stocke l'utilisateur dans la session
                return "Connexion réussie !";
            } else {
                return "Mot de passe incorrect.";
            }
        } else {
            return "Aucun utilisateur trouvé avec cet email.";
        }
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
}


function register($pdo, $firstname, $lastname, $email, $password, $confirm_password) {
    // Vérification des mots de passe
    if ($password !== $confirm_password) {
        return "Les mots de passe ne correspondent pas.";
    }

    // Vérification de la force du mot de passe
    if (!isStrongPassword($password)) {
        return "Le mot de passe n'est pas assez fort. Il doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
    }

    try {
        // Vérifier si l'email existe déjà
        $checkEmailStmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $checkEmailStmt->bindParam(':email', $email);
        $checkEmailStmt->execute();

        if ($checkEmailStmt->rowCount() > 0) {
            return "Cette adresse e-mail est déjà utilisée.";
        } else {
            // Insertion du nouvel utilisateur
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                return "Inscription réussie !";
            } else {
                return "Erreur lors de l'inscription.";
            }
        }
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
}



function isStrongPassword($password) {
    if (strlen($password) < 8) {
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    if (!preg_match('/[\W_]/', $password)) {
        return false;
    }
    return true;
}

function getGeolocation($apiKey, $wifiAccessPoints = null, $cellTowers = null) {
    $url = "https://www.googleapis.com/geolocation/v1/geolocate?key=$apiKey";

    // Construction du corps de la requête
    $data = [];
    if ($wifiAccessPoints) {
        $data['wifiAccessPoints'] = $wifiAccessPoints;
    }
    if ($cellTowers) {
        $data['cellTowers'] = $cellTowers;
    }
    $data['considerIp'] = true;

    // Encodage des données en JSON
    $jsonData = json_encode($data);

    // Initialisation de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    // Exécution de la requête cURL
    $response = curl_exec($ch);
    curl_close($ch);

    // Traitement de la réponse JSON
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['location'])) {
            return [
                'latitude' => $result['location']['lat'],
                'longitude' => $result['location']['lng'],
                'accuracy' => $result['accuracy']
            ];
        }
    }

    // Retourner null si la géolocalisation échoue
    return null;
}


function getUserLocation($apiKey) {
    // Obtenir les coordonnées géographiques
    $geolocation = getGeolocation($apiKey);

    // Si la géolocalisation a échoué
    if (!$geolocation) {
        return "<span>Localisation inconnue</span>";
    }

    $latitude = $geolocation['latitude'];
    $longitude = $geolocation['longitude'];

    // URL de l'API de géocodage inversé de Google
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=$apiKey";

    // Initialisation de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Exécution de la requête cURL
    $response = curl_exec($ch);
    curl_close($ch);

    // Traitement de la réponse JSON
    if ($response) {
        $result = json_decode($response, true);

        // Vérification si la réponse contient des résultats
        if (isset($result['results'][0])) {
            // Recherche de la ville et du pays dans la réponse
            $addressComponents = $result['results'][0]['address_components'];
            $city = '';
            $country = '';

            foreach ($addressComponents as $component) {
                if (in_array('locality', $component['types'])) {
                    $city = $component['long_name'];
                }
                if (in_array('country', $component['types'])) {
                    $country = $component['long_name'];
                }
            }

            // Retourner directement le <span> avec la ville et le pays
            return "<span>". htmlspecialchars($city). ", ". htmlspecialchars($country) ."</span>";
        }
    }

    // Si aucun résultat n'est trouvé
    return "<span>Localisation inconnue</span>";
}

function getImages() {
    $query = "SELECT * FROM images ORDER BY RAND() LIMIT 3S0"; // 20 images aléatoires
    $result = mysqli_query($conn, $query);
    $images = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
}

function savePreference($user_id, $image_id, $liked) {
    $query = "INSERT INTO user_preferences (user_id, image_id, liked) VALUES (?, ?, ?)
              ON DUPLICATE KEY UPDATE liked = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiii", $user_id, $image_id, $liked, $liked);
    mysqli_stmt_execute($stmt);
}

function getUserPreferences($user_id) {
    global $conn; // Connexion PDO globale

    // Requête SQL avec PDO
    $query = "SELECT color, style, COUNT(*) as count
              FROM user_preferences 
              JOIN images ON user_preferences.image_id = images.image_id
              WHERE user_preferences.user_id = :user_id AND user_preferences.liked = 1
              GROUP BY color, style
              ORDER BY count DESC";

    // Préparer la requête PDO
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les résultats
    $preferences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $preferences;
}
function likeImage($user_id, $image_id, $like) {
    global $conn;
    $query = "SELECT * FROM user_preferences WHERE user_id = :user_id AND image_id = :image_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $updateQuery = "UPDATE user_preferences SET liked = :liked WHERE user_id = :user_id AND image_id = :image_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':liked', $like, PDO::PARAM_INT);
        $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $updateStmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        $updateStmt->execute();
    } else {
        $insertQuery = "INSERT INTO user_preferences (user_id, image_id, liked) VALUES (:user_id, :image_id, :liked)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':liked', $like, PDO::PARAM_INT);
        $insertStmt->execute();
    }
}
function getImagesToLike($user_id) {
    global $conn; 
    $query = "SELECT image_id, image_url, color, style FROM images WHERE image_id NOT IN (SELECT image_id FROM user_preferences WHERE user_id = :user_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
