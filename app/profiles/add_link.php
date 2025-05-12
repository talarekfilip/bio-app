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

$platform = $_POST['platform'] ?? '';
$url = $_POST['url'] ?? '';
$display_order = $_POST['display_order'] ?? 0;

if (empty($platform) || empty($url)) {
    echo json_encode([
        'success' => false,
        'message' => 'Wszystkie pola są wymagane'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO social_links (profile_id, platform, url, display_order) VALUES (?, ?, ?, ?)');
    $stmt->execute([$profile['id'], $platform, $url, $display_order]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Link został dodany pomyślnie'
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Wystąpił błąd podczas dodawania linku'
    ]);
} 