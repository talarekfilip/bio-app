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

if (!isset($data['id'])) {
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
    
    // Usuń link
    $stmt = $pdo->prepare("DELETE FROM social_links WHERE id = ? AND profile_id = ?");
    $stmt->execute([$data['id'], $profile['id']]);
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Link not found or unauthorized']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Link deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Błąd usuwania linku: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while deleting the link'
    ]);
} 