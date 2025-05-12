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

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: ../dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portfolio Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>Login</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="auth-button">Login</button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html> 