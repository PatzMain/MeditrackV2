<?php
// ========================================
// CONFIGURATION & DATABASE CONNECTION
// ========================================
session_start();

$db_config = [
    'host' => 'localhost',
    'dbname' => 'meditrack_system',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

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

// ========================================
// AUTHENTICATION FUNCTIONS
// ========================================
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
        header('Location: /Meditrack/login/index.php?error=access');
        exit();
    }
}

function requireRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: /Meditrack/login/index.php?error=access');
        exit();
    }
}

/* ========================================
   AUTO-CREATE SUPERADMIN IF MISSING
   ======================================== */
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

// ========================================
// HANDLE LOGIN FORM SUBMISSION
// ========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Fetch user with case-sensitive username check
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE BINARY username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            header("Location: index.php?error=access");
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

// ========================================
// HANDLE LOGOUT
// ========================================
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
    header("Location: login.php?logout=success");
    exit();
}

// If already logged in, redirect to dashboard
if (isUserLoggedIn($pdo) && !isset($_GET['action'])) {
    header("Location: ../pages/dashboard/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Login</title>
    <style>
        /* ========================================
           EMBEDDED CSS STYLES
           ======================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        :root {
            --primary-color: #0f7b0f;
            --primary-hover: #0a5a0a;
            --secondary-color: #00cc44;
            --accent-color: #4CAF50;
            --form-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #1a1a1a;
            --text-secondary: #666;
            --border-color: #e0e0e0;
            --focus-color: #0f7b0f;
            --error-color: #dc3545;
            --success-color: #28a745;
            --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.15);
            --shadow-heavy: 0 25px 80px rgba(0, 0, 0, 0.25);
            --glow-green: 0 0 30px rgba(15, 123, 15, 0.3);
        }

        body {
            background-image: url("../assets/background.png");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* Background effects */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(15, 123, 15, 0.1));
            backdrop-filter: blur(5px);
            animation: float 8s ease-in-out infinite;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .shape:nth-child(1) { width: 120px; height: 120px; top: 10%; left: 15%; animation-delay: 0s; }
        .shape:nth-child(2) { width: 180px; height: 180px; top: 60%; left: 75%; animation-delay: 2s; }
        .shape:nth-child(3) { width: 90px; height: 90px; top: 35%; left: 5%; animation-delay: 4s; }
        .shape:nth-child(4) { width: 150px; height: 150px; top: 80%; left: 20%; animation-delay: 6s; }
        .shape:nth-child(5) { width: 70px; height: 70px; top: 20%; right: 10%; animation-delay: 3s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            33% { transform: translateY(-30px) rotate(120deg) scale(1.1); }
            66% { transform: translateY(15px) rotate(240deg) scale(0.9); }
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(15, 123, 15, 0.6);
            border-radius: 50%;
            animation: particle-float 15s linear infinite;
        }

        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        /* Header */
        header {
            background: transparent;
            text-align: center;
            padding: 1.5rem 1rem;
            position: relative;
            z-index: 10;
            flex-shrink: 0;
        }

        .logo {
            color: #2e7d32;
            padding: 10px 20px;
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            text-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            margin: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: linear-gradient(135deg, #2e7d32, #4CAF50);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo:hover {
            transform: translateY(-5px) scale(1.05);
            text-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
        }

        .subtitle {
            color: rgba(20, 97, 66, 0.95);
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 0.8rem;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Main content */
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            position: relative;
            z-index: 10;
            min-height: 0;
        }

        .login-container {
            background: var(--form-bg);
            padding: 2rem;
            border-radius: 28px;
            box-shadow: var(--shadow-heavy);
            width: 100%;
            max-width: 420px;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: slideUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color));
            border-radius: 28px 28px 0 0;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-container:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 35px 100px rgba(0, 0, 0, 0.3);
            border-color: rgba(15, 123, 15, 0.3);
        }

        .form-title {
            margin-bottom: 2.5rem;
            color: var(--text-primary);
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            position: relative;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
            box-shadow: var(--glow-green);
        }

        /* Error message */
        .error-message, .success-message {
            padding: 1.2rem 2rem;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) scale(0.8);
            opacity: 0;
            min-width: 320px;
            max-width: 90%;
            z-index: 10000;
            text-align: center;
            animation: popup 5s ease-in-out forwards;
        }

        .error-message {
            background: linear-gradient(135deg, #fee, #fdd);
            color: var(--error-color);
            border: 1px solid rgba(220, 53, 69, 0.3);
            box-shadow: 0 8px 24px rgba(220, 53, 69, 0.3);
        }

        .success-message {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.3);
            box-shadow: 0 8px 24px rgba(40, 167, 69, 0.3);
        }

        @keyframes popup {
            0% { opacity: 0; transform: translateX(-50%) scale(0.8); }
            10% { opacity: 1; transform: translateX(-50%) scale(1); }
            90% { opacity: 1; transform: translateX(-50%) scale(1); }
            100% { opacity: 0; transform: translateX(-50%) scale(0.8); }
        }

        /* Form elements */
        .form-group {
            margin-bottom: 2rem;
            position: relative;
            animation: fadeInUp 0.8s ease-out calc(0.7s + var(--delay, 0s)) both;
        }

        .form-group:nth-child(3) { --delay: 0.1s; }
        .form-group:nth-child(4) { --delay: 0.2s; }

        label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
            letter-spacing: 0.02em;
            transition: color 0.3s ease;
        }

        .input-container {
            position: relative;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            font-size: 1.05rem;
            background-color: rgba(200, 200, 200, 0.95);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: var(--focus-color);
            box-shadow: 0 0 0 4px rgba(15, 123, 15, 0.15), var(--glow-green);
            transform: translateY(-2px);
            background-color: rgba(200, 200, 200, 0.95);
        }

        .show-password {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 1.2rem;
            transition: all 0.3s ease;
            user-select: none;
        }

        .show-password:hover {
            color: var(--focus-color);
            transform: translateY(-50%) scale(1.1);
        }

        .login-btn {
            width: 100%;
            padding: 1.3rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color), var(--accent-color));
            background-size: 200% 200%;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.15rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.8px;
            margin-top: 1.5rem;
            text-transform: uppercase;
            animation: fadeInUp 0.8s ease-out 1s both;
        }

        .login-btn:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 15px 40px rgba(15, 123, 15, 0.4);
            background-position: 100% 0;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 2rem;
            font-size: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            animation: fadeInUp 0.8s ease-out 1.2s both;
        }

        .forgot-password:hover {
            color: var(--primary-hover);
            transform: translateY(-2px);
        }

        footer {
            background: rgba(0, 0, 0, 0.1);
            color: rgba(255, 255, 255, 0.95);
            text-align: center;
            padding: 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            animation: fadeInUp 0.8s ease-out 1.4s both;
            flex-shrink: 0;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .logo { font-size: 2.5rem; }
            .subtitle { font-size: 1rem; }
            .login-container { padding: 1.5rem; margin: 0.5rem; border-radius: 20px; }
            .form-title { font-size: 1.8rem; margin-bottom: 1.5rem; }
        }

        @media (max-width: 480px) {
            .logo { font-size: 2.2rem; }
            .subtitle { font-size: 0.9rem; }
            .login-container { padding: 1.2rem; border-radius: 16px; }
            .form-title { font-size: 1.6rem; }
            input[type="text"], input[type="password"] { padding: 1rem 1.25rem; font-size: 1rem; }
            .login-btn { padding: 1rem; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="particles" id="particles"></div>

    <header>
        <h1 class="logo">MediTrack</h1>
        <p class="subtitle">Cavite State University - Rosario Campus</p>
    </header>

    <div id="messageContainer">
        <?php
        // Display error messages
        $errorType = $_GET['error'] ?? '';
        $errorMessages = [
            'invalid' => '⚠️ Invalid username or password. Please try again.',
            'access' => '🔒 Access denied. Please login to continue.',
            'inactive' => '❌ Your account has been deactivated. Please contact the administrator.',
            'database' => '❗ A database error occurred. Please try again later.'
        ];
        
        if (isset($errorMessages[$errorType])) {
            echo '<div class="error-message">' . $errorMessages[$errorType] . '</div>';
        }
        
        // Display success message for logout
        if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
            echo '<div class="success-message">✓ You have been successfully logged out.</div>';
        }
        ?>
    </div>

    <main>
        <div class="login-container">
            <h2 class="form-title">Welcome Back</h2>
            <form method="POST" id="loginForm" autocomplete="off">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-container">
                        <input type="text" id="username" name="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-container">
                        <input type="password" id="password" name="password" required>
                        <span class="show-password" onclick="togglePassword()">👁️</span>
                    </div>
                </div>

                <button type="submit" class="login-btn">Sign In</button>
                <a href="#" class="forgot-password">Forgot your password?</a>
            </form>
        </div>
    </main>

    <footer>
        © 2025 Cavite State University - Rosario Campus. All rights reserved.
    </footer>

    <script>
        // ========================================
        // EMBEDDED JAVASCRIPT
        // ========================================
        
        // Create floating particles
        function createParticles() {
            const particleContainer = document.getElementById('particles');
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particleContainer.appendChild(particle);
            }
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const showPasswordSpan = document.querySelector('.show-password');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                showPasswordSpan.textContent = '👁️‍🗨️';
                showPasswordSpan.style.color = 'var(--error-color)';
            } else {
                passwordField.type = 'password';
                showPasswordSpan.textContent = '👁️';
                showPasswordSpan.style.color = 'var(--text-secondary)';
            }
        }

        // Initialize effects on page load
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();

            // Add subtle parallax effect to background shapes
            document.addEventListener('mousemove', function(e) {
                const shapes = document.querySelectorAll('.shape');
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;

                shapes.forEach((shape, index) => {
                    const speed = (index + 1) * 0.5;
                    const xPos = (x - 0.5) * speed;
                    const yPos = (y - 0.5) * speed;
                    shape.style.transform = `translate(${xPos}px, ${yPos}px)`;
                });
            });

            // Enhanced input focus effects
            const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
            inputs.forEach((input, index) => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02) translateY(-2px)';
                    this.parentElement.parentElement.querySelector('label').style.color = 'var(--focus-color)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1) translateY(0)';
                    this.parentElement.parentElement.querySelector('label').style.color = 'var(--text-primary)';
                });

                // Add staggered animation delay
                input.closest('.form-group').style.setProperty('--delay', (index * 0.1) + 's');
            });
        });

        // Clear error messages after animation
        setTimeout(function() {
            const messages = document.querySelectorAll('.error-message, .success-message');
            messages.forEach(msg => {
                setTimeout(() => msg.remove(), 5000);
            });
        }, 100);
    </script>
</body>
</html>
