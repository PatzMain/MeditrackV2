<?php
/**
 * TableLayout Class
 * Complete table interface with search, filters, export, and AJAX handling
 */

require_once 'TableFetcher.php';
require_once 'TableRenderer.php';
require_once 'table_config.php'; // Adjust path as necessary

class TableLayout {
    private $fetcher;
    private $renderer;
    private $tableConfig;
    private $tableName;
    private $options;
    
    public function __construct($tableName, $options = []) {
        $this->fetcher = new TableFetcher();
        $this->renderer = new TableRenderer();
        $this->tableConfig = $GLOBALS['tableConfig'] ?? [];
        $this->tableName = $tableName;
        
        $this->options = array_merge([
            'show_search' => true,
            'show_filters' => true,
            'show_add_button' => true,
            'show_export' => true,
            'show_bulk_actions' => true,
            'per_page' => 25,
            'ajax_url' => 'table_ajax.php',
            'add_url' => 'add.php',
            'edit_url' => 'edit.php',
            'delete_url' => 'delete.php',
            'export_formats' => ['csv', 'pdf', 'docx']
        ], $options);
    }
    
    /**
     * Render the complete table layout
     */
    public function render() {
        if (!isset($this->tableConfig[$this->tableName])) {
            throw new Exception("Table configuration not found for: {$this->tableName}");
        }
        
        $config = $this->tableConfig[$this->tableName];
        
        $html = '<div class="table-layout-container" data-table="' . $this->tableName . '">';
        
        // Render toolbar
        $html .= $this->renderToolbar($config);
        
        // Render filters
        if ($this->options['show_filters']) {
            $html .= $this->renderFilters($config);
        }
        
        // Table container
        $html .= '<div class="table-container" id="table-container-' . $this->tableName . '">';
        $html .= '<div class="table-loading" style="display:none;">';
        $html .= '<div class="text-center py-5">';
        $html .= '<div class="spinner-border text-primary" role="status">';
        $html .= '<span class="visually-hidden">Loading...</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Initial table render
        $html .= '<div class="table-content">';
        $html .= $this->renderTable();
        $html .= '</div>';
        
        $html .= '</div>'; // End table-container
        
        // Add JavaScript for handling
        $html .= $this->renderJavaScript();
        
        $html .= '</div>'; // End table-layout-container
        
        return $html;
    }
    
