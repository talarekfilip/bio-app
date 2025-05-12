<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'auth/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Portfolio Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <div class="welcome-box">
            <h1>Welcome to Portfolio Generator</h1>
            <p class="subtitle">Create your professional portfolio in minutes</p>
            
            <div class="features">
                <div class="feature">
                    <h3>Customizable Profile</h3>
                    <p>Personalize your profile with your own style</p>
                </div>
                <div class="feature">
                    <h3>Easy to Share</h3>
                    <p>Share your portfolio with a single link</p>
                </div>
                <div class="feature">
                    <h3>Modern Design</h3>
                    <p>Clean and professional look</p>
                </div>
            </div>

            <div class="cta-buttons">
                <a href="auth/register.php" class="btn btn-primary">Get Started</a>
                <a href="auth/login.php" class="btn btn-secondary">Login</a>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
