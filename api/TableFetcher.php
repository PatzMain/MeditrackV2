<?php
class TableFetcher {
    private $pdo;
    private $config;
    
    public function __construct($pdo, $config) {
        $this->pdo = $pdo;
        $this->config = $config;
    }
    
    public function fetchTableData($tableName = 'medical_medicines', $params = []) {
        $tableConfig = $this->config[$tableName];
        $columns = $tableConfig['columns'];
        
        // Extract parameters
        $page = max(1, (int)($params['page'] ?? 1));
        $itemsPerPage = (int)($params['items_per_page'] ?? $tableConfig['default_items_per_page']);
        $search = $params['search'] ?? '';
        $sortColumn = $params['sort'] ?? $tableConfig['default_sort']['column'];
        $sortDirection = strtoupper($params['direction'] ?? $tableConfig['default_sort']['direction']);
        
        // Validate sort direction
        if (!in_array($sortDirection, ['ASC', 'DESC'])) {
            $sortDirection = 'ASC';
        }
        
        // Build base query
        $selectColumns = [];
        foreach ($columns as $columnKey => $column) {
            $selectColumns[] = $columnKey;
        }
        
        $sql = "SELECT " . implode(', ', $selectColumns) . " FROM " . $tableConfig['table_name'];
        $countSql = "SELECT COUNT(*) FROM " . $tableConfig['table_name'];
        
        // Build WHERE conditions
        $whereConditions = [];
        $whereParams = [];
        
        // Search functionality
        if (!empty($search)) {
            $searchConditions = [];
            foreach ($columns as $columnKey => $column) {
                if ($column['searchable']) {
                    $searchConditions[] = "$columnKey LIKE :search";
                }
            }
            if (!empty($searchConditions)) {
                $whereConditions[] = "(" . implode(' OR ', $searchConditions) . ")";
                $whereParams['search'] = "%$search%";
            }
        }
        
        // Filter functionality
        $this->applyFilters($whereConditions, $whereParams, $params, $tableConfig);
        
        // Apply WHERE clause
        if (!empty($whereConditions)) {
            $whereClause = " WHERE " . implode(' AND ', $whereConditions);
            $sql .= $whereClause;
            $countSql .= $whereClause;
        }
        
        // Get total count for pagination
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($whereParams);
        $totalRecords = $countStmt->fetchColumn();
        
        // Add sorting
        if (isset($columns[$sortColumn]) && $columns[$sortColumn]['sortable']) {
            $sql .= " ORDER BY $sortColumn $sortDirection";
        }
        
        // Add pagination
        $offset = ($page - 1) * $itemsPerPage;
        $sql .= " LIMIT :limit OFFSET :offset";
        
        // Execute main query
        $stmt = $this->pdo->prepare($sql);
        
        // Bind WHERE parameters
        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        // Bind pagination parameters
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate pagination info
        $totalPages = ceil($totalRecords / $itemsPerPage);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'items_per_page' => $itemsPerPage
            ],
            'filters' => $this->getActiveFilters($params, $tableConfig),
            'search' => $search
        ];
    }
    
    private function applyFilters(&$whereConditions, &$whereParams, $params, $tableConfig) {
        foreach ($tableConfig['filters'] as $filterKey => $filter) {
            $filterValue = $params[$filterKey] ?? '';
            
            if (empty($filterValue) || $filterValue === 'all') {
                continue;
            }
            
            switch ($filterKey) {
                case 'classification':
                    $whereConditions[] = "medicine_classification = :classification";
                    $whereParams['classification'] = $filterValue;
                    break;
                    
                case 'stock_status':
                    switch ($filterValue) {
                        case 'in_stock':
                            $whereConditions[] = "medicine_stock > 20";
                            break;
                        case 'low_stock':
                            $whereConditions[] = "medicine_stock > 0 AND medicine_stock <= 20";
                            break;
                        case 'out_of_stock':
                            $whereConditions[] = "medicine_stock = 0";
                            break;
                    }
                    break;
                    
                case 'expiry_status':
                    switch ($filterValue) {
                        case 'valid':
                            $whereConditions[] = "medicine_expiry_date > DATE_ADD(NOW(), INTERVAL 30 DAY)";
                            break;
                        case 'expiring_soon':
                            $whereConditions[] = "medicine_expiry_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)";
                            break;
                        case 'expired':
                            $whereConditions[] = "medicine_expiry_date < NOW()";
                            break;
                    }
                    break;
            }
        }
    }
    
    private function getActiveFilters($params, $tableConfig) {
        $activeFilters = [];
        foreach ($tableConfig['filters'] as $filterKey => $filter) {
            if (isset($params[$filterKey]) && !empty($params[$filterKey]) && $params[$filterKey] !== 'all') {
                $activeFilters[$filterKey] = $params[$filterKey];
            }
        }
        return $activeFilters;
    }
    
    public function getRecord($tableName = 'medical_medicines', $id) {
        $tableConfig = $this->config[$tableName];
        $primaryKey = $tableConfig['primary_key'];
        $tableName = $tableConfig['table_name'];
        
        $columns = [];
        foreach ($tableConfig['columns'] as $columnKey => $column) {
            $columns[] = $columnKey;
        }
        
        $sql = "SELECT " . implode(', ', $columns) . " FROM $tableName WHERE $primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function insertRecord($tableName = 'medical_medicines', $data) {
        $tableConfig = $this->config[$tableName];
        $tableName = $tableConfig['table_name'];
        
        // Filter out non-editable columns and empty values
        $insertData = [];
        foreach ($tableConfig['columns'] as $columnKey => $column) {
            if ($column['editable'] && isset($data[$columnKey]) && $data[$columnKey] !== '') {
                $insertData[$columnKey] = $data[$columnKey];
            }
        }
        
        if (empty($insertData)) {
            return false;
        }
        
        // Add created_at if it exists
        if (isset($tableConfig['columns']['created_at'])) {
            $insertData['created_at'] = date('Y-m-d H:i:s');
        }
        
        $columns = array_keys($insertData);
        $placeholders = ':' . implode(', :', $columns);
        
        $sql = "INSERT INTO $tableName (" . implode(', ', $columns) . ") VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($insertData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }
    
    public function updateRecord($tableName = 'medical_medicines', $id, $data) {
        $tableConfig = $this->config[$tableName];
        $primaryKey = $tableConfig['primary_key'];
        $tableName = $tableConfig['table_name'];
        
        // Filter out non-editable columns
        $updateData = [];
        foreach ($tableConfig['columns'] as $columnKey => $column) {
            if ($column['editable'] && isset($data[$columnKey])) {
                $updateData[$columnKey] = $data[$columnKey];
            }
        }
        
        if (empty($updateData)) {
            return false;
        }
        
        $setParts = [];
        foreach (array_keys($updateData) as $column) {
            $setParts[] = "$column = :$column";
        }
        
        $sql = "UPDATE $tableName SET " . implode(', ', $setParts) . " WHERE $primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($updateData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function deleteRecord($tableName = 'medical_medicines', $id) {
        $tableConfig = $this->config[$tableName];
        $primaryKey = $tableConfig['primary_key'];
        $tableName = $tableConfig['table_name'];
        
        $sql = "DELETE FROM $tableName WHERE $primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function exportData($tableName = 'medical_medicines', $params = []) {
        $result = $this->fetchTableData($tableName, array_merge($params, ['page' => 1, 'items_per_page' => 999999]));
        return $result['data'];
    }
}
?>