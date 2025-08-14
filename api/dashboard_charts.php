<?php
// api/dashboard_charts.php
include 'auth.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'meditrack_system');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

header('Content-Type: application/json');

// Get chart type from request
$chart_type = $_GET['chart'] ?? '';
$period = $_GET['period'] ?? '30'; // days

switch($chart_type) {
    case 'inventory_overview':
        echo json_encode(getInventoryOverview($conn));
        break;
    case 'stock_levels':
        echo json_encode(getStockLevels($conn));
        break;
    case 'expiry_timeline':
        echo json_encode(getExpiryTimeline($conn));
        break;
    case 'category_distribution':
        echo json_encode(getCategoryDistribution($conn));
        break;
    case 'monthly_usage':
        echo json_encode(getMonthlyUsage($conn));
        break;
    case 'medicine_types':
        echo json_encode(getMedicineTypes($conn));
        break;
    case 'equipment_conditions':
        echo json_encode(getEquipmentConditions($conn));
        break;
    case 'supply_quantities':
        echo json_encode(getSupplyQuantities($conn));
        break;
    case 'patient_status':
        echo json_encode(getPatientStatus($conn));
        break;
    case 'vital_trends':
        echo json_encode(getVitalTrends($conn, $period));
        break;
    case 'assessment_types':
        echo json_encode(getAssessmentTypes($conn));
        break;
    case 'nursing_shifts':
        echo json_encode(getNursingShifts($conn));
        break;
    case 'activity_logs':
        echo json_encode(getActivityLogs($conn, $period));
        break;
    case 'expiry_alerts':
        echo json_encode(getExpiryAlerts($conn));
        break;
    case 'medicine_classification':
        echo json_encode(getMedicineClassification($conn));
        break;
    case 'patient_demographics':
        echo json_encode(getPatientDemographics($conn));
        break;
    default:
        echo json_encode(['error' => 'Invalid chart type']);
}