    /**
     * Render toolbar with search, add button, and export options
     */
    private function renderToolbar($config) {
        $html = '<div class="table-toolbar mb-3">';
        $html .= '<div class="row align-items-center">';
        
        // Search box
        if ($this->options['show_search'] && !empty($config['searchable'])) {
            $html .= '<div class="col-md-4">';
            $html .= '<div class="input-group">';
            $html .= '<span class="input-group-text"><i class="fas fa-search"></i></span>';
            $html .= '<input type="text" class="form-control" id="table-search-' . $this->tableName . '" ';
            $html .= 'placeholder="Search ' . implode(', ', array_map(function($col) {
                return ucfirst(str_replace('_', ' ', $col));
            }, array_slice($config['searchable'], 0, 3))) . '...">';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        // Middle spacer
        $html .= '<div class="col-md-4">';
        if ($this->options['show_bulk_actions']) {
            $html .= '<div class="bulk-actions" style="display:none;">';
            $html .= '<div class="btn-group" role="group">';
            $html .= '<button type="button" class="btn btn-sm btn-outline-danger" id="bulk-delete">';
            $html .= '<i class="fas fa-trash"></i> Delete Selected';
            $html .= '</button>';
            $html .= '<button type="button" class="btn btn-sm btn-outline-secondary" id="bulk-export">';
            $html .= '<i class="fas fa-download"></i> Export Selected';
            $html .= '</button>';
            $html .= '</div>';
            $html .= '<span class="ms-2 text-muted"><span class="selected-count">0</span> items selected</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // Action buttons
        $html .= '<div class="col-md-4 text-end">';
        $html .= '<div class="btn-group" role="group">';
        
        // Add button
        if ($this->options['show_add_button']) {
            $html .= '<button type="button" class="btn btn-success" id="add-record-' . $this->tableName . '">';
            $html .= '<i class="fas fa-plus"></i> Add New';
            $html .= '</button>';
        }
        
        // Export dropdown
        if ($this->options['show_export']) {
            $html .= '<div class="btn-group" role="group">';
            $html .= '<button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">';
            $html .= '<i class="fas fa-download"></i> Export';
            $html .= '</button>';
            $html .= '<ul class="dropdown-menu">';
            
            foreach ($this->options['export_formats'] as $format) {
                $icon = 'fa-file';
                if ($format === 'csv') $icon = 'fa-file-csv';
                elseif ($format === 'pdf') $icon = 'fa-file-pdf';
                elseif ($format === 'docx') $icon = 'fa-file-word';
                
                $html .= '<li><a class="dropdown-item export-option" href="#" data-format="' . $format . '">';
                $html .= '<i class="fas ' . $icon . ' me-2"></i>Export as ' . strtoupper($format);
                $html .= '</a></li>';
            }
            
            $html .= '</ul>';
            $html .= '</div>';
        }
        
        // Refresh button
        $html .= '<button type="button" class="btn btn-outline-secondary" id="refresh-table-' . $this->tableName . '" title="Refresh">';
        $html .= '<i class="fas fa-sync-alt"></i>';
        $html .= '</button>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render filter controls
     */
    private function renderFilters($config) {
        if (empty($config['filterable'])) {
            return '';
        }
        
        $html = '<div class="table-filters mb-3">';
        $html .= '<div class="card">';
        $html .= '<div class="card-header py-2">';
        $html .= '<a href="#" class="text-decoration-none" data-bs-toggle="collapse" data-bs-target="#filters-' . $this->tableName . '">';
        $html .= '<i class="fas fa-filter me-2"></i>Filters';
        $html .= '<i class="fas fa-chevron-down float-end"></i>';
        $html .= '</a>';
        $html .= '</div>';
        $html .= '<div class="collapse" id="filters-' . $this->tableName . '">';
        $html .= '<div class="card-body">';
        $html .= '<div class="row">';
        
        foreach ($config['filterable'] as $column) {
            $columnConfig = $config['columns'][$column] ?? [];
            $label = $columnConfig['label'] ?? ucfirst(str_replace('_', ' ', $column));
            
            $html .= '<div class="col-md-3 mb-2">';
            $html .= '<label class="form-label small">' . htmlspecialchars($label) . '</label>';
            
            // Determine filter type
            $filterType = $columnConfig['filter_type'] ?? 'select';
            
            if ($filterType === 'select') {
                $html .= '<select class="form-select form-select-sm table-filter" data-column="' . $column . '">';
                $html .= '<option value="">All</option>';
                
                // Get distinct values for dropdown
                try {
                    $distinctValues = $this->fetcher->getDistinctValues($this->tableName, $column);
                    foreach ($distinctValues as $value) {
                        $html .= '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</option>';
                    }
                } catch (Exception $e) {
                    // Handle error silently
                }
                
                $html .= '</select>';
            } elseif ($filterType === 'date_range') {
                $html .= '<div class="input-group input-group-sm">';
                $html .= '<input type="date" class="form-control table-filter-date" data-column="' . $column . '" data-type="from">';
                $html .= '<span class="input-group-text">to</span>';
                $html .= '<input type="date" class="form-control table-filter-date" data-column="' . $column . '" data-type="to">';
                $html .= '</div>';
            } else {
                $html .= '<input type="text" class="form-control form-control-sm table-filter" data-column="' . $column . '" placeholder="Filter...">';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '<div class="text-end mt-2">';
        $html .= '<button type="button" class="btn btn-sm btn-secondary me-2" id="clear-filters-' . $this->tableName . '">';
        $html .= '<i class="fas fa-times me-1"></i>Clear Filters';
        $html .= '</button>';
        $html .= '<button type="button" class="btn btn-sm btn-primary" id="apply-filters-' . $this->tableName . '">';
        $html .= '<i class="fas fa-check me-1"></i>Apply Filters';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render the table with initial data
     */
    private function renderTable() {
        // Get initial data
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? $this->options['per_page'];
        $search = $_GET['search'] ?? '';
        $orderBy = $_GET['order_by'] ?? '';
        $orderDir = $_GET['order_dir'] ?? 'ASC';
        
        $options = [
            'search' => $search,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir,
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage
        ];
        
        // Get filters from GET parameters
        $filters = [];
        if (isset($this->tableConfig[$this->tableName]['filterable'])) {
            foreach ($this->tableConfig[$this->tableName]['filterable'] as $column) {
                if (isset($_GET['filter_' . $column])) {
                    $filters[$column] = $_GET['filter_' . $column];
                }
            }
        }
        $options['filters'] = $filters;
        
        try {
            $result = $this->fetcher->fetchTableData($this->tableName, $options);
            
            $this->renderer->setTable($this->tableName, $result['data'], [
                'current_page' => $result['page'],
                'total_pages' => $result['pages'],
                'total_records' => $result['total'],
                'current_sort' => $orderBy,
                'current_dir' => $orderDir
            ]);
            
            return $this->renderer->render();
        } catch (Exception $e) {
            return '<div class="alert alert-danger">Error loading table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Render JavaScript for table interactions
     */
    private function renderJavaScript() {
        $ajaxUrl = $this->options['ajax_url'];
        $tableName = $this->tableName;
        
        $html = '<script>
        (function() {
            const tableContainer = document.querySelector(".table-layout-container[data-table=\"' . $tableName . '\"]");
            const searchInput = document.getElementById("table-search-' . $tableName . '");
            const refreshBtn = document.getElementById("refresh-table-' . $tableName . '");
            const addBtn = document.getElementById("add-record-' . $tableName . '");
            let searchTimeout;
            let currentFilters = {};
            
            // Initialize sort.js if available
            if (typeof initializeSort === "function") {
                initializeSort("#data-table-' . $tableName . '");
            }
            
            // Search functionality (seamless load)
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        loadTableData({search: this.value});
                    }, 300); // Debounce 300ms
                });
            }
            
            // Filter functionality
            const applyFiltersBtn = document.getElementById("apply-filters-' . $tableName . '");
            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener("click", function() {
                    const filters = {};
                    tableContainer.querySelectorAll(".table-filter").forEach(filter => {
                        if (filter.value) {
                            filters[filter.dataset.column] = filter.value;
                        }
                    });
                    currentFilters = filters;
                    loadTableData({filters: filters});
                });
            }
            
            // Clear filters
            const clearFiltersBtn = document.getElementById("clear-filters-' . $tableName . '");
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener("click", function() {
                    tableContainer.querySelectorAll(".table-filter").forEach(filter => {
                        filter.value = "";
                    });
                    currentFilters = {};
                    loadTableData({filters: {}});
                });
            }
            
            // Sorting
            tableContainer.addEventListener("click", function(e) {
                const th = e.target.closest("th.sortable");
                if (th) {
                    const column = th.dataset.column;
                    const currentDir = th.classList.contains("asc") ? "DESC" : "ASC";
                    loadTableData({orderBy: column, orderDir: currentDir});
                }
            });
            
            // Pagination
            tableContainer.addEventListener("click", function(e) {
                const pageLink = e.target.closest(".page-link");
                if (pageLink && !pageLink.parentElement.classList.contains("disabled")) {
                    e.preventDefault();
                    const page = pageLink.dataset.page;
                    loadTableData({page: page});
                }
            });
            
            // Export functionality
            tableContainer.querySelectorAll(".export-option").forEach(link => {
                link.addEventListener("click", function(e) {
                    e.preventDefault();
                    const format = this.dataset.format;
                    exportTable(format);
                });
            });
            
            // Add record
            if (addBtn) {
                addBtn.addEventListener("click", function() {
                    window.location.href = "' . $this->options['add_url'] . '?table=' . $tableName . '";
                });
            }
            
            // Refresh
            if (refreshBtn) {
                refreshBtn.addEventListener("click", function() {
                    loadTableData();
                });
            }
            
            // Select all checkbox
            const selectAllCheckbox = document.getElementById("select-all-' . $tableName . '");
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener("change", function() {
                    tableContainer.querySelectorAll(".row-checkbox").forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateBulkActions();
                });
            }
            
            // Row checkboxes
            tableContainer.addEventListener("change", function(e) {
                if (e.target.classList.contains("row-checkbox")) {
                    updateBulkActions();
                }
            });
            
            // Bulk delete
            const bulkDeleteBtn = document.getElementById("bulk-delete");
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener("click", function() {
                    const selected = getSelectedRows();
                    if (selected.length > 0 && confirm("Are you sure you want to delete " + selected.length + " items?")) {
                        bulkDelete(selected);
                    }
                });
            }
            
            // Action buttons
            tableContainer.addEventListener("click", function(e) {
                const viewBtn = e.target.closest(".action-view");
                const editBtn = e.target.closest(".action-edit");
                const deleteBtn = e.target.closest(".action-delete");
                
                if (viewBtn) {
                    window.location.href = "view.php?table=' . $tableName . '&id=" + viewBtn.dataset.id;
                } else if (editBtn) {
                    window.location.href = "' . $this->options['edit_url'] . '?table=' . $tableName . '&id=" + editBtn.dataset.id;
                } else if (deleteBtn) {
                    if (confirm("Are you sure you want to delete this item?")) {
                        deleteRecord(deleteBtn.dataset.id);
                    }
                }
            });
            
            // Load table data via AJAX
            function loadTableData(params = {}) {
                const tableContent = tableContainer.querySelector(".table-content");
                const loadingDiv = tableContainer.querySelector(".table-loading");
                
                // Show loading
                loadingDiv.style.display = "block";
                tableContent.style.opacity = "0.5";
                
                // Build query parameters
                const queryParams = new URLSearchParams();
                queryParams.append("action", "fetch");
                queryParams.append("table", "' . $tableName . '");
                
                // Add search
                if (params.search !== undefined) {
                    queryParams.append("search", params.search);
                } else if (searchInput && searchInput.value) {
                    queryParams.append("search", searchInput.value);
                }
                
                // Add filters
                const filters = params.filters || currentFilters;
                Object.keys(filters).forEach(key => {
                    queryParams.append("filter_" + key, filters[key]);
                });
                
                // Add sorting
                if (params.orderBy) queryParams.append("order_by", params.orderBy);
                if (params.orderDir) queryParams.append("order_dir", params.orderDir);
                
                // Add pagination
                if (params.page) queryParams.append("page", params.page);
                
                // Fetch data
                fetch("' . $ajaxUrl . '?" + queryParams.toString())
                    .then(response => response.text())
                    .then(html => {
                        tableContent.innerHTML = html;
                        loadingDiv.style.display = "none";
                        tableContent.style.opacity = "1";
                        
                        // Reinitialize sort.js if needed
                        if (typeof initializeSort === "function") {
                            initializeSort("#data-table-' . $tableName . '");
                        }
                    })
                    .catch(error => {
                        console.error("Error loading table:", error);
                        loadingDiv.style.display = "none";
                        tableContent.style.opacity = "1";
                        alert("Error loading table data");
                    });
            }
            
            // Export table
            function exportTable(format) {
                const queryParams = new URLSearchParams();
                queryParams.append("action", "export");
                queryParams.append("table", "' . $tableName . '");
                queryParams.append("format", format);
                
                // Add current search and filters
                if (searchInput && searchInput.value) {
                    queryParams.append("search", searchInput.value);
                }
                
                Object.keys(currentFilters).forEach(key => {
                    queryParams.append("filter_" + key, currentFilters[key]);
                });
                
                // Download file
                window.location.href = "' . $ajaxUrl . '?" + queryParams.toString();
            }
            
            // Delete record
            function deleteRecord(id) {
                fetch("' . $ajaxUrl . '", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "action=delete&table=' . $tableName . '&id=" + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadTableData();
                    } else {
                        alert(data.message || "Error deleting record");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error deleting record");
                });
            }
            
            // Bulk delete
            function bulkDelete(ids) {
                fetch("' . $ajaxUrl . '", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "action=bulk_delete&table=' . $tableName . '&ids=" + ids.join(",")
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadTableData();
                        updateBulkActions();
                    } else {
                        alert(data.message || "Error deleting records");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error deleting records");
                });
            }
            
