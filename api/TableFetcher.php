<?php
/**
 * TableFetcher Class
 * Handles all database operations for fetching table data
 */

require_once 'connection.php';
require_once 'table_config.php'; // Adjust path as necessary

class TableFetcher
{
    private $pdo;
    private $tableConfig;

    public function __construct()
    {
        // Get PDO instance from connection.php
        $this->pdo = $GLOBALS['pdo'] ?? null;
        if (!$this->pdo) {
            // Fallback to create connection if not available globally
            require_once 'connection.php';
            $this->pdo = $GLOBALS['pdo'];
        }

        // Load table configuration
        $this->tableConfig = $GLOBALS['tableConfig'] ?? [];
    }

    /**
     * Fetch data from specified table with filters, search, sorting and pagination
     */
    public function fetchTableData($tableName, $options = [])
    {
        $defaults = [
            'search' => '',
            'filters' => [],
            'orderBy' => '',
            'orderDir' => 'ASC',
            'limit' => 25,
            'offset' => 0,
            'columns' => '*'
        ];

        $options = array_merge($defaults, $options);

        // Validate table exists in config
        if (!isset($this->tableConfig[$tableName])) {
            throw new Exception("Table configuration not found for: {$tableName}");
        }

        $config = $this->tableConfig[$tableName];
        $actualTableName = $config['table'] ?? $tableName;

        // Build query
        $query = $this->buildQuery($actualTableName, $config, $options);
        $countQuery = $this->buildCountQuery($actualTableName, $config, $options);

        try {
            // Get total count
            $countStmt = $this->pdo->prepare($countQuery['sql']);
            $countStmt->execute($countQuery['params']);
            $totalCount = $countStmt->fetchColumn();

            // Get data
            $stmt = $this->pdo->prepare($query['sql']);
            $stmt->execute($query['params']);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'total' => $totalCount,
                'filtered' => count($data),
                'page' => ($options['limit'] > 0) ? floor($options['offset'] / $options['limit']) + 1 : 1,
                'pages' => ($options['limit'] > 0) ? ceil($totalCount / $options['limit']) : 1
            ];


        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Build SELECT query with all conditions
     */
    private function buildQuery($tableName, $config, $options)
    {
        $params = [];
        $conditions = [];

        // Select columns
        $columns = $options['columns'];
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        $sql = "SELECT {$columns} FROM {$tableName}";

        // Add search conditions
        if (!empty($options['search']) && !empty($config['searchable'])) {
            $searchConditions = [];
            foreach ($config['searchable'] as $column) {
                $searchConditions[] = "{$column} LIKE :search";
            }
            if (!empty($searchConditions)) {
                $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
                $params[':search'] = '%' . $options['search'] . '%';
            }
        }

        // Add filter conditions
        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $column => $value) {
                if ($value !== '' && $value !== null) {
                    // Check if column is filterable
                    if (isset($config['filterable']) && in_array($column, $config['filterable'])) {
                        if (is_array($value)) {
                            // Handle array values (for IN clause)
                            $placeholders = [];
                            foreach ($value as $i => $v) {
                                $placeholder = ":filter_{$column}_{$i}";
                                $placeholders[] = $placeholder;
                                $params[$placeholder] = $v;
                            }
                            $conditions[] = "{$column} IN (" . implode(',', $placeholders) . ")";
                        } else {
                            $conditions[] = "{$column} = :filter_{$column}";
                            $params[":filter_{$column}"] = $value;
                        }
                    }
                }
            }
        }

        // Add WHERE clause if conditions exist
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Add ORDER BY
        if (!empty($options['orderBy'])) {
            $orderDir = strtoupper($options['orderDir']) === 'DESC' ? 'DESC' : 'ASC';
            // Validate column exists in sortable list
            if (isset($config['sortable']) && in_array($options['orderBy'], $config['sortable'])) {
                $sql .= " ORDER BY {$options['orderBy']} {$orderDir}";
            } elseif (isset($config['default_sort'])) {
                $sql .= " ORDER BY {$config['default_sort']['column']} {$config['default_sort']['direction']}";
            }
        } elseif (isset($config['default_sort'])) {
            $sql .= " ORDER BY {$config['default_sort']['column']} {$config['default_sort']['direction']}";
        }

