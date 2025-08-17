<?php
/**
 * TableRenderer Class
 * Handles rendering of table HTML with sorting, pagination, and actions
 */

require_once 'table_config.php'; // Adjust path as necessary

class TableRenderer {
    private $tableConfig;
    private $currentTable;
    private $data;
    private $options;
    
    public function __construct() {
        $this->tableConfig = $GLOBALS['tableConfig'] ?? [];
    }
    
    /**
     * Set the current table to render
     */
    public function setTable($tableName, $data = [], $options = []) {
        if (!isset($this->tableConfig[$tableName])) {
            throw new Exception("Table configuration not found for: {$tableName}");
        }
        
        $this->currentTable = $tableName;
        $this->data = $data;
        $this->options = array_merge([
            'show_actions' => true,
            'show_checkbox' => true,
            'actions' => ['view', 'edit', 'delete'],
            'current_page' => 1,
            'total_pages' => 1,
            'total_records' => 0,
            'current_sort' => '',
            'current_dir' => 'ASC',
            'row_id_field' => 'id'
        ], $options);
        
        return $this;
    }
    
    /**
     * Render the complete table
     */
    public function render() {
        if (!$this->currentTable) {
            throw new Exception("No table set for rendering");
        }
        
        $config = $this->tableConfig[$this->currentTable];
        $html = '';
        
        // Start table wrapper
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-hover table-striped" id="data-table-' . $this->currentTable . '">';
        
        // Render header
        $html .= $this->renderHeader($config);
        
        // Render body
        $html .= $this->renderBody($config);
        
        // Render footer if needed
        $html .= $this->renderFooter($config);
        
        $html .= '</table>';
        $html .= '</div>';
        
        // Add pagination
        $html .= $this->renderPagination();
        
        return $html;
    }
    
    /**
     * Render table header
     */
    private function renderHeader($config) {
        $html = '<thead class="table-dark">';
        $html .= '<tr>';
        
        // Checkbox column
        if ($this->options['show_checkbox']) {
            $html .= '<th class="text-center" width="40">';
            $html .= '<input type="checkbox" class="form-check-input" id="select-all-' . $this->currentTable . '">';
            $html .= '</th>';
        }
        
        // Data columns
        foreach ($config['columns'] as $column => $columnConfig) {
            if (isset($columnConfig['visible']) && !$columnConfig['visible']) {
                continue;
            }
            
            $label = $columnConfig['label'] ?? ucfirst(str_replace('_', ' ', $column));
            $width = isset($columnConfig['width']) ? ' width="' . $columnConfig['width'] . '"' : '';
            $class = isset($columnConfig['class']) ? ' class="' . $columnConfig['class'] . '"' : '';
            
            // Check if sortable
            $isSortable = isset($config['sortable']) && in_array($column, $config['sortable']);
            
            if ($isSortable) {
                // Use naming convention from sort.js
                $sortClass = 'sortable';
                $sortIcon = 'fa-sort';
                
                if ($this->options['current_sort'] === $column) {
                    if ($this->options['current_dir'] === 'ASC') {
                        $sortClass .= ' asc';
                        $sortIcon = 'fa-sort-up';
                    } else {
                        $sortClass .= ' desc';
                        $sortIcon = 'fa-sort-down';
                    }
                }
                
                $html .= '<th' . $width . ' class="' . $sortClass . '" data-column="' . $column . '">';
                $html .= '<span class="th-inner">' . htmlspecialchars($label) . '</span>';
                $html .= ' <i class="fas ' . $sortIcon . ' sort-icon"></i>';
                $html .= '</th>';
            } else {
                $html .= '<th' . $width . $class . '>';
                $html .= '<span class="th-inner">' . htmlspecialchars($label) . '</span>';
                $html .= '</th>';
            }
        }
        
        // Actions column
        if ($this->options['show_actions']) {
            $html .= '<th class="text-center" width="150">Actions</th>';
        }
        
        $html .= '</tr>';
        $html .= '</thead>';
        
        return $html;
    }
    