// Chart 1: Inventory Overview
function getInventoryOverview($conn) {
    $medicines = $conn->query("SELECT COUNT(*) as count FROM medicines")->fetch_assoc()['count'];
    $supplies = $conn->query("SELECT COUNT(*) as count FROM supplies")->fetch_assoc()['count'];
    $equipment = $conn->query("SELECT COUNT(*) as count FROM equipment")->fetch_assoc()['count'];
    
    return [
        'labels' => ['Medicines', 'Supplies', 'Equipment'],
        'datasets' => [[
            'data' => [$medicines, $supplies, $equipment],
            'backgroundColor' => ['#4CAF50', '#2196F3', '#FF9800'],
            'borderColor' => ['#45a049', '#1976D2', '#F57C00'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 2: Stock Levels
function getStockLevels($conn) {
    $stockLevels = $conn->query("
        SELECT 
            CASE 
                WHEN medicine_stock = 0 THEN 'Out of Stock'
                WHEN medicine_stock <= 10 THEN 'Low Stock'
                WHEN medicine_stock <= 50 THEN 'Medium Stock'
                ELSE 'High Stock'
            END as level,
            COUNT(*) as count
        FROM medicines 
        GROUP BY level
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($stockLevels, 'level');
    $data = array_column($stockLevels, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'data' => $data,
            'backgroundColor' => ['#f44336', '#FF9800', '#FFC107', '#4CAF50'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 3: Expiry Timeline
function getExpiryTimeline($conn) {
    $today = date('Y-m-d');
    $periods = [
        'Expired' => "medicine_expiry_date < '$today'",
        'This Week' => "medicine_expiry_date BETWEEN '$today' AND DATE_ADD('$today', INTERVAL 7 DAY)",
        'This Month' => "medicine_expiry_date BETWEEN DATE_ADD('$today', INTERVAL 8 DAY) AND DATE_ADD('$today', INTERVAL 30 DAY)",
        'Next 3 Months' => "medicine_expiry_date BETWEEN DATE_ADD('$today', INTERVAL 31 DAY) AND DATE_ADD('$today', INTERVAL 90 DAY)",
        'Beyond' => "medicine_expiry_date > DATE_ADD('$today', INTERVAL 90 DAY)"
    ];
    
    $labels = [];
    $data = [];
    
    foreach ($periods as $label => $condition) {
        $count = $conn->query("SELECT COUNT(*) as count FROM medicines WHERE $condition AND medicine_expiry_date IS NOT NULL")->fetch_assoc()['count'];
        $labels[] = $label;
        $data[] = $count;
    }
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Items',
            'data' => $data,
            'backgroundColor' => ['#f44336', '#FF5722', '#FF9800', '#FFC107', '#4CAF50'],
            'borderColor' => '#fff',
            'borderWidth' => 2
        ]]
    ];
}

// Chart 4: Category Distribution
function getCategoryDistribution($conn) {
    $categories = $conn->query("
        SELECT medicine_classification as category, COUNT(*) as count 
        FROM medicines 
        WHERE medicine_classification IS NOT NULL 
        GROUP BY medicine_classification 
        ORDER BY count DESC 
        LIMIT 10
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($categories, 'category');
    $data = array_column($categories, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Count',
            'data' => $data,
            'backgroundColor' => '#4CAF50',
            'borderColor' => '#45a049',
            'borderWidth' => 2
        ]]
    ];
}

// Chart 5: Monthly Usage Trend
function getMonthlyUsage($conn) {
    $months = [];
    $usage = [];
    
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $months[] = date('M Y', strtotime($month));
        
        $count = $conn->query("
            SELECT COUNT(*) as count 
            FROM activity_logs 
            WHERE logs_timestamp LIKE '$month%' 
            AND logs_item_type IN ('medicine', 'supply', 'equipment')
        ")->fetch_assoc()['count'];
        
        $usage[] = $count;
    }
    
    return [
        'labels' => $months,
        'datasets' => [[
            'label' => 'Activities',
            'data' => $usage,
            'borderColor' => '#2196F3',
            'backgroundColor' => 'rgba(33, 150, 243, 0.1)',
            'tension' => 0.4,
            'fill' => true
        ]]
    ];
}

// Chart 6: Medicine Types
function getMedicineTypes($conn) {
    $types = $conn->query("
        SELECT medicine_type, COUNT(*) as count 
        FROM medicines 
        GROUP BY medicine_type
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($types, 'medicine_type');
    $data = array_column($types, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'data' => $data,
            'backgroundColor' => ['#2196F3', '#9C27B0', '#4CAF50'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 7: Equipment Conditions
function getEquipmentConditions($conn) {
    $conditions = $conn->query("
        SELECT equipment_condition, COUNT(*) as count 
        FROM equipment 
        WHERE equipment_condition IS NOT NULL 
        GROUP BY equipment_condition
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($conditions, 'equipment_condition');
    $data = array_column($conditions, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Equipment Count',
            'data' => $data,
            'backgroundColor' => ['#4CAF50', '#FFC107', '#FF9800', '#f44336'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 8: Supply Quantities
function getSupplyQuantities($conn) {
    $supplies = $conn->query("
        SELECT supply_name, supply_quantity 
        FROM supplies 
        ORDER BY supply_quantity DESC 
        LIMIT 10
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($supplies, 'supply_name');
    $data = array_column($supplies, 'supply_quantity');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Quantity',
            'data' => $data,
            'backgroundColor' => '#FF9800',
            'borderColor' => '#F57C00',
            'borderWidth' => 2
        ]]
    ];
}

// Chart 9: Patient Status
function getPatientStatus($conn) {
    $status = $conn->query("
        SELECT patient_status, COUNT(*) as count 
        FROM patients 
        GROUP BY patient_status
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($status, 'patient_status');
    $data = array_column($status, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'data' => $data,
            'backgroundColor' => ['#4CAF50', '#2196F3', '#f44336', '#FF9800'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 10: Vital Signs Trends
function getVitalTrends($conn, $period) {
    $days = [];
    $avgBP = [];
    $avgTemp = [];
    $avgHR = [];
    
    for ($i = intval($period) - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $days[] = date('M d', strtotime($date));
        
        $vitals = $conn->query("
            SELECT 
                AVG((systolic_bp + diastolic_bp) / 2) as avg_bp,
                AVG(temperature) as avg_temp,
                AVG(heart_rate) as avg_hr
            FROM vital_signs 
            WHERE DATE(recorded_at) = '$date'
        ")->fetch_assoc();
        
        $avgBP[] = round($vitals['avg_bp'] ?? 0, 1);
        $avgTemp[] = round($vitals['avg_temp'] ?? 0, 1);
        $avgHR[] = round($vitals['avg_hr'] ?? 0, 1);
    }
    
    return [
        'labels' => $days,
        'datasets' => [
            [
                'label' => 'Avg Blood Pressure',
                'data' => $avgBP,
                'borderColor' => '#f44336',
                'backgroundColor' => 'rgba(244, 67, 54, 0.1)',
                'tension' => 0.4
            ],
            [
                'label' => 'Avg Temperature (°C)',
                'data' => $avgTemp,
                'borderColor' => '#FF9800',
                'backgroundColor' => 'rgba(255, 152, 0, 0.1)',
                'tension' => 0.4,
                'yAxisID' => 'y1'
            ],
            [
                'label' => 'Avg Heart Rate',
                'data' => $avgHR,
                'borderColor' => '#2196F3',
                'backgroundColor' => 'rgba(33, 150, 243, 0.1)',
                'tension' => 0.4
            ]
        ]
    ];
}

// Chart 11: Assessment Types
function getAssessmentTypes($conn) {
    $types = $conn->query("
        SELECT assessment_type, COUNT(*) as count 
        FROM medical_assessments 
        GROUP BY assessment_type
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($types, 'assessment_type');
    $data = array_column($types, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'data' => $data,
            'backgroundColor' => ['#2196F3', '#9C27B0', '#4CAF50', '#FF9800', '#f44336'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 12: Nursing Shifts
function getNursingShifts($conn) {
    $shifts = $conn->query("
        SELECT nursing_shift, COUNT(*) as count 
        FROM nursing_notes 
        GROUP BY nursing_shift
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($shifts, 'nursing_shift');
    $data = array_column($shifts, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Notes Count',
            'data' => $data,
            'backgroundColor' => ['#FFC107', '#FF9800', '#3F51B5'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 13: Activity Logs
function getActivityLogs($conn, $period) {
    $activities = $conn->query("
        SELECT logs_item_type, COUNT(*) as count 
        FROM activity_logs 
        WHERE logs_timestamp >= DATE_SUB(NOW(), INTERVAL $period DAY)
        AND logs_item_type IS NOT NULL
        GROUP BY logs_item_type
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($activities, 'logs_item_type');
    $data = array_column($activities, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Activities',
            'data' => $data,
            'backgroundColor' => '#4CAF50',
            'borderColor' => '#45a049',
            'borderWidth' => 2
        ]]
    ];
}

// Chart 14: Expiry Alerts
function getExpiryAlerts($conn) {
    $today = date('Y-m-d');
    $alerts = [
        'Expired' => 0,
        'Expiring This Week' => 0,
        'Expiring This Month' => 0,
        'Good' => 0
    ];
    
    // Medicine expiry alerts
    $medAlerts = $conn->query("
        SELECT 
            CASE 
                WHEN medicine_expiry_date < '$today' THEN 'Expired'
                WHEN medicine_expiry_date <= DATE_ADD('$today', INTERVAL 7 DAY) THEN 'Expiring This Week'
                WHEN medicine_expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY) THEN 'Expiring This Month'
                ELSE 'Good'
            END as alert_type,
            COUNT(*) as count
        FROM medicines 
        WHERE medicine_expiry_date IS NOT NULL
        GROUP BY alert_type
    ")->fetch_all(MYSQLI_ASSOC);
    
    foreach ($medAlerts as $alert) {
        $alerts[$alert['alert_type']] += $alert['count'];
    }
    
    // Supply expiry alerts
    $supAlerts = $conn->query("
        SELECT 
            CASE 
                WHEN supply_expiry_date < '$today' THEN 'Expired'
                WHEN supply_expiry_date <= DATE_ADD('$today', INTERVAL 7 DAY) THEN 'Expiring This Week'
                WHEN supply_expiry_date <= DATE_ADD('$today', INTERVAL 30 DAY) THEN 'Expiring This Month'
                ELSE 'Good'
            END as alert_type,
            COUNT(*) as count
        FROM supplies 
        WHERE supply_expiry_date IS NOT NULL
        GROUP BY alert_type
    ")->fetch_all(MYSQLI_ASSOC);
    
    foreach ($supAlerts as $alert) {
        $alerts[$alert['alert_type']] += $alert['count'];
    }
    
    return [
        'labels' => array_keys($alerts),
        'datasets' => [[
            'data' => array_values($alerts),
            'backgroundColor' => ['#f44336', '#FF5722', '#FF9800', '#4CAF50'],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 15: Medicine Classification
function getMedicineClassification($conn) {
    $classifications = $conn->query("
        SELECT medicine_classification, COUNT(*) as count 
        FROM medicines 
        WHERE medicine_classification IS NOT NULL 
        GROUP BY medicine_classification 
        ORDER BY count DESC 
        LIMIT 8
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($classifications, 'medicine_classification');
    $data = array_column($classifications, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Count',
            'data' => $data,
            'backgroundColor' => [
                '#4CAF50', '#2196F3', '#FF9800', '#9C27B0',
                '#f44336', '#FF5722', '#795548', '#607D8B'
            ],
            'borderWidth' => 2
        ]]
    ];
}

// Chart 16: Patient Demographics
function getPatientDemographics($conn) {
    $demographics = $conn->query("
        SELECT 
            CASE 
                WHEN YEAR(CURDATE()) - YEAR(date_of_birth) < 18 THEN 'Under 18'
                WHEN YEAR(CURDATE()) - YEAR(date_of_birth) BETWEEN 18 AND 30 THEN '18-30'
                WHEN YEAR(CURDATE()) - YEAR(date_of_birth) BETWEEN 31 AND 50 THEN '31-50'
                WHEN YEAR(CURDATE()) - YEAR(date_of_birth) BETWEEN 51 AND 70 THEN '51-70'
                ELSE 'Over 70'
            END as age_group,
            COUNT(*) as count
        FROM patients 
        GROUP BY age_group
    ")->fetch_all(MYSQLI_ASSOC);
    
    $labels = array_column($demographics, 'age_group');
    $data = array_column($demographics, 'count');
    
    return [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Patients',
            'data' => $data,
            'backgroundColor' => ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#f44336'],
            'borderWidth' => 2
        ]]
    ];
}

$conn->close();
?>