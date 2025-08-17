<?php
include '../../api/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Dashboard</title>
    <?php include '../includes/styles.php'; ?>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $currentPage = 'dashboard';
        include '../includes/navbar.php';
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <?php
            $pageKey = 'dashboard';
            include '../includes/page-header.php';
            ?>

            <!-- Statistics Cards -->
            <?php
            $pageKey = 'dashboard';
            include '../includes/stats-cards.php';
            ?>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <h2>Welcome to the Dashboard</h2>
                <p>Here you can find an overview of the system's performance and key metrics.</p>
                <!-- Additional dashboard content can be added here -->
            </div>
        </main>
    </div>
    <script src="../js/navbar.js"></script>
</body>
</html>