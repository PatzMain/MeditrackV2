<?php
include 'connection.php';
function isUserLoggedIn($pdo) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: false;
}

function requireLogin($pdo) {
    if (!isUserLoggedIn($pdo)) {
        header('Location: /Meditrack/login/');
        exit();
    }
}

function requireRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: /Meditrack/login/');
        exit();
    }
}

try {
    $checkSuperadmin = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'superadmin'");
    $checkSuperadmin->execute();
    $count = $checkSuperadmin->fetchColumn();

    if ($count == 0) {
        $username = "superadmin";
        $rawPassword = "superadmin123";
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
        $role = "superadmin";

        $insertSuperadmin = $pdo->prepare("
            INSERT INTO users (username, password, role) 
            VALUES (:username, :password, :role)
        ");
        $insertSuperadmin->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);

        error_log("Superadmin account created: username={$username} / password={$rawPassword}");
    }
} catch (PDOException $e) {
    error_log("Superadmin check/insert failed: " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Fetch user with case-sensitive username check
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE BINARY username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            header("Location: index.php");
            exit;
        }

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Log successful login
        $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_description, logs_status) VALUES (?, 'authentication', ?, 'login')");
        $log_stmt->execute([$user['user_id'], "User '{$username}' logged in"]);

        // Redirect to dashboard
        header("Location: ../pages/dashboard/");
        exit;

    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? null;
        
        // Log logout activity
        try {
            $description = $username ? "User '{$username}' logged out" : "User logged out";
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_description, logs_status) VALUES (?, 'authentication', ?, 'logout')");
            $stmt->execute([$user_id, $description]);
        } catch (PDOException $e) {
            error_log("Logout logging failed: " . $e->getMessage());
        }
    }
    
    // Clear session
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // Clear any cookies
    if (isset($_COOKIE['meditrack_remember'])) {
        setcookie('meditrack_remember', '', time() - 3600, '/');
    }
    
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Location: index.php");
    exit();
}

// If already logged in, redirect to dashboard
if (isUserLoggedIn($pdo) && !isset($_GET['action'])) {
    header("Location: ../pages/dashboard/");
    exit();
}
?>