        // Add LIMIT and OFFSET
        if ($options['limit'] > 0) {
            $sql .= " LIMIT {$options['limit']} OFFSET {$options['offset']}";
        }

        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * Build COUNT query for pagination
     */
    private function buildCountQuery($tableName, $config, $options)
    {
        $params = [];
        $conditions = [];

        $sql = "SELECT COUNT(*) FROM {$tableName}";

        // Add search conditions
        if (!empty($options['search']) && !empty($config['searchable'])) {
            $searchConditions = [];
            foreach ($config['searchable'] as $column) {
                $searchConditions[] = "{$column} LIKE :search";
            }
            if (!empty($searchConditions)) {
                $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
                $params[':search'] = '%' . $options['search'] . '%';
            }
        }

        // Add filter conditions
        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $column => $value) {
                if ($value !== '' && $value !== null) {
                    if (isset($config['filterable']) && in_array($column, $config['filterable'])) {
                        if (is_array($value)) {
                            $placeholders = [];
                            foreach ($value as $i => $v) {
                                $placeholder = ":filter_{$column}_{$i}";
                                $placeholders[] = $placeholder;
                                $params[$placeholder] = $v;
                            }
                            $conditions[] = "{$column} IN (" . implode(',', $placeholders) . ")";
                        } else {
                            $conditions[] = "{$column} = :filter_{$column}";
                            $params[":filter_{$column}"] = $value;
                        }
                    }
                }
            }
        }

        // Add WHERE clause if conditions exist
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * Get distinct values for a column (useful for filter dropdowns)
     */
    public function getDistinctValues($tableName, $column)
    {
        if (!isset($this->tableConfig[$tableName])) {
            throw new Exception("Table configuration not found for: {$tableName}");
        }

        $actualTableName = $this->tableConfig[$tableName]['table'] ?? $tableName;

        try {
            $sql = "SELECT DISTINCT {$column} FROM {$actualTableName} WHERE {$column} IS NOT NULL ORDER BY {$column}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Export data to different formats
     */
    public function exportData($tableName, $format = 'csv', $options = [])
    {
        // Fetch all data without pagination
        $exportOptions = array_merge($options, [
            'limit' => 0,
            'offset' => 0
        ]);

        $result = $this->fetchTableData($tableName, $exportOptions);
        $data = $result['data'];

        switch (strtolower($format)) {
            case 'csv':
                return $this->exportToCSV($data, $tableName);
            case 'pdf':
                return $this->exportToPDF($data, $tableName);
            case 'docx':
                return $this->exportToDOCX($data, $tableName);
            default:
                throw new Exception("Unsupported export format: {$format}");
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($data, $tableName)
    {
        $output = fopen('php://temp', 'r+');

        // Write headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }

        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return [
            'content' => $csv,
            'filename' => $tableName . '_' . date('Y-m-d_H-i-s') . '.csv',
            'mime' => 'text/csv'
        ];
    }

    /**
     * Export to PDF (basic implementation - requires additional library)
     */
    private function exportToPDF($data, $tableName)
    {
        // Note: This is a placeholder. You'll need to integrate a PDF library like TCPDF or FPDF
        $html = '<html><body>';
        $html .= '<h1>' . ucfirst($tableName) . ' Data Export</h1>';
        $html .= '<table border="1" cellpadding="5">';

        // Headers
        if (!empty($data)) {
            $html .= '<tr>';
            foreach (array_keys($data[0]) as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            $html .= '</tr>';
        }

        // Data
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . htmlspecialchars($value ?? '') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        return [
            'content' => $html,
            'filename' => $tableName . '_' . date('Y-m-d_H-i-s') . '.pdf',
            'mime' => 'application/pdf',
            'note' => 'PDF export requires additional library implementation'
        ];
    }

    /**
     * Export to DOCX (basic implementation - requires additional library)
     */
    private function exportToDOCX($data, $tableName)
    {
        // Note: This is a placeholder. You'll need to integrate a library like PHPWord
        $content = ucfirst($tableName) . " Data Export\n";
        $content .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

        // Create simple text table
        if (!empty($data)) {
            // Headers
            $headers = array_keys($data[0]);
            $content .= implode("\t", $headers) . "\n";
            $content .= str_repeat("-", 80) . "\n";

            // Data
            foreach ($data as $row) {
                $content .= implode("\t", array_map(function ($v) {
                    return $v ?? '';
                }, $row)) . "\n";
            }
        }

        return [
            'content' => $content,
            'filename' => $tableName . '_' . date('Y-m-d_H-i-s') . '.docx',
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'note' => 'DOCX export requires additional library implementation'
        ];
    }
}
?>