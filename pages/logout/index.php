<?php
include '../../api/auth.php';

// Function to log logout activity
function logLogoutActivity($pdo, $user_id, $username = null) {
    try {
        $description = $username ? "User '{$username}' logged out" : "User logged out";
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, a_item_type, a_item_id, a_description, a_quantity, a_status) VALUES (?, NULL, NULL, ?, NULL, ?)");
        $stmt->execute([$user_id, $description, 'logout']);
    } catch (PDOException $e) {
        // Log error but don't prevent logout
        error_log("Failed to log logout activity: " . $e->getMessage());
    }
}

// Check if user is logged in and log the logout activity
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    
    // Log the logout activity
    try {
        logLogoutActivity($pdo, $user_id, $username);
    } catch (Exception $e) {
        // Continue with logout even if logging fails
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear any authentication cookies if they exist
if (isset($_COOKIE['meditrack_remember'])) {
    setcookie('meditrack_remember', '', time() - 3600, '/');
}

// Prevent caching of this page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to index.html with a logout message
header("Location: ../../login/");
exit();
?>