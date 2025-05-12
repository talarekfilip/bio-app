<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ../dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                $stmt->execute([$email, $hashed_password]);
                
                // Get the new user's ID
                $user_id = $pdo->lastInsertId();
                
                // Create profile
                $username = 'user' . $user_id;
                $display_name = 'User ' . $user_id;
                $stmt = $pdo->prepare("INSERT INTO profiles (user_id, username, display_name) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $username, $display_name]);
                
                // Log in the user
                $_SESSION['user_id'] = $user_id;
                header('Location: ../dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'An error occurred during registration. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Portfolio Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>Register</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="auth-button">Register</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html> 