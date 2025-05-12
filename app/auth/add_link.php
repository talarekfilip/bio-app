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

if (!isset($data['platform']) || !isset($data['url'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Pobierz ID profilu
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$profile) {
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
        exit;
    }
    
    // Sprawdź czy to edycja istniejącego linku
    if (isset($data['edit_id'])) {
        $stmt = $pdo->prepare("
            UPDATE social_links 
            SET platform = ?, 
                url = ?, 
                display_order = ?
            WHERE id = ? AND profile_id = ?
        ");
        
        $stmt->execute([
            $data['platform'],
            $data['url'],
            $data['display_order'] ?? 0,
            $data['edit_id'],
            $profile['id']
        ]);
    } else {
        // Dodaj nowy link
        $stmt = $pdo->prepare("
            INSERT INTO social_links 
            (profile_id, platform, url, display_order)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $profile['id'],
            $data['platform'],
            $data['url'],
            $data['display_order'] ?? 0
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => isset($data['edit_id']) ? 'Link updated successfully' : 'Link added successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Błąd dodawania/aktualizacji linku: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while saving the link'
    ]);
} 