    /**
     * Render table body
     */
    private function renderBody($config) {
        $html = '<tbody>';
        
        if (empty($this->data)) {
            $colspan = count($config['columns']);
            if ($this->options['show_checkbox']) $colspan++;
            if ($this->options['show_actions']) $colspan++;
            
            $html .= '<tr>';
            $html .= '<td colspan="' . $colspan . '" class="text-center text-muted py-4">';
            $html .= '<i class="fas fa-inbox fa-3x mb-3"></i><br>';
            $html .= 'No data available';
            $html .= '</td>';
            $html .= '</tr>';
        } else {
            foreach ($this->data as $index => $row) {
                $rowId = $row[$this->options['row_id_field']] ?? $index;
                $html .= '<tr data-row-id="' . htmlspecialchars($rowId) . '">';
                
                // Checkbox column
                if ($this->options['show_checkbox']) {
                    $html .= '<td class="text-center">';
                    $html .= '<input type="checkbox" class="form-check-input row-checkbox" value="' . htmlspecialchars($rowId) . '">';
                    $html .= '</td>';
                }
                
                // Data columns
                foreach ($config['columns'] as $column => $columnConfig) {
                    if (isset($columnConfig['visible']) && !$columnConfig['visible']) {
                        continue;
                    }
                    
                    $value = $row[$column] ?? '';
                    $class = isset($columnConfig['class']) ? ' class="' . $columnConfig['class'] . '"' : '';
                    
                    // Apply formatter if exists
                    if (isset($columnConfig['formatter'])) {
                        $value = $this->applyFormatter($value, $columnConfig['formatter'], $row);
                    } else {
                        $value = htmlspecialchars($value);
                    }
                    
                    $html .= '<td' . $class . '>' . $value . '</td>';
                }
                
                // Actions column
                if ($this->options['show_actions']) {
                    $html .= '<td class="text-center">';
                    $html .= $this->renderActions($rowId, $row);
                    $html .= '</td>';
                }
                
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody>';
        
        return $html;
    }
    
    /**
     * Render table footer
     */
    private function renderFooter($config) {
        // Optional: Add summary row or totals
        return '';
    }
    
    /**
     * Render action buttons
     */
    private function renderActions($rowId, $row) {
        $html = '<div class="btn-group" role="group">';
        
        foreach ($this->options['actions'] as $action) {
            switch ($action) {
                case 'view':
                    $html .= '<button type="button" class="btn btn-sm btn-info action-view" data-id="' . htmlspecialchars($rowId) . '" title="View">';
                    $html .= '<i class="fas fa-eye"></i>';
                    $html .= '</button>';
                    break;
                    
                case 'edit':
                    $html .= '<button type="button" class="btn btn-sm btn-warning action-edit" data-id="' . htmlspecialchars($rowId) . '" title="Edit">';
                    $html .= '<i class="fas fa-edit"></i>';
                    $html .= '</button>';
                    break;
                    
                case 'delete':
                    $html .= '<button type="button" class="btn btn-sm btn-danger action-delete" data-id="' . htmlspecialchars($rowId) . '" title="Delete">';
                    $html .= '<i class="fas fa-trash"></i>';
                    $html .= '</button>';
                    break;
                    
                default:
                    // Custom action
                    if (is_array($action)) {
                        $html .= '<button type="button" class="btn btn-sm ' . ($action['class'] ?? 'btn-secondary') . ' ' . ($action['action_class'] ?? '') . '" ';
                        $html .= 'data-id="' . htmlspecialchars($rowId) . '" ';
                        if (isset($action['data'])) {
                            foreach ($action['data'] as $key => $value) {
                                $html .= 'data-' . $key . '="' . htmlspecialchars($value) . '" ';
                            }
                        }
                        $html .= 'title="' . ($action['title'] ?? '') . '">';
                        $html .= '<i class="' . ($action['icon'] ?? 'fas fa-cog') . '"></i>';
                        if (isset($action['text'])) {
                            $html .= ' ' . $action['text'];
                        }
                        $html .= '</button>';
                    }
                    break;
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render pagination controls
     */
    private function renderPagination() {
        if ($this->options['total_pages'] <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Table pagination" class="mt-3">';
        $html .= '<div class="d-flex justify-content-between align-items-center">';
        
        // Records info
        $html .= '<div class="text-muted">';
        $html .= 'Showing page ' . $this->options['current_page'] . ' of ' . $this->options['total_pages'];
        $html .= ' (' . $this->options['total_records'] . ' total records)';
        $html .= '</div>';
        
        // Pagination buttons
        $html .= '<ul class="pagination mb-0">';
        
        // Previous button
        $prevDisabled = $this->options['current_page'] <= 1 ? ' disabled' : '';
        $html .= '<li class="page-item' . $prevDisabled . '">';
        $html .= '<a class="page-link" href="#" data-page="' . ($this->options['current_page'] - 1) . '" tabindex="-1">Previous</a>';
        $html .= '</li>';
        
        // Page numbers
        $startPage = max(1, $this->options['current_page'] - 2);
        $endPage = min($this->options['total_pages'], $this->options['current_page'] + 2);
        
        if ($startPage > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
            if ($startPage > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            $active = $i == $this->options['current_page'] ? ' active' : '';
            $html .= '<li class="page-item' . $active . '">';
            $html .= '<a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a>';
            $html .= '</li>';
        }
        
        if ($endPage < $this->options['total_pages']) {
            if ($endPage < $this->options['total_pages'] - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="#" data-page="' . $this->options['total_pages'] . '">' . $this->options['total_pages'] . '</a></li>';
        }
        
        // Next button
        $nextDisabled = $this->options['current_page'] >= $this->options['total_pages'] ? ' disabled' : '';
        $html .= '<li class="page-item' . $nextDisabled . '">';
        $html .= '<a class="page-link" href="#" data-page="' . ($this->options['current_page'] + 1) . '">Next</a>';
        $html .= '</li>';
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</nav>';
        
        return $html;
    }
    
    /**
     * Apply formatter to value
     */
    private function applyFormatter($value, $formatter, $row) {
        if (is_callable($formatter)) {
            return call_user_func($formatter, $value, $row);
        }
        
        // Built-in formatters
        switch ($formatter) {
            case 'date':
                return $value ? date('Y-m-d', strtotime($value)) : '';
                
            case 'datetime':
                return $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
                
            case 'currency':
                return '$' . number_format((float)$value, 2);
                
            case 'number':
                return number_format((float)$value);
                
            case 'percentage':
                return number_format((float)$value, 2) . '%';
                
            case 'boolean':
                return $value ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>';
                
            case 'status':
                $statusClass = 'secondary';
                if ($value === 'active') $statusClass = 'success';
                elseif ($value === 'inactive') $statusClass = 'danger';
                elseif ($value === 'pending') $statusClass = 'warning';
                return '<span class="badge bg-' . $statusClass . '">' . ucfirst($value) . '</span>';
                
            default:
                return htmlspecialchars($value);
        }
    }
}
?>