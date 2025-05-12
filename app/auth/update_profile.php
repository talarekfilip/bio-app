<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['display_name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Sprawdź czy username jest już zajęty
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE username = ? AND user_id != ?");
    $stmt->execute([$data['username'], $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken']);
        exit;
    }
    
    // Sprawdź czy profil istnieje
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($profile) {
        // Aktualizuj istniejący profil
        $stmt = $pdo->prepare("
            UPDATE profiles 
            SET username = ?, 
                display_name = ?, 
                bio = ?, 
                theme_color = ?,
                background_type = ?,
                background_settings = ?
            WHERE user_id = ?
        ");
        
        $backgroundSettings = json_encode([
            'type' => $data['background_type'],
            'settings' => $data
        ]);
        
        $stmt->execute([
            $data['username'],
            $data['display_name'],
            $data['bio'] ?? '',
            $data['theme_color'] ?? '#000000',
            $data['background_type'] ?? 'color',
            $backgroundSettings,
            $_SESSION['user_id']
        ]);
    } else {
        // Utwórz nowy profil
        $stmt = $pdo->prepare("
            INSERT INTO profiles 
            (user_id, username, display_name, bio, theme_color, background_type, background_settings)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $backgroundSettings = json_encode([
            'type' => $data['background_type'],
            'settings' => $data
        ]);
        
        $stmt->execute([
            $_SESSION['user_id'],
            $data['username'],
            $data['display_name'],
            $data['bio'] ?? '',
            $data['theme_color'] ?? '#000000',
            $data['background_type'] ?? 'color',
            $backgroundSettings
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Błąd aktualizacji profilu: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating profile'
    ]);
} 