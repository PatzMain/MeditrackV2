<?php
session_start();
include '../api/login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Login</title>
    <link rel="stylesheet" href="login.css">
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
                <a href="" class="forgot-password">Forgot your password?</a>
            </form>
        </div>
    </main>

    <footer>
        © 2025 Cavite State University - Rosario Campus. All rights reserved.
    </footer>

    <script src="login.js"></script>
</body>
</html>
