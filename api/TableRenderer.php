<?php
class TableRenderer {
    private $config;
    private $baseUrl;
    
    public function __construct($config, $baseUrl = '') {
        $this->config = $config;
        $this->baseUrl = $baseUrl;
    }
    
    public function renderTable($data = [], $currentPage = 1, $totalPages = 1, $totalRecords = 0, $filters = []) {
        $tableConfig = $this->config['medical_medicines'];
        $columns = $tableConfig['columns'];
        
        ob_start();
        ?>
        <div class="table-container">
            <!-- Header with Title and Controls -->
            <div class="table-header">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4><i class="fas <?php echo $tableConfig['icon']; ?>"></i> <?php echo $tableConfig['display_name']; ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-success" onclick="addRecord()">
                            <i class="fas fa-plus"></i> Add Medicine
                        </button>
                        <?php if ($tableConfig['enable_export']): ?>
                        <button type="button" class="btn btn-info" onclick="exportData()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Search and Filters -->
                <div class="row mb-3">
                    <?php if ($tableConfig['enable_search']): ?>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Search medicines..." 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="performSearch()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($tableConfig['enable_filters']): ?>
                    <div class="col-md-8">
                        <div class="row">
                            <?php foreach ($tableConfig['filters'] as $filterKey => $filter): ?>
                            <div class="col-md-4">
                                <select class="form-select" id="filter_<?php echo $filterKey; ?>" onchange="applyFilters()">
                                    <option value=""><?php echo $filter['label']; ?></option>
                                    <?php
                                    $options = $filter['options'];
                                    if (is_string($options) && isset($columns[$options]['options'])) {
                                        $options = $columns[$options]['options'];
                                    }
                                    foreach ($options as $value => $label):
                                        $selected = (isset($filters[$filterKey]) && $filters[$filterKey] == $value) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <?php foreach ($columns as $columnKey => $column): ?>
                                <?php if ($column['visible']): ?>
                                <th style="width: <?php echo $column['width'] ?? 'auto'; ?>" 
                                    class="<?php echo $column['sortable'] ? 'sortable' : ''; ?><?php echo isset($column['class']) ? ' ' . $column['class'] : ''; ?>"
                                    <?php if ($column['sortable']): ?>
                                    onclick="sortTable('<?php echo $columnKey; ?>')"
                                    <?php endif; ?>>
                                    <?php echo $column['label']; ?>
                                    <?php if ($column['sortable']): ?>
                                    <i class="fas fa-sort sort-icon" id="sort_<?php echo $columnKey; ?>"></i>
                                    <?php endif; ?>
                                </th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?php echo count(array_filter($columns, function($col) { return $col['visible']; })) + 1; ?>" 
                                class="text-center">No records found</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($columns as $columnKey => $column): ?>
                                    <?php if ($column['visible']): ?>
                                    <td class="<?php echo $column['class'] ?? ''; ?>">
                                        <?php echo $this->formatCellValue($row[$columnKey] ?? '', $column, $columnKey); ?>
                                    </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php foreach ($tableConfig['actions'] as $actionKey => $action): ?>
                                        <button type="button" 
                                                class="btn <?php echo $action['class']; ?>" 
                                                onclick="performAction('<?php echo $actionKey; ?>', <?php echo $row[$tableConfig['primary_key']]; ?>)"
                                                title="<?php echo $action['label']; ?>">
                                            <i class="fas <?php echo $action['icon']; ?>"></i>
                                        </button>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($tableConfig['enable_pagination'] && $totalPages > 1): ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="showing-info">
                        Showing <?php echo (($currentPage - 1) * $tableConfig['default_items_per_page']) + 1; ?> 
                        to <?php echo min($currentPage * $tableConfig['default_items_per_page'], $totalRecords); ?> 
                        of <?php echo $totalRecords; ?> entries
                    </div>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Table pagination">
                        <ul class="pagination justify-content-end">
                            <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="#" onclick="changePage(<?php echo $currentPage - 1; ?>)">Previous</a>
                            </li>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="#" onclick="changePage(<?php echo $i; ?>)"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="#" onclick="changePage(<?php echo $currentPage + 1; ?>)">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Styles -->
        <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .sort-icon {
            margin-left: 5px;
            opacity: 0.5;
        }
        .badge {
            font-size: 0.75em;
        }
        .stock-low { color: #ffc107; font-weight: bold; }
        .stock-out { color: #dc3545; font-weight: bold; }
        .stock-ok { color: #28a745; font-weight: bold; }
        .expiry-expired { color: #dc3545; font-weight: bold; }
        .expiry-soon { color: #ffc107; font-weight: bold; }
        .expiry-ok { color: #28a745; font-weight: bold; }
        .showing-info {
            padding: 8px 0;
            color: #6c757d;
        }
        </style>
        
        <!-- JavaScript -->
        <script>
        let currentSort = '<?php echo $tableConfig['default_sort']['column']; ?>';
        let currentDirection = '<?php echo $tableConfig['default_sort']['direction']; ?>';
        
        function performSearch() {
            const search = document.getElementById('searchInput').value;
            const url = new URL(window.location);
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            url.searchParams.set('page', 1);
            window.location = url;
        }
        
        function applyFilters() {
            const url = new URL(window.location);
            <?php foreach ($tableConfig['filters'] as $filterKey => $filter): ?>
            const filter_<?php echo $filterKey; ?> = document.getElementById('filter_<?php echo $filterKey; ?>').value;
            if (filter_<?php echo $filterKey; ?>) {
                url.searchParams.set('<?php echo $filterKey; ?>', filter_<?php echo $filterKey; ?>);
            } else {
                url.searchParams.delete('<?php echo $filterKey; ?>');
            }
            <?php endforeach; ?>
            url.searchParams.set('page', 1);
            window.location = url;
        }
        
        function sortTable(column) {
            const url = new URL(window.location);
            if (currentSort === column) {
                currentDirection = currentDirection === 'ASC' ? 'DESC' : 'ASC';
            } else {
                currentDirection = 'ASC';
            }
            url.searchParams.set('sort', column);
            url.searchParams.set('direction', currentDirection);
            url.searchParams.set('page', 1);
            window.location = url;
        }
        
        function changePage(page) {
            if (page < 1) return;
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location = url;
        }
        
        function performAction(action, id) {
            switch(action) {
                case 'view':
                    window.location = 'view.php?id=' + id;
                    break;
                case 'edit':
                    window.location = 'edit.php?id=' + id;
                    break;
                case 'delete':
                    if (confirm('Are you sure you want to delete this medicine?')) {
                        window.location = 'delete.php?id=' + id;
                    }
                    break;
            }
        }
        
        function addRecord() {
            window.location = 'add.php';
        }
        
        function exportData() {
            window.location = 'export.php?' + window.location.search.substring(1);
        }
        
        // Handle Enter key in search
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    private function formatCellValue($value, $column, $columnKey) {
        if (empty($value) && $value !== '0' && $value !== 0) {
            return '<span class="text-muted">-</span>';
        }
        
        switch ($column['type']) {
            case 'date':
                if (isset($column['format']) && $column['format'] === 'expiry_status') {
                    return $this->formatExpiryDate($value);
                }
                return date('M j, Y', strtotime($value));
                
            case 'datetime':
                return date('M j, Y g:i A', strtotime($value));
                
            case 'integer':
                if (isset($column['format']) && $column['format'] === 'stock_status') {
                    return $this->formatStock($value, $column['low_stock_threshold'] ?? 20);
                }
                return number_format($value);
                
            case 'enum':
                if (isset($column['badge']) && $column['badge']) {
                    $badgeColor = $column['badge_colors'][$value] ?? 'secondary';
                    return '<span class="badge bg-' . $badgeColor . '">' . htmlspecialchars($value) . '</span>';
                }
                return htmlspecialchars($value);
                
            default:
                return htmlspecialchars($value);
        }
    }
    
    private function formatStock($stock, $threshold = 20) {
        $stock = (int)$stock;
        if ($stock === 0) {
            return '<span class="stock-out">' . $stock . ' (Out of Stock)</span>';
        } elseif ($stock <= $threshold) {
            return '<span class="stock-low">' . $stock . ' (Low Stock)</span>';
        } else {
            return '<span class="stock-ok">' . $stock . '</span>';
        }
    }
    
    private function formatExpiryDate($date) {
        $expiryDate = new DateTime($date);
        $today = new DateTime();
        $interval = $today->diff($expiryDate);
        
        if ($expiryDate < $today) {
            return '<span class="expiry-expired">' . $expiryDate->format('M j, Y') . ' (Expired)</span>';
        } elseif ($interval->days <= 30) {
            return '<span class="expiry-soon">' . $expiryDate->format('M j, Y') . ' (Expires Soon)</span>';
        } else {
            return '<span class="expiry-ok">' . $expiryDate->format('M j, Y') . '</span>';
        }
    }
}
?>