            // Get selected rows
            function getSelectedRows() {
                const selected = [];
                tableContainer.querySelectorAll(".row-checkbox:checked").forEach(cb => {
                    selected.push(cb.value);
                });
                return selected;
            }
            
            // Update bulk actions visibility
            function updateBulkActions() {
                const selected = getSelectedRows();
                const bulkActions = tableContainer.querySelector(".bulk-actions");
                if (bulkActions) {
                    if (selected.length > 0) {
                        bulkActions.style.display = "block";
                        bulkActions.querySelector(".selected-count").textContent = selected.length;
                    } else {
                        bulkActions.style.display = "none";
                    }
                }
            }
        })();
        </script>';
        
        return $html;
    }
    
    /**
     * Handle AJAX requests (for table_ajax.php)
     */
    public static function handleAjaxRequest() {
        $action = $_REQUEST['action'] ?? '';
        $tableName = $_REQUEST['table'] ?? '';
        
        if (!$tableName) {
            echo json_encode(['success' => false, 'message' => 'Table name required']);
            return;
        }
        
        $layout = new self($tableName);
        
        switch ($action) {
            case 'fetch':
                $layout->handleFetchRequest();
                break;
                
            case 'export':
                $layout->handleExportRequest();
                break;
                
            case 'delete':
                $layout->handleDeleteRequest();
                break;
                
            case 'bulk_delete':
                $layout->handleBulkDeleteRequest();
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }
    
    /**
     * Handle fetch request
     */
    private function handleFetchRequest() {
        $page = intval($_GET['page'] ?? 1);
        $perPage = intval($_GET['per_page'] ?? $this->options['per_page']);
        $search = $_GET['search'] ?? '';
        $orderBy = $_GET['order_by'] ?? '';
        $orderDir = $_GET['order_dir'] ?? 'ASC';
        
        $options = [
            'search' => $search,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir,
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage
        ];
        
        // Get filters
        $filters = [];
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'filter_') === 0) {
                $column = substr($key, 7);
                $filters[$column] = $value;
            }
        }
        $options['filters'] = $filters;
        
        try {
            $result = $this->fetcher->fetchTableData($this->tableName, $options);
            
            $this->renderer->setTable($this->tableName, $result['data'], [
                'current_page' => $result['page'],
                'total_pages' => $result['pages'],
                'total_records' => $result['total'],
                'current_sort' => $orderBy,
                'current_dir' => $orderDir
            ]);
            
            echo $this->renderer->render();
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Handle export request
     */
    private function handleExportRequest() {
        $format = $_GET['format'] ?? 'csv';
        $search = $_GET['search'] ?? '';
        
        $options = [
            'search' => $search
        ];
        
        // Get filters
        $filters = [];
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'filter_') === 0) {
                $column = substr($key, 7);
                $filters[$column] = $value;
            }
        }
        $options['filters'] = $filters;
        
        try {
            $export = $this->fetcher->exportData($this->tableName, $format, $options);
            
            // Set headers for download
            header('Content-Type: ' . $export['mime']);
            header('Content-Disposition: attachment; filename="' . $export['filename'] . '"');
            header('Cache-Control: max-age=0');
            
            echo $export['content'];
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Handle delete request
     */
    private function handleDeleteRequest() {
        $id = $_POST['id'] ?? '';
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID required']);
            return;
        }
        
        // Note: Implement actual delete logic here
        // This is a placeholder
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
    }
    
    /**
     * Handle bulk delete request
     */
    private function handleBulkDeleteRequest() {
        $ids = $_POST['ids'] ?? '';
        
        if (!$ids) {
            echo json_encode(['success' => false, 'message' => 'IDs required']);
            return;
        }
        
        $idsArray = explode(',', $ids);
        
        // Note: Implement actual bulk delete logic here
        // This is a placeholder
        echo json_encode(['success' => true, 'message' => count($idsArray) . ' records deleted successfully']);
    }
}