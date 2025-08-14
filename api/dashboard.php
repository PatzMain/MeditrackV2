<?php
include 'auth.php';

// Database connection using PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meditrack_system;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Total Medicines
$stmt = $pdo->query('SELECT COUNT(*) FROM medicines');
$medicines_count = $stmt->fetchColumn();

// Total Supplies
$stmt = $pdo->query('SELECT COUNT(*) FROM supplies');
$supplies_count = $stmt->fetchColumn();

// Total Equipment
$stmt = $pdo->query('SELECT COUNT(*) FROM equipment');
$equipment_count = $stmt->fetchColumn();

// Items Expiring Soon (medicines or supplies expiring in next 30 days)
$today = date('Y-m-d');
$soon = date('Y-m-d', strtotime('+30 days'));

$stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines WHERE medicine_expiry_date IS NOT NULL AND medicine_expiry_date BETWEEN ? AND ?");
$stmt->execute([$today, $soon]);
$expiring_meds = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM supplies WHERE supply_expiry_date IS NOT NULL AND supply_expiry_date BETWEEN ? AND ?");
$stmt->execute([$today, $soon]);
$expiring_supplies = $stmt->fetchColumn();

$expiring_soon = $expiring_meds + $expiring_supplies;
?>