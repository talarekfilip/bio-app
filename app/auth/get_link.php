<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing link ID']);
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
    
    // Pobierz dane linku
    $stmt = $pdo->prepare("
        SELECT id, platform, url, display_order 
        FROM social_links 
        WHERE id = ? AND profile_id = ?
    ");
    $stmt->execute([$_GET['id'], $profile['id']]);
    $link = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$link) {
        echo json_encode(['success' => false, 'message' => 'Link not found or unauthorized']);
        exit;
    }
    
    echo json_encode($link);
    
} catch (Exception $e) {
    error_log("Błąd pobierania linku: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching the link'
    ]);
} 