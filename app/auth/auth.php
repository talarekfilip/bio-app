<?php
require_once '../config/config.php';
require_once '../config/database.php';

// TODO: Replace with your secret key
define('JWT_SECRET', 'your-secret-key-here');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: auth/login.php');
        exit;
    }
}

/**
 * Require user to be a guest (not logged in)
 */
function requireGuest() {
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Logout user
 */
function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit;
}

function generateToken($userId) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // 1 hour

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user_id' => $userId
    ];

    return jwt_encode($payload, JWT_SECRET, 'HS256');
}

function verifyToken($token) {
    try {
        $decoded = jwt_decode($token, JWT_SECRET, ['HS256']);
        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}

function isAuthenticated() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return true;
}

function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
} 