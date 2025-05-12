<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'auth/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit;
}

try {
    // Get user profile
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $stmt->execute([getCurrentUserId()]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // If profile doesn't exist, create one
    if (!$profile) {
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, username, display_name) VALUES (?, ?, ?)");
        $stmt->execute([getCurrentUserId(), 'user' . getCurrentUserId(), 'User ' . getCurrentUserId()]);
        
        $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
        $stmt->execute([getCurrentUserId()]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("An error occurred while loading your profile. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Portfolio Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="dashboard-nav">
            <div class="nav-brand">
                <h1>Portfolio Generator</h1>
            </div>
            <div class="nav-links">
                <a href="profiles/<?php echo htmlspecialchars($profile['username']); ?>" target="_blank">View Profile</a>
                <a href="auth/logout.php">Logout</a>
            </div>
        </nav>

        <main class="dashboard-main">
            <section id="profile" class="dashboard-section active">
                <div class="profile-header">
                    <h2>Profile Settings</h2>
                </div>

                <div class="profile-content">
                    <div class="profile-preview">
                        <div class="profile-avatar">
                            <img src="<?php echo htmlspecialchars($profile['avatar_url'] ?? 'assets/images/default-avatar.png'); ?>" 
                                 alt="Profile Avatar" 
                                 id="avatarPreview">
                            <div class="avatar-overlay">Change avatar</div>
                        </div>
                        <div class="profile-link">
                            <input type="text" id="profileLink" value="<?php echo htmlspecialchars(SITE_URL . '/profiles/' . $profile['username']); ?>" readonly>
                            <button class="copy-link">Copy Link</button>
                        </div>
                    </div>

                    <form id="profileForm" action="profiles/update_profile.php" method="POST" enctype="multipart/form-data">
                        <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="display_name">Display Name</label>
                            <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($profile['display_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="save-button">Save Changes</button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <div class="notification-container"></div>
    <script src="assets/js/dashboard.js"></script>
</body>
</html> 