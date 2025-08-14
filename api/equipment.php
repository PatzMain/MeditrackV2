<?php
include 'auth.php';

// Optional helper function for equipment condition color coding
function getConditionClass($condition) {
    if (!$condition) return '';
    
    $condition = strtolower($condition);
    switch ($condition) {
        case 'poor':
            return 'condition-poor';
        case 'fair':
            return 'condition-fair';
        case 'good':
            return 'condition-good';
        case 'excellent':
            return 'condition-excellent';
        default:
            return '';
    }
}

// Handle AJAX requests for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    try {
        switch ($_POST['action']) {
            case 'add_equipment':
                $stmt = $pdo->prepare("INSERT INTO equipment 
                    (equipment_name, equipment_type, serial_number, equipment_condition, equipment_location, equipment_classification, equipment_description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['equipment_name'],
                    $_POST['equipment_type'],
                    $_POST['serial_number'],
                    $_POST['equipment_condition'],
                    $_POST['equipment_location'],
                    $_POST['equipment_classification'],
                    $_POST['equipment_description']
                ]);

                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs 
                    (user_id, logs_item_type, logs_item_name, logs_description, logs_status) 
                    VALUES (?, 'equipment', ?, ?, 'added')");
                $log_stmt->execute([
                    $_SESSION['user_id'],
                    $_POST['equipment_name'],
                    "Added new equipment: {$_POST['equipment_name']}"
                ]);

                echo json_encode(['success' => true, 'message' => 'Equipment added successfully']);
                break;

            case 'edit_equipment':
                $stmt = $pdo->prepare("UPDATE equipment 
                    SET equipment_name = ?, equipment_type = ?, serial_number = ?, equipment_condition = ?, equipment_location = ?, equipment_classification = ?, equipment_description = ? 
                    WHERE equipment_id = ?");
                $stmt->execute([
                    $_POST['equipment_name'],
                    $_POST['equipment_type'],
                    $_POST['serial_number'],
                    $_POST['equipment_condition'],
                    $_POST['equipment_location'],
                    $_POST['equipment_classification'],
                    $_POST['equipment_description'],
                    $_POST['equipment_id']
                ]);

                echo json_encode(['success' => true, 'message' => 'Equipment updated successfully']);
                break;

            case 'delete_equipment':
                $equipment_id = (int)$_POST['equipment_id'];
                $stmt = $pdo->prepare("DELETE FROM equipment WHERE equipment_id = ?");
                $stmt->execute([$equipment_id]);

                echo json_encode(['success' => true, 'message' => 'Equipment deleted successfully']);
                break;

            case 'get_equipment':
                $equipment_id = (int)$_POST['equipment_id'];
                $stmt = $pdo->prepare("SELECT * FROM equipment WHERE equipment_id = ?");
                $stmt->execute([$equipment_id]);
                $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode(['success' => true, 'data' => $equipment]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Retrieve equipment from database
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

$query = "SELECT * FROM equipment WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (equipment_name LIKE ? OR serial_number LIKE ? OR equipment_location LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

// Example filters (adjust as needed)
if ($filter === 'poor-condition') {
    $query .= " AND LOWER(equipment_condition) = 'poor'";
} elseif ($filter === 'good-condition') {
    $query .= " AND LOWER(equipment_condition) = 'good'";
}

$query .= " ORDER BY equipment_name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_equipment,
    SUM(CASE WHEN LOWER(equipment_condition) = 'poor' THEN 1 ELSE 0 END) as poor_condition,
    SUM(CASE WHEN LOWER(equipment_condition) = 'good' THEN 1 ELSE 0 END) as good_condition
    FROM equipment";
$stats_result = $pdo->query($stats_query);
$stats = $stats_result->fetch(PDO::FETCH_ASSOC);
?>