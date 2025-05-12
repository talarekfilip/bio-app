<?php
require_once 'config/config.php';
require_once 'config/database.php';

$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

if (!$username) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE username = ?");
    $stmt->execute([$username]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("An error occurred while fetching profile data");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['display_name']); ?> - Portfolio</title>
    <link rel="stylesheet" href="assets/css/bio.css">
</head>
<body>
    <div class="bio-container">
        <div class="profile-card">
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($profile['avatar_url'] ?? 'assets/images/default-avatar.png'); ?>" 
                     alt="<?php echo htmlspecialchars($profile['display_name']); ?>" 
                     class="profile-avatar">
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($profile['display_name']); ?></h1>
                    <p class="username">@<?php echo htmlspecialchars($profile['username']); ?></p>
                </div>
            </div>

            <?php if ($profile['bio']): ?>
            <div class="bio-section">
                <p><?php echo nl2br(htmlspecialchars($profile['bio'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if ($profile['location']): ?>
            <div class="location-section">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo htmlspecialchars($profile['location']); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($profile['website']): ?>
            <div class="website-section">
                <a href="<?php echo htmlspecialchars($profile['website']); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer">
                    <?php echo htmlspecialchars($profile['website']); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</body>
</html> 