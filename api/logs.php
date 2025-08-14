<?php
include 'auth.php';

// Handle AJAX requests for CRUD operations on logs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    try {
        switch ($_POST['action']) {
            case 'delete_log':
                // Only allow superadmins to delete logs
                if ($_SESSION['role'] !== 'superadmin') {
                    throw new Exception('Unauthorized: Only superadmins can delete logs');
                }

                $log_id = (int)$_POST['log_id'];
                $stmt = $pdo->prepare("DELETE FROM activity_logs WHERE log_id = ?");
                $stmt->execute([$log_id]);

                // Log the deletion action
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_item_id, logs_description) VALUES (?, 'system', ?, ?)");
                $log_stmt->execute([$_SESSION['user_id'], $log_id, "Log entry deleted by " . $_SESSION['username']]);

                echo json_encode(['success' => true, 'message' => 'Log deleted successfully']);
                break;

            case 'get_log':
                $log_id = (int)$_POST['log_id'];
                $stmt = $pdo->prepare("SELECT al.*, u.username, 
                    CONCAT(p.first_name, ' ', p.last_name) as patient_name
                    FROM activity_logs al
                    LEFT JOIN users u ON al.user_id = u.user_id
                    LEFT JOIN patients p ON al.patient_id = p.patient_id
                    WHERE al.log_id = ?");
                $stmt->execute([$log_id]);
                $log = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$log) {
                    throw new Exception('Log not found');
                }

                echo json_encode(['success' => true, 'data' => $log]);
                break;

            case 'export_logs':
                // Get filtered logs for export
                $search = $_POST['search'] ?? '';
                $filter = $_POST['filter'] ?? 'all';
                $date_filter = $_POST['date_filter'] ?? 'all';
                $start_date = $_POST['start_date'] ?? '';
                $end_date = $_POST['end_date'] ?? '';

                $query = "SELECT al.*, u.username, 
                    CONCAT(p.first_name, ' ', p.last_name) as patient_name
                    FROM activity_logs al
                    LEFT JOIN users u ON al.user_id = u.user_id
                    LEFT JOIN patients p ON al.patient_id = p.patient_id
                    WHERE 1=1";
                $params = [];

                // Apply filters (same logic as below)
                if (!empty($search)) {
                    $query .= " AND (al.logs_item_name LIKE ? OR al.logs_description LIKE ? OR u.username LIKE ?)";
                    $params[] = "%{$search}%";
                    $params[] = "%{$search}%";
                    $params[] = "%{$search}%";
                }

                if ($filter !== 'all' && !empty($filter)) {
                    $query .= " AND al.logs_item_type = ?";
                    $params[] = $filter;
                }

                // Date filtering
                if ($date_filter !== 'all') {
                    switch ($date_filter) {
                        case 'today':
                            $query .= " AND DATE(al.logs_timestamp) = CURDATE()";
                            break;
                        case 'week':
                            $query .= " AND al.logs_timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                            break;
                        case 'month':
                            $query .= " AND al.logs_timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                            break;
                        case 'custom':
                            if (!empty($start_date) && !empty($end_date)) {
                                $query .= " AND DATE(al.logs_timestamp) BETWEEN ? AND ?";
                                $params[] = $start_date;
                                $params[] = $end_date;
                            }
                            break;
                    }
                }

                $query .= " ORDER BY al.logs_timestamp DESC";

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $export_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(['success' => true, 'data' => $export_logs]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Helper function to get status class for display
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'success':
        case 'completed':
        case 'approved':
            return 'success';
        case 'pending':
        case 'in-progress':
            return 'warning';
        case 'failed':
        case 'error':
        case 'rejected':
            return 'danger';
        default:
            return 'default';
    }
}

// Retrieve logs from database for page load
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';
$date_filter = $_GET['date_filter'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$query = "SELECT al.*, u.username, 
    CONCAT(p.first_name, ' ', p.last_name) as patient_name
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.user_id
    LEFT JOIN patients p ON al.patient_id = p.patient_id
    WHERE 1=1";
$params = [];

// Search by description, item name, or username
if (!empty($search)) {
    $query .= " AND (al.logs_item_name LIKE ? OR al.logs_description LIKE ? OR u.username LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

// Filter by activity type
if ($filter !== 'all' && !empty($filter)) {
    $query .= " AND al.logs_item_type = ?";
    $params[] = $filter;
}

// Date filtering
if ($date_filter !== 'all') {
    switch ($date_filter) {
        case 'today':
            $query .= " AND DATE(al.logs_timestamp) = CURDATE()";
            break;
        case 'week':
            $query .= " AND al.logs_timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND al.logs_timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case 'custom':
            if (!empty($start_date) && !empty($end_date)) {
                $query .= " AND DATE(al.logs_timestamp) BETWEEN ? AND ?";
                $params[] = $start_date;
                $params[] = $end_date;
            }
            break;
    }
}

$query .= " ORDER BY al.logs_timestamp DESC LIMIT 1000"; // Limit for performance

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get comprehensive statistics
$stats_query = "SELECT 
    COUNT(*) as total_logs,
    SUM(CASE WHEN DATE(logs_timestamp) = CURDATE() THEN 1 ELSE 0 END) as today_logs,
    SUM(CASE WHEN logs_item_type = 'medicine' THEN 1 ELSE 0 END) as medicine_logs,
    SUM(CASE WHEN logs_item_type = 'supply' THEN 1 ELSE 0 END) as supply_logs,
    SUM(CASE WHEN logs_item_type = 'equipment' THEN 1 ELSE 0 END) as equipment_logs,
    SUM(CASE WHEN logs_item_type = 'patient' OR logs_item_type = 'vital_signs' OR logs_item_type = 'assessment' THEN 1 ELSE 0 END) as patient_logs,
    SUM(CASE WHEN logs_item_type = 'authentication' THEN 1 ELSE 0 END) as auth_logs,
    SUM(CASE WHEN logs_item_type = 'system' THEN 1 ELSE 0 END) as system_logs
    FROM activity_logs";

$stats_result = $pdo->query($stats_query);
$stats = $stats_result->fetch(PDO::FETCH_ASSOC);

// Get list of users for filtering (optional)
$users_query = "SELECT DISTINCT u.user_id, u.username 
    FROM users u 
    INNER JOIN activity_logs al ON u.user_id = al.user_id 
    ORDER BY u.username";
$users_result = $pdo->query($users_query);
$users = $users_result->fetchAll(PDO::FETCH_ASSOC);
?>