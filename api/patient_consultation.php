<?php
include 'auth.php';

// Handle AJAX requests for getting patient data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_GET['action']) {
            case 'get_patient':
                $patient_id = (int)$_GET['patient_id'];
                $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
                $stmt->execute([$patient_id]);
                $patient = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$patient) {
                    throw new Exception('Patient not found');
                }
                
                echo json_encode(['success' => true, 'data' => $patient]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add_patient') {
            // Add new patient
            $stmt = $pdo->prepare("
                INSERT INTO patients (
                    patient_number, first_name, last_name, date_of_birth, gender, 
                    blood_group, phone, email, patient_address, emergency_contact_name, 
                    emergency_contact_phone, allergies, medical_conditions, 
                    admission_date, assigned_room, assigned_bed, patient_status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Generate patient number
            $patient_number = 'PT' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $stmt->execute([
                $patient_number,
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['date_of_birth'],
                $_POST['gender'],
                $_POST['blood_group'] ?: null,
                $_POST['phone'] ?: null,
                $_POST['email'] ?: null,
                $_POST['patient_address'] ?: null,
                $_POST['emergency_contact_name'] ?: null,
                $_POST['emergency_contact_phone'] ?: null,
                $_POST['allergies'] ?: null,
                $_POST['medical_conditions'] ?: null,
                $_POST['admission_date'] ?: date('Y-m-d'),
                $_POST['assigned_room'] ?: null,
                $_POST['assigned_bed'] ?: null,
                $_POST['patient_status'] ?: 'admitted'
            ]);
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (user_id, logs_item_type, logs_item_id, logs_item_name, logs_description, logs_status) 
                VALUES (?, 'patient', ?, ?, ?, 'success')
            ");
            $stmt->execute([
                $_SESSION['user_id'], 
                $pdo->lastInsertId(), 
                $_POST['first_name'] . ' ' . $_POST['last_name'],
                'Patient registered: ' . $_POST['first_name'] . ' ' . $_POST['last_name']
            ]);
            
            $_SESSION['success_message'] = "Patient registered successfully!";
            
        } elseif ($action === 'edit_patient') {
            // Edit existing patient
            $stmt = $pdo->prepare("
                UPDATE patients SET 
                    first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, 
                    blood_group = ?, phone = ?, email = ?, patient_address = ?, 
                    emergency_contact_name = ?, emergency_contact_phone = ?, 
                    allergies = ?, medical_conditions = ?, assigned_room = ?, 
                    assigned_bed = ?, patient_status = ?, updated_at = CURRENT_TIMESTAMP
                WHERE patient_id = ?
            ");
            
            $stmt->execute([
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['date_of_birth'],
                $_POST['gender'],
                $_POST['blood_group'] ?: null,
                $_POST['phone'] ?: null,
                $_POST['email'] ?: null,
                $_POST['patient_address'] ?: null,
                $_POST['emergency_contact_name'] ?: null,
                $_POST['emergency_contact_phone'] ?: null,
                $_POST['allergies'] ?: null,
                $_POST['medical_conditions'] ?: null,
                $_POST['assigned_room'] ?: null,
                $_POST['assigned_bed'] ?: null,
                $_POST['patient_status'],
                $_POST['patient_id']
            ]);
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (user_id, logs_item_type, logs_item_id, logs_item_name, logs_description, logs_status) 
                VALUES (?, 'patient', ?, ?, ?, 'success')
            ");
            $stmt->execute([
                $_SESSION['user_id'], 
                $_POST['patient_id'], 
                $_POST['first_name'] . ' ' . $_POST['last_name'],
                'Patient updated: ' . $_POST['first_name'] . ' ' . $_POST['last_name']
            ]);
            
            $_SESSION['success_message'] = "Patient updated successfully!";
            
        } elseif ($action === 'delete_patient') {
            // Get patient info before deletion for logging
            $stmt = $pdo->prepare("SELECT first_name, last_name FROM patients WHERE patient_id = ?");
            $stmt->execute([$_POST['patient_id']]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete patient
            $stmt = $pdo->prepare("DELETE FROM patients WHERE patient_id = ?");
            $stmt->execute([$_POST['patient_id']]);
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (user_id, logs_item_type, logs_item_id, logs_item_name, logs_description, logs_status) 
                VALUES (?, 'patient', ?, ?, ?, 'success')
            ");
            $stmt->execute([
                $_SESSION['user_id'], 
                $_POST['patient_id'], 
                $patient['first_name'] . ' ' . $patient['last_name'],
                'Patient deleted: ' . $patient['first_name'] . ' ' . $patient['last_name']
            ]);
            
            $_SESSION['success_message'] = "Patient deleted successfully!";
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

// Build query with search and filter
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR patient_number LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
}

if ($status_filter !== 'all') {
    $where_conditions[] = "patient_status = ?";
    $params[] = $status_filter;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get patients
$stmt = $pdo->prepare("
    SELECT * FROM patients 
    $where_clause 
    ORDER BY created_at DESC
");
$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats = [];

// Total patients
$stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
$stats['total_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Admitted patients
$stmt = $pdo->query("SELECT COUNT(*) as count FROM patients WHERE patient_status = 'admitted'");
$stats['admitted'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Discharged patients
$stmt = $pdo->query("SELECT COUNT(*) as count FROM patients WHERE patient_status = 'discharged'");
$stats['discharged'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Transferred patients
$stmt = $pdo->query("SELECT COUNT(*) as count FROM patients WHERE patient_status = 'transferred'");
$stats['transferred'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>