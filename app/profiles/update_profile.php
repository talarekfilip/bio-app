<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../auth/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'You must be logged in to update your profile']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = getCurrentUserId();
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$display_name = filter_input(INPUT_POST, 'display_name', FILTER_SANITIZE_STRING);
$bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);

try {
    // Check if username is already taken
    if ($username) {
        $stmt = $pdo->prepare("SELECT id FROM profiles WHERE username = ? AND user_id != ?");
        $stmt->execute([$username, $user_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username is already taken']);
            exit;
        }
    }

    // Handle avatar upload
    $avatar_url = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        
        // Validate file type
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed types: JPG, PNG, GIF']);
            exit;
        }
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size: 5MB']);
            exit;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = UPLOAD_DIR . '/' . $filename;
        
        // Create uploads directory if it doesn't exist
        if (!file_exists(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $avatar_url = 'uploads/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload avatar']);
            exit;
        }
    }

    // Update profile
    $sql = "UPDATE profiles SET 
            username = COALESCE(?, username),
            display_name = COALESCE(?, display_name),
            bio = ?,
            location = ?,
            website = ?";
    
    $params = [$username, $display_name, $bio, $location, $website];
    
    if ($avatar_url) {
        $sql .= ", avatar_url = ?";
        $params[] = $avatar_url;
    }
    
    $sql .= " WHERE user_id = ?";
    $params[] = $user_id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating profile']);
} 