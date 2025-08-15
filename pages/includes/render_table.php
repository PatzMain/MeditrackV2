<?php
// Include the config file
$config = include '../../config/table_config.php';
$table = $config[$tableKey];
/** 
 * Example: get data from your database
 * Replace this with your real query
 */
try {
    $pdo = new PDO("mysql:host=localhost;dbname=meditrack_system;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM {$table['table_name']} ORDER BY {$table['default_sort']['column']} {$table['default_sort']['direction']}");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Render table
echo '<table class="table">';
echo '<thead><tr>';

// Table headers
foreach ($table['columns'] as $key => $col) {
    if (!empty($col['visible'])) {
        $class = !empty($col['sortable']) ? 'sortable' : '';
        $width = !empty($col['width']) ? ' style="width:' . $col['width'] . '"' : '';
        echo "<th class='{$class}'{$width}>{$col['label']}</th>";
    }
}

// Optional: Actions header
if (!empty($table['actions'])) {
    echo '<th>Actions</th>';
}

echo '</tr></thead>';
echo '<tbody>';

// Table rows
if (!empty($data)) {
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($table['columns'] as $key => $col) {
            if (!empty($col['visible'])) {
                $value = isset($row[$key]) ? htmlspecialchars($row[$key]) : '';

                // Example: apply custom format if defined
                if (!empty($col['format']) && $col['format'] === 'stock_status') {
                    if ($value <= $col['low_stock_threshold']) {
                        $value = "<span class='badge bg-warning'>Low ({$value})</span>";
                    } else {
                        $value = "<span class='badge bg-success'>{$value}</span>";
                    }
                }

                echo "<td>{$value}</td>";
            }
        }

        // Actions buttons
        if (!empty($table['actions'])) {
            echo '<td>';
            foreach ($table['actions'] as $actionKey => $action) {
                echo "<button class='{$action['class']}' title='{$action['label']}'><i class='fa {$action['icon']}'></i></button> ";
            }
            echo '</td>';
        }

        echo '</tr>';
    }
} else {
    echo "<tr><td colspan='" . (count(array_filter($table['columns'], fn($c) => !empty($c['visible']))) + 1) . "' class='text-center'>No records found</td></tr>";
}

echo '</tbody></table>';
?>
