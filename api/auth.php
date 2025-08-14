<?php
/**
 * Authentication Include File
 * Include this file at the top of any page that requires authentication
 * Usage: include '../../login/auth.php';
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$db_config = [
    'host' => 'localhost',
    'dbname' => 'meditrack_system',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Create PDO connection
try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset={$db_config['charset']}", 
        $db_config['username'], 
        $db_config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
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
    header('Location: /Meditrack/login/login.php?error=access');
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