<?php
require_once '../config/database.php';
require_once '../vendor/PHPMailer/src/Exception.php';
require_once '../vendor/PHPMailer/src/PHPMailer.php';
require_once '../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Pobierz ostatni niezweryfikowany email z sesji
    session_start();
    if (!isset($_SESSION['temp_email'])) {
        echo json_encode(['success' => false, 'message' => 'No email found for verification']);
        exit;
    }
    
    $email = $_SESSION['temp_email'];
    
    // Wygeneruj nowy kod
    $verificationCode = sprintf("%06d", mt_rand(0, 999999));
    
    // Aktualizuj kod w bazie danych
    $stmt = $pdo->prepare("UPDATE users SET verification_code = ? WHERE email = ? AND is_verified = 0");
    $stmt->execute([$verificationCode, $email]);
    
    // Wyślij nowy email
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';
    $mail->Password = 'YOUR_PASSWORD';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('your-email@gmail.com', 'Portfolio Generator');
    $mail->addAddress($email);
    
    $mail->isHTML(true);
    $mail->Subject = 'New Verification Code';
    $mail->Body = "
        <h2>Your New Verification Code</h2>
        <p>Your new verification code is: <strong>{$verificationCode}</strong></p>
        <p>The code is valid for 24 hours.</p>
    ";
    
    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => 'New verification code has been sent to your email'
    ]);
    
} catch (Exception $e) {
    error_log("Błąd ponownego wysyłania kodu: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while resending the code'
    ]);
} 