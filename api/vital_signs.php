<?php 
include 'auth.php';

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$patient_filter = $_GET['patient_filter'] ?? 'all';
$date_filter = $_GET['date_filter'] ?? '';

try {
    // Get statistics
    $stats_query = "
        SELECT 
            COUNT(DISTINCT v.patient_id) as total_patients_monitored,
            COUNT(*) as total_records,
            COUNT(CASE WHEN DATE(v.recorded_at) = CURDATE() THEN 1 END) as today_records,
            COUNT(CASE WHEN v.recorded_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_records
        FROM vital_signs v
        INNER JOIN patients p ON v.patient_id = p.patient_id
    ";
    $stats_stmt = $pdo->query($stats_query);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    // Get all patients for dropdown
    $patients_query = "
        SELECT patient_id, patient_number, first_name, last_name, patient_status 
        FROM patients 
        WHERE patient_status IN ('admitted', 'transferred')
        ORDER BY patient_number
    ";
    $patients_stmt = $pdo->query($patients_query);
    $all_patients = $patients_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build main query with filters
    $where_conditions = [];
    $params = [];

    if (!empty($search)) {
        $where_conditions[] = "(
            p.patient_number LIKE :search OR 
            p.first_name LIKE :search OR 
            p.last_name LIKE :search OR
            v.notes LIKE :search
        )";
        $params['search'] = '%' . $search . '%';
    }

    if ($patient_filter !== 'all') {
        $where_conditions[] = "v.patient_id = :patient_filter";
        $params['patient_filter'] = $patient_filter;
    }

    if (!empty($date_filter)) {
        $where_conditions[] = "DATE(v.recorded_at) = :date_filter";
        $params['date_filter'] = $date_filter;
    }

    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }

    // Get vital signs records
    $vital_signs_query = "
        SELECT 
            v.*,
            p.patient_number,
            p.first_name,
            p.last_name,
            p.patient_status,
            p.assigned_room,
            p.assigned_bed
        FROM vital_signs v
        INNER JOIN patients p ON v.patient_id = p.patient_id
        $where_clause
        ORDER BY v.recorded_at DESC, p.patient_number
        LIMIT 100
    ";

    $stmt = $pdo->prepare($vital_signs_query);
    $stmt->execute($params);
    $vital_signs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_vital_signs':
                    try {
                        $insert_query = "
                            INSERT INTO vital_signs (
                                patient_id, systolic_bp, diastolic_bp, heart_rate, 
                                respiratory_rate, temperature, temperature_unit, 
                                oxygen_saturation, blood_glucose, v_weight, v_height,
                                pain_scale, consciousness_level, notes
                            ) VALUES (
                                :patient_id, :systolic_bp, :diastolic_bp, :heart_rate,
                                :respiratory_rate, :temperature, :temperature_unit,
                                :oxygen_saturation, :blood_glucose, :v_weight, :v_height,
                                :pain_scale, :consciousness_level, :notes
                            )
                        ";

                        $insert_stmt = $pdo->prepare($insert_query);
                        $result = $insert_stmt->execute([
                            'patient_id' => $_POST['patient_id'],
                            'systolic_bp' => !empty($_POST['systolic_bp']) ? $_POST['systolic_bp'] : null,
                            'diastolic_bp' => !empty($_POST['diastolic_bp']) ? $_POST['diastolic_bp'] : null,
                            'heart_rate' => !empty($_POST['heart_rate']) ? $_POST['heart_rate'] : null,
                            'respiratory_rate' => !empty($_POST['respiratory_rate']) ? $_POST['respiratory_rate'] : null,
                            'temperature' => !empty($_POST['temperature']) ? $_POST['temperature'] : null,
                            'temperature_unit' => $_POST['temperature_unit'] ?? 'C',
                            'oxygen_saturation' => !empty($_POST['oxygen_saturation']) ? $_POST['oxygen_saturation'] : null,
                            'blood_glucose' => !empty($_POST['blood_glucose']) ? $_POST['blood_glucose'] : null,
                            'v_weight' => !empty($_POST['v_weight']) ? $_POST['v_weight'] : null,
                            'v_height' => !empty($_POST['v_height']) ? $_POST['v_height'] : null,
                            'pain_scale' => !empty($_POST['pain_scale']) ? $_POST['pain_scale'] : null,
                            'consciousness_level' => $_POST['consciousness_level'] ?? null,
                            'notes' => $_POST['notes'] ?? null
                        ]);

                        if ($result) {
                            // Log the activity
                            $log_query = "
                                INSERT INTO activity_logs (user_id, patient_id, logs_item_type, logs_description, logs_timestamp) 
                                VALUES (:user_id, :patient_id, 'vital_signs', :description, NOW())
                            ";
                            $log_stmt = $pdo->prepare($log_query);
                            $log_stmt->execute([
                                'user_id' => $user_id,
                                'patient_id' => $_POST['patient_id'],
                                'description' => 'Added vital signs record'
                            ]);

                            $_SESSION['success_message'] = 'Vital signs recorded successfully!';
                        } else {
                            $_SESSION['error_message'] = 'Failed to record vital signs.';
                        }
                    } catch (PDOException $e) {
                        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
                    }
                    break;

                case 'edit_vital_signs':
                    try {
                        $update_query = "
                            UPDATE vital_signs SET 
                                systolic_bp = :systolic_bp, diastolic_bp = :diastolic_bp,
                                heart_rate = :heart_rate, respiratory_rate = :respiratory_rate,
                                temperature = :temperature, temperature_unit = :temperature_unit,
                                oxygen_saturation = :oxygen_saturation, blood_glucose = :blood_glucose,
                                v_weight = :v_weight, v_height = :v_height,
                                pain_scale = :pain_scale, consciousness_level = :consciousness_level,
                                notes = :notes
                            WHERE vital_id = :vital_id
                        ";

                        $update_stmt = $pdo->prepare($update_query);
                        $result = $update_stmt->execute([
                            'vital_id' => $_POST['vital_id'],
                            'systolic_bp' => !empty($_POST['systolic_bp']) ? $_POST['systolic_bp'] : null,
                            'diastolic_bp' => !empty($_POST['diastolic_bp']) ? $_POST['diastolic_bp'] : null,
                            'heart_rate' => !empty($_POST['heart_rate']) ? $_POST['heart_rate'] : null,
                            'respiratory_rate' => !empty($_POST['respiratory_rate']) ? $_POST['respiratory_rate'] : null,
                            'temperature' => !empty($_POST['temperature']) ? $_POST['temperature'] : null,
                            'temperature_unit' => $_POST['temperature_unit'] ?? 'C',
                            'oxygen_saturation' => !empty($_POST['oxygen_saturation']) ? $_POST['oxygen_saturation'] : null,
                            'blood_glucose' => !empty($_POST['blood_glucose']) ? $_POST['blood_glucose'] : null,
                            'v_weight' => !empty($_POST['v_weight']) ? $_POST['v_weight'] : null,
                            'v_height' => !empty($_POST['v_height']) ? $_POST['v_height'] : null,
                            'pain_scale' => !empty($_POST['pain_scale']) ? $_POST['pain_scale'] : null,
                            'consciousness_level' => $_POST['consciousness_level'] ?? null,
                            'notes' => $_POST['notes'] ?? null
                        ]);

                        if ($result) {
                            // Log the activity
                            $log_query = "
                                INSERT INTO activity_logs (user_id, patient_id, logs_item_type, logs_item_id, logs_description, logs_timestamp) 
                                VALUES (:user_id, :patient_id, 'vital_signs', :vital_id, :description, NOW())
                            ";
                            $log_stmt = $pdo->prepare($log_query);
                            $log_stmt->execute([
                                'user_id' => $user_id,
                                'patient_id' => $_POST['patient_id'] ?? null,
                                'vital_id' => $_POST['vital_id'],
                                'description' => 'Updated vital signs record'
                            ]);

                            $_SESSION['success_message'] = 'Vital signs updated successfully!';
                        } else {
                            $_SESSION['error_message'] = 'Failed to update vital signs.';
                        }
                    } catch (PDOException $e) {
                        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
                    }
                    break;

                case 'delete_vital_signs':
                    try {
                        // Get vital signs info for logging
                        $vital_info_query = "SELECT patient_id FROM vital_signs WHERE vital_id = :vital_id";
                        $vital_info_stmt = $pdo->prepare($vital_info_query);
                        $vital_info_stmt->execute(['vital_id' => $_POST['vital_id']]);
                        $vital_info = $vital_info_stmt->fetch(PDO::FETCH_ASSOC);

                        $delete_query = "DELETE FROM vital_signs WHERE vital_id = :vital_id";
                        $delete_stmt = $pdo->prepare($delete_query);
                        $result = $delete_stmt->execute(['vital_id' => $_POST['vital_id']]);

                        if ($result) {
                            // Log the activity
                            $log_query = "
                                INSERT INTO activity_logs (user_id, patient_id, logs_item_type, logs_item_id, logs_description, logs_timestamp) 
                                VALUES (:user_id, :patient_id, 'vital_signs', :vital_id, :description, NOW())
                            ";
                            $log_stmt = $pdo->prepare($log_query);
                            $log_stmt->execute([
                                'user_id' => $user_id,
                                'patient_id' => $vital_info['patient_id'] ?? null,
                                'vital_id' => $_POST['vital_id'],
                                'description' => 'Deleted vital signs record'
                            ]);

                            $_SESSION['success_message'] = 'Vital signs record deleted successfully!';
                        } else {
                            $_SESSION['error_message'] = 'Failed to delete vital signs record.';
                        }
                    } catch (PDOException $e) {
                        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
                    }
                    break;
            }
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database connection failed: ' . $e->getMessage();
    $vital_signs = [];
    $all_patients = [];
    $stats = ['total_patients_monitored' => 0, 'total_records' => 0, 'today_records' => 0, 'week_records' => 0];
}
?>