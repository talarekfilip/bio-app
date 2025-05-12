<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Nie jesteś zalogowany'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Nieprawidłowa metoda żądania'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$link_id = $data['id'] ?? null;

if (!$link_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Brak ID linku'
    ]);
    exit;
}

try {
    // Pobierz ID profilu
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        echo json_encode([
            'success' => false,
            'message' => 'Nie znaleziono profilu'
        ]);
        exit;
    }

    // Sprawdź czy link należy do profilu
    $stmt = $pdo->prepare('SELECT id FROM social_links WHERE id = ? AND profile_id = ?');
    $stmt->execute([$link_id, $profile['id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Nie znaleziono linku lub nie masz do niego dostępu'
        ]);
        exit;
    }
    
    // Usuń link
    $stmt = $pdo->prepare('DELETE FROM social_links WHERE id = ?');
    $stmt->execute([$link_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Link został usunięty pomyślnie'
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Wystąpił błąd podczas usuwania linku'
    ]);
} 