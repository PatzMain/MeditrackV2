<?php
include 'connection.php';
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isUserLoggedIn($pdo) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: false;
}

// Require login - redirect to login page if not authenticated
if (!isUserLoggedIn($pdo)) {
    header('Location: login/index.php');
    exit();
}

// Helper function to check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Helper function to check if user is superadmin
function isSuperAdmin() {
    return hasRole('superadmin');
}

// Helper function to check if user is admin or superadmin
function isAdmin() {
    return hasRole('admin') || hasRole('superadmin');
}
?>