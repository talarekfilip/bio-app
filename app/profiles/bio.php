<?php
require_once '../config/config.php';
require_once '../config/database.php';

$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

if (!$username) {
    header('Location: ../index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.email 
        FROM profiles p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.username = ?
    ");
    $stmt->execute([$username]);
    $profile = $stmt->fetch();

    if (!$profile) {
        header('Location: ../index.php');
        exit;
    }

    // Pobierz linki społecznościowe
    $stmt = $pdo->prepare("
        SELECT * FROM social_links 
        WHERE profile_id = ? 
        ORDER BY display_order
    ");
    $stmt->execute([$profile['id']]);
    $social_links = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dekoduj ustawienia tła
    $background_settings = json_decode($profile['background_settings'], true);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['display_name']); ?> - Portfolio Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/bio.css">
    <style>
        :root {
            --theme-color: <?php echo htmlspecialchars($profile['theme_color']); ?>;
        }
        
        <?php if ($background_settings['type'] === 'color'): ?>
        body {
            background-color: <?php echo htmlspecialchars($background_settings['settings']['background_color']); ?>;
        }
        <?php elseif ($background_settings['type'] === 'gradient'): ?>
        body {
            background: linear-gradient(<?php echo htmlspecialchars($background_settings['settings']['gradient_direction']); ?>, 
                                     <?php echo htmlspecialchars($background_settings['settings']['gradient_start']); ?>, 
                                     <?php echo htmlspecialchars($background_settings['settings']['gradient_end']); ?>);
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <?php if ($background_settings['type'] === 'image'): ?>
    <div class="background-image" style="
        background-image: url('<?php echo htmlspecialchars($background_settings['settings']['background_image']); ?>');
        opacity: <?php echo htmlspecialchars($background_settings['settings']['image_opacity'] / 100); ?>;
    "></div>
    <?php endif; ?>

    <?php if ($background_settings['type'] === 'video'): ?>
    <video class="background-video" autoplay muted loop style="
        opacity: <?php echo htmlspecialchars($background_settings['settings']['video_opacity'] / 100); ?>;
    ">
        <source src="<?php echo htmlspecialchars($background_settings['settings']['background_video']); ?>" type="video/mp4">
    </video>
    <?php endif; ?>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if ($profile['avatar_url']): ?>
                        <img src="<?php echo htmlspecialchars($profile['avatar_url']); ?>" alt="Profile Avatar">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($profile['display_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <h1><?php echo htmlspecialchars($profile['display_name']); ?></h1>
                <?php if ($profile['bio']): ?>
                    <p class="bio"><?php echo nl2br(htmlspecialchars($profile['bio'])); ?></p>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <?php if ($profile['location']): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($profile['location']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($profile['website']): ?>
                    <div class="info-item">
                        <i class="fas fa-globe"></i>
                        <a href="<?php echo htmlspecialchars($profile['website']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($profile['website']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="profile-actions">
                <a href="../index.php" class="back-button">Back to Home</a>
            </div>
        </div>
    </div>

    <div class="social-links">
        <?php foreach ($social_links as $link): ?>
        <a href="<?php echo htmlspecialchars($link['url']); ?>" 
           class="social-link" 
           target="_blank" 
           rel="noopener noreferrer">
            <span class="platform"><?php echo htmlspecialchars($link['platform']); ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- TODO: Replace with your Font Awesome kit URL -->
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 