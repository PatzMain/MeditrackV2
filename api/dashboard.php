<?php
include 'auth.php';
// Database connection
$conn = new mysqli('localhost', 'root', '', 'meditrack_system');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Total Medicines
$medicines_count = $conn->query('SELECT COUNT(*) FROM medicines')->fetch_row()[0];
// Total Supplies
$supplies_count = $conn->query('SELECT COUNT(*) FROM supplies')->fetch_row()[0];
// Total Equipment
$equipment_count = $conn->query('SELECT COUNT(*) FROM equipment')->fetch_row()[0];
// Items Expiring Soon (medicines or supplies expiring in next 30 days)
$today = date('Y-m-d');
$soon = date('Y-m-d', strtotime('+30 days'));
$expiring_meds = $conn->query("SELECT COUNT(*) FROM medicines WHERE medicine_expiry_date IS NOT NULL AND medicine_expiry_date BETWEEN '$today' AND '$soon'")->fetch_row()[0];
$expiring_supplies = $conn->query("SELECT COUNT(*) FROM supplies WHERE supply_expiry_date IS NOT NULL AND supply_expiry_date BETWEEN '$today' AND '$soon'")->fetch_row()[0];
$expiring_soon = $expiring_meds + $expiring_supplies;
?>