<?php
include 'auth.php';

// Calculate days until expiry for color coding
function getExpiryClass($expiryDate) {
    if (!$expiryDate) return '';
    
    $today = new DateTime();
    $expiry = new DateTime($expiryDate);
    $interval = $today->diff($expiry);
    $days = $interval->format('%r%a');
    
    if ($days < 0) return 'expired';
    if ($days <= 30) return 'expiry-critical';
    if ($days <= 90) return 'expiry-warning';
    if ($days <= 180) return 'expiry-caution';
    return 'expiry-good';
}

// Handle AJAX requests for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'add_medicine':
                // Validate required fields
                $required_fields = ['medicine_name', 'medicine_type', 'medicine_dosage', 'medicine_unit', 'medicine_stock'];
                foreach ($required_fields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Field '$field' is required");
                    }
                }
                
                $stmt = $pdo->prepare("INSERT INTO medicines (medicine_name, medicine_type, medicine_dosage, medicine_unit, medicine_stock, medicine_expiry_date, medicine_classification, medicine_brand_name, medicine_generic_name, medicine_description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    trim($_POST['medicine_name']),
                    trim($_POST['medicine_type']),
                    trim($_POST['medicine_dosage']),
                    trim($_POST['medicine_unit']),
                    (int)$_POST['medicine_stock'],
                    !empty($_POST['medicine_expiry_date']) ? $_POST['medicine_expiry_date'] : null,
                    !empty($_POST['medicine_classification']) ? trim($_POST['medicine_classification']) : null,
                    !empty($_POST['medicine_brand_name']) ? trim($_POST['medicine_brand_name']) : null,
                    !empty($_POST['medicine_generic_name']) ? trim($_POST['medicine_generic_name']) : null,
                    !empty($_POST['medicine_description']) ? trim($_POST['medicine_description']) : null
                ]);
                
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_item_name, logs_description, logs_quantity, logs_status, created_at) VALUES (?, 'medicine', ?, ?, ?, 'added', NOW())");
                $log_stmt->execute([$_SESSION['user_id'], $_POST['medicine_name'], "Added new medicine: {$_POST['medicine_name']}", $_POST['medicine_stock']]);
                
                echo json_encode(['success' => true, 'message' => 'Medicine added successfully']);
                break;
                
            case 'get_medicine':
                if (empty($_POST['medicine_id'])) {
                    throw new Exception("Medicine ID is required");
                }
                
                $medicine_id = (int)$_POST['medicine_id'];
                if ($medicine_id <= 0) {
                    throw new Exception("Invalid medicine ID");
                }
                
                $stmt = $pdo->prepare("SELECT * FROM medicines WHERE medicine_id = ?");
                $stmt->execute([$medicine_id]);
                $medicine = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$medicine) {
                    throw new Exception("Medicine not found");
                }
                
                echo json_encode(['success' => true, 'data' => $medicine]);
                break;
                
            case 'edit_medicine':
                // Validate required fields
                $required_fields = ['medicine_id', 'medicine_name', 'medicine_type', 'medicine_dosage', 'medicine_unit', 'medicine_stock'];
                foreach ($required_fields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Field '$field' is required");
                    }
                }
                
                $medicine_id = (int)$_POST['medicine_id'];
                if ($medicine_id <= 0) {
                    throw new Exception("Invalid medicine ID");
                }
                
                $stmt = $pdo->prepare("UPDATE medicines SET medicine_name = ?, medicine_type = ?, medicine_dosage = ?, medicine_unit = ?, medicine_stock = ?, medicine_expiry_date = ?, medicine_classification = ?, medicine_brand_name = ?, medicine_generic_name = ?, medicine_description = ?, updated_at = NOW() WHERE medicine_id = ?");
                $result = $stmt->execute([
                    trim($_POST['medicine_name']),
                    trim($_POST['medicine_type']),
                    trim($_POST['medicine_dosage']),
                    trim($_POST['medicine_unit']),
                    (int)$_POST['medicine_stock'],
                    !empty($_POST['medicine_expiry_date']) ? $_POST['medicine_expiry_date'] : null,
                    !empty($_POST['medicine_classification']) ? trim($_POST['medicine_classification']) : null,
                    !empty($_POST['medicine_brand_name']) ? trim($_POST['medicine_brand_name']) : null,
                    !empty($_POST['medicine_generic_name']) ? trim($_POST['medicine_generic_name']) : null,
                    !empty($_POST['medicine_description']) ? trim($_POST['medicine_description']) : null,
                    $medicine_id
                ]);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception("Medicine not found or no changes made");
                }
                
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_item_name, logs_description, logs_quantity, logs_status, created_at) VALUES (?, 'medicine', ?, ?, ?, 'updated', NOW())");
                $log_stmt->execute([$_SESSION['user_id'], $_POST['medicine_name'], "Updated medicine: {$_POST['medicine_name']}", $_POST['medicine_stock']]);
                
                echo json_encode(['success' => true, 'message' => 'Medicine updated successfully']);
                break;
                
            case 'delete_medicine':
                if (empty($_POST['medicine_id'])) {
                    throw new Exception("Medicine ID is required");
                }
                
                $medicine_id = (int)$_POST['medicine_id'];
                if ($medicine_id <= 0) {
                    throw new Exception("Invalid medicine ID");
                }
                
                // Get medicine name for logging before deletion
                $name_stmt = $pdo->prepare("SELECT medicine_name FROM medicines WHERE medicine_id = ?");
                $name_stmt->execute([$medicine_id]);
                $medicine_name = $name_stmt->fetchColumn();
                
                if (!$medicine_name) {
                    throw new Exception("Medicine not found");
                }
                
                $stmt = $pdo->prepare("DELETE FROM medicines WHERE medicine_id = ?");
                $result = $stmt->execute([$medicine_id]);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception("Medicine not found");
                }
                
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, logs_item_type, logs_item_name, logs_description, logs_quantity, logs_status, created_at) VALUES (?, 'medicine', ?, ?, 0, 'deleted', NOW())");
                $log_stmt->execute([$_SESSION['user_id'], $medicine_name, "Deleted medicine: {$medicine_name}"]);
                
                echo json_encode(['success' => true, 'message' => 'Medicine deleted successfully']);
                break;
                
            default:
                throw new Exception("Invalid action");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>