<?php
// medicines.config.php
// Table configuration and rendering for medicines list

if (!function_exists('getExpiryClass')) {
    function getExpiryClass($date) {
        $now = strtotime('today');
        $expiry = strtotime($date);
        if (!$expiry) return 'invalid-date';
        if ($expiry < $now) return 'expired';
        if ($expiry <= strtotime('+30 days')) return 'near-expiry';
        return 'valid';
    }
}

$config = [
    'medicines' => [
        'thClass' => 'table-header', // Base class for all table headers
        'columns' => [
            [
                'label' => 'Medicine Name',
                'key' => 'medicine_name',
                'sortable' => true,
                'callback' => function ($value, $row) {
                    $html = '<a href="#" class="medicine-name-link" 
                                onclick=\'showDescription(event, ' . json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) . ')\'>' 
                                . htmlspecialchars($value ?: 'N/A') . '</a>';
                    if (!empty($row['medicine_brand_name'])) {
                        $html .= '<div class="medicine-brand">' . htmlspecialchars($row['medicine_brand_name']) . '</div>';
                    }
                    return $html;
                }
            ],
            [
                'label' => 'Type',
                'key' => 'medicine_type',
                'sortable' => true,
                'callback' => function ($value) {
                    return '<span class="type-badge ' . strtolower($value) . '">' 
                            . htmlspecialchars($value ?: 'N/A') . '</span>';
                }
            ],
            [
                'label' => 'Dosage',
                'key' => 'medicine_dosage',
                'sortable' => false,
                'callback' => function ($value, $row) {
                    return htmlspecialchars($value ?: 'N/A') . 
                           (!empty($row['medicine_unit']) ? ' ' . htmlspecialchars($row['medicine_unit']) : '');
                }
            ],
            [
                'label' => 'Stock',
                'key' => 'medicine_stock',
                'sortable' => true,
                'callback' => function ($value, $row) {
                    $stock = $value ?? 0;
                    $stockClass = $stock <= 10 ? 'low-stock' : '';
                    return '<span class="stock-count ' . $stockClass . '">' . $stock . '</span> ' .
                           '<span class="stock-unit">' . (!empty($row['medicine_unit']) ? htmlspecialchars($row['medicine_unit']) : 'units') . '</span>';
                }
            ],
            [
                'label' => 'Expiry Date',
                'key' => 'medicine_expiry_date',
                'sortable' => true,
                'callback' => function ($value) {
                    if (empty($value)) return 'N/A';
                    return '<span class="expiry-date ' . getExpiryClass($value) . '">' 
                            . date('M d, Y', strtotime($value)) . '</span>';
                }
            ],
        ],
        'actions' => [
            [
                'label' => 'Edit',
                'icon' => '✏️',
                'onclick' => function ($row) {
                    return "editMedicine({$row['medicine_id']})";
                }
            ],
            [
                'label' => 'Delete',
                'icon' => '🗑',
                'onclick' => function ($row) {
                    return "deleteMedicine({$row['medicine_id']})";
                }
            ]
        ],
        'emptyMessage' => 'No medicines found'
    ]
];

/**
 * Render the medicines table
 */
if (!function_exists('renderMedicinesTable')) {
    function renderMedicinesTable(array $data, array $tableConfig)
    {
        echo "<table class='medicines-table'>";
        echo "<thead><tr>";

        // Table headers
        foreach ($tableConfig['columns'] as $col) {
            $classes = [$tableConfig['thClass']];
            if (!empty($col['sortable'])) {
                $classes[] = 'sortable';
            }
            echo "<th class='" . implode(' ', $classes) . "'>{$col['label']}</th>";
        }

        // Actions header
        if (!empty($tableConfig['actions'])) {
            echo "<th class='{$tableConfig['thClass']}'>Actions</th>";
        }

        echo "</tr></thead>";
        echo "<tbody>";

        if (empty($data)) {
            echo "<tr><td colspan='" . (count($tableConfig['columns']) + 1) . "' class='empty-message'>{$tableConfig['emptyMessage']}</td></tr>";
        } else {
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($tableConfig['columns'] as $col) {
                    $value = $row[$col['key']] ?? null;
                    echo "<td>" . call_user_func($col['callback'], $value, $row) . "</td>";
                }

                // Actions
                if (!empty($tableConfig['actions'])) {
                    echo "<td class='table-actions'>";
                    foreach ($tableConfig['actions'] as $action) {
                        $onclick = call_user_func($action['onclick'], $row);
                        echo "<button onclick=\"$onclick\">{$action['icon']} {$action['label']}</button> ";
                    }
                    echo "</td>";
                }

                echo "</tr>";
            }
        }

        echo "</tbody></table>";
    }
}

return $config;
