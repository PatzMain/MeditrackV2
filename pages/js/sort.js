// ========================================
// UNIVERSAL TABLE SORTING & FILTERING HANDLER
// File: pages/js/sort.js
// ========================================

// Use namespace to avoid conflicts
window.TableSort = (function() {
    // Private variables
    let currentSort = {
        column: null,
        direction: 'asc'
    };
    let searchTimeout;
    let filterTimeout;

    // ========================================
    // SORTING FUNCTIONS
    // ========================================

    /**
     * Initialize sortable table headers
     */
    function initializeSortable() {
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            makeSortable(table);
        });
    }

    /**
     * Makes a table sortable by adding click handlers to headers
     * @param {HTMLTableElement} table - Table element to make sortable
     */
    function makeSortable(table) {
        const headers = table.querySelectorAll('th.sortable');
        
        headers.forEach(header => {
            // Remove any existing event listeners
            const newHeader = header.cloneNode(true);
            header.parentNode.replaceChild(newHeader, header);
            
            // Add sort indicator if not present
            if (!newHeader.querySelector('.sort-indicator')) {
                const indicator = document.createElement('span');
                indicator.className = 'sort-indicator';
                indicator.innerHTML = '⇅';
                newHeader.appendChild(indicator);
            }
            
            // Add click handler
            newHeader.addEventListener('click', function() {
                sortTable(this, table);
            });
            
            // Add hover effect
            newHeader.style.cursor = 'pointer';
            newHeader.style.userSelect = 'none';
        });
    }

    /**
     * Sorts table by clicked column
     * @param {HTMLTableHeaderCellElement} headerElement - Clicked header element
     * @param {HTMLTableElement} table - Table to sort
     */
    function sortTable(headerElement, table) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Filter out empty state rows
        const dataRows = rows.filter(row => 
            !row.querySelector('.empty-state') && 
            !row.querySelector('[colspan]')
        );
        
        if (dataRows.length === 0) return;
        
        // Get column index
        const headerRow = headerElement.parentElement;
        const headers = Array.from(headerRow.children);
        const columnIndex = headers.indexOf(headerElement);
        
        // Determine sort direction
        const currentDirection = headerElement.dataset.sortDirection || 'none';
        let newDirection;
        
        if (currentDirection === 'none' || currentDirection === 'desc') {
            newDirection = 'asc';
        } else {
            newDirection = 'desc';
        }
        
        // Update sort state
        currentSort.column = columnIndex;
        currentSort.direction = newDirection;
        
        // Reset all headers
        headers.forEach(h => {
            if (h.classList.contains('sortable')) {
                h.dataset.sortDirection = 'none';
                h.classList.remove('asc', 'desc');
                const indicator = h.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = '⇅';
                }
            }
        });
        
        // Set current header
        headerElement.dataset.sortDirection = newDirection;
        headerElement.classList.add(newDirection);
        
        // Update sort indicator
        const indicator = headerElement.querySelector('.sort-indicator');
        if (indicator) {
            indicator.innerHTML = newDirection === 'asc' ? '↑' : '↓';
        }
        
        // Sort rows
        dataRows.sort((a, b) => {
            let aValue = getCellValue(a, columnIndex);
            let bValue = getCellValue(b, columnIndex);
            
            // Determine data type and compare
            const comparison = compareValues(aValue, bValue, headerElement);
            return newDirection === 'desc' ? -comparison : comparison;
        });
        
        // Clear tbody and append sorted rows
        tbody.innerHTML = '';
        dataRows.forEach(row => tbody.appendChild(row));
        
        // Add any empty state row back if needed
        const emptyRow = rows.find(row => 
            row.querySelector('.empty-state') || 
            row.querySelector('[colspan]')
        );
        if (emptyRow) {
            tbody.appendChild(emptyRow);
        }
        
        // Animate sorted rows
        animateSortedRows(tbody);
    }

    /**
     * Gets cell value for sorting
     * @param {HTMLTableRowElement} row - Table row
     * @param {number} columnIndex - Column index
     * @returns {string} Cell value
     */
    function getCellValue(row, columnIndex) {
        const cell = row.cells[columnIndex];
        if (!cell) return '';
        
        // Check for special elements in order of priority
        
        // Links
        const link = cell.querySelector('a');
        if (link) return link.textContent.trim();
        
        // Badges (status, type, etc.)
        const badge = cell.querySelector('[class*="badge"]');
        if (badge) return badge.textContent.trim();
        
        // Date fields
        const dateField = cell.querySelector('.expiry-date, .date-field, .timestamp');
        if (dateField) return dateField.textContent.trim();
        
        // Input fields (for editable cells)
        const input = cell.querySelector('input, select');
        if (input) return input.value;
        
        // Default to cell text content
        return cell.textContent.trim();
    }

    /**
     * Compares two values for sorting
     * @param {string} aValue - First value
     * @param {string} bValue - Second value
     * @param {HTMLElement} header - Header element (for data type hints)
     * @returns {number} Comparison result
     */
    function compareValues(aValue, bValue, header) {
        // Clean values
        aValue = String(aValue).toLowerCase().replace(/^\s+|\s+$/g, '');
        bValue = String(bValue).toLowerCase().replace(/^\s+|\s+$/g, '');
        
        // Check if N/A or empty
        if (aValue === 'n/a' || aValue === '') return 1;
        if (bValue === 'n/a' || bValue === '') return -1;
        
        // Check header text for data type hints
        const headerText = header.textContent.toLowerCase();
        
        // Date columns
        if (headerText.includes('date') || headerText.includes('time')) {
            return compareDates(aValue, bValue);
        }
        
        // Numeric columns
        if (headerText.includes('stock') || 
            headerText.includes('quantity') || 
            headerText.includes('dosage') ||
            headerText.includes('price') ||
            headerText.includes('amount') ||
            headerText.includes('age')) {
            return compareNumbers(aValue, bValue);
        }
        
        // Status columns (custom order)
        if (headerText.includes('status') || headerText.includes('condition')) {
            return compareStatus(aValue, bValue);
        }
        
        // Default string comparison
        return aValue.localeCompare(bValue);
    }

    /**
     * Compares date values
     * @param {string} aValue - First date string
     * @param {string} bValue - Second date string
     * @returns {number} Comparison result
     */
    function compareDates(aValue, bValue) {
        // Try multiple date formats
        const dateFormats = [
            /^(\w{3})\s+(\d{1,2}),\s+(\d{4})$/,  // MMM dd, YYYY
            /^(\d{4})-(\d{2})-(\d{2})$/,          // YYYY-MM-DD
            /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/     // MM/DD/YYYY or DD/MM/YYYY
        ];
        
        let aDate = null;
        let bDate = null;
        
        // Try to parse dates
        for (const format of dateFormats) {
            if (format.test(aValue)) {
                aDate = new Date(aValue);
                break;
            }
        }
        
        for (const format of dateFormats) {
            if (format.test(bValue)) {
                bDate = new Date(bValue);
                break;
            }
        }
        
        // If both are valid dates, compare them
        if (aDate && bDate && !isNaN(aDate) && !isNaN(bDate)) {
            return aDate - bDate;
        }
        
        // Fallback to string comparison
        return aValue.localeCompare(bValue);
    }

    /**
     * Compares numeric values
     * @param {string} aValue - First numeric string
     * @param {string} bValue - Second numeric string
     * @returns {number} Comparison result
     */
    function compareNumbers(aValue, bValue) {
        // Extract numbers from strings
        const aMatch = aValue.match(/[\d.,-]+/);
        const bMatch = bValue.match(/[\d.,-]+/);
        
        if (aMatch && bMatch) {
            const aNum = parseFloat(aMatch[0].replace(/,/g, ''));
            const bNum = parseFloat(bMatch[0].replace(/,/g, ''));
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return aNum - bNum;
            }
        }
        
        // Fallback to string comparison
        return aValue.localeCompare(bValue);
    }

    /**
     * Compares status values with custom order
     * @param {string} aValue - First status
     * @param {string} bValue - Second status
     * @returns {number} Comparison result
     */
    function compareStatus(aValue, bValue) {
        // Define status priority (lower number = higher priority)
        const statusOrder = {
            // General status
            'active': 1,
            'available': 1,
            'excellent': 1,
            'good': 2,
            'working': 2,
            'fair': 3,
            'low stock': 4,
            'low-stock': 4,
            'needs maintenance': 5,
            'expired': 6,
            'out of stock': 6,
            'out-of-stock': 6,
            'out of order': 7,
            'inactive': 8,
            'n/a': 9
        };
        
        const aPriority = statusOrder[aValue] || 99;
        const bPriority = statusOrder[bValue] || 99;
        
        if (aPriority !== bPriority) {
            return aPriority - bPriority;
        }
        
        // Same priority, use alphabetical
        return aValue.localeCompare(bValue);
    }

    /**
     * Animates sorted rows
     * @param {HTMLTableSectionElement} tbody - Table body element
     */
    function animateSortedRows(tbody) {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            if (!row.querySelector('.empty-state')) {
                row.style.opacity = '0';
                row.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 30);
            }
        });
    }

    // ========================================
    // SEARCH & FILTER FUNCTIONS
    // ========================================

    /**
     * Enhanced search with debouncing
     */
    function enhancedSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            const searchInput = document.getElementById('searchInput');
            const filterSelect = document.getElementById('filterSelect');
            
            if (!searchInput) return;
            
            const searchTerm = searchInput.value;
            const filter = filterSelect ? filterSelect.value : 'all';
            
            // Update URL with search parameters
            const url = new URL(window.location);
            
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }
            
            if (filter && filter !== 'all') {
                url.searchParams.set('filter', filter);
            } else {
                url.searchParams.delete('filter');
            }
            
            // Reload page with new parameters
            window.location.href = url.toString();
        }, 500);
    }

    /**
     * Clear all filters and search
     */
    function clearFilters() {
        const searchInput = document.getElementById('searchInput');
        const filterSelect = document.getElementById('filterSelect');
        
        if (searchInput) searchInput.value = '';
        if (filterSelect) filterSelect.value = 'all';
        
        // Remove search parameters from URL
        const url = new URL(window.location);
        url.searchParams.delete('search');
        url.searchParams.delete('filter');
        window.location.href = url.toString();
    }

    /**
     * Export table data to CSV
     */
    function exportTableData() {
        const table = document.querySelector('.table');
        if (!table) return;
        
        const headers = [];
        const rows = [];
        
        // Get headers
        table.querySelectorAll('thead th').forEach(th => {
            if (!th.classList.contains('actions-column')) {
                headers.push(th.textContent.trim().replace(/[⇅↑↓]/g, ''));
            }
        });
        
        // Get data rows
        table.querySelectorAll('tbody tr').forEach(tr => {
            if (!tr.querySelector('.empty-state')) {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    // Skip actions column
                    if (!td.classList.contains('actions-cell')) {
                        row.push(getCellValue(tr, index));
                    }
                });
                if (row.length > 0) {
                    rows.push(row);
                }
            }
        });
        
        // Create CSV content
        let csv = headers.join(',') + '\n';
        rows.forEach(row => {
            csv += row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',') + '\n';
        });
        
        // Download CSV
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${getPageType()}_export_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    /**
     * Gets current page type for export filename
     * @returns {string} Page type
     */
    function getPageType() {
        const path = window.location.pathname.toLowerCase();
        
        if (path.includes('medicine')) return 'medicines';
        if (path.includes('supplies')) return 'supplies';
        if (path.includes('equipment')) return 'equipment';
        if (path.includes('patient')) return 'patients';
        if (path.includes('logs')) return 'activity_logs';
        if (path.includes('admin')) return 'admins';
        
        return 'data';
    }

    // ========================================
    // INITIALIZATION
    // ========================================

    /**
     * Initialize all table functionality
     */
    function initialize() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initialize);
            return;
        }
        
        // Initialize sortable tables
        initializeSortable();
        
        // Set up search listeners
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', enhancedSearch);
        }
        
        // Set up filter listeners
        const filterSelect = document.getElementById('filterSelect');
        if (filterSelect) {
            filterSelect.addEventListener('change', enhancedSearch);
        }
        
        // Set up clear button
        const clearButton = document.querySelector('[onclick="clearFilters()"]');
        if (clearButton) {
            clearButton.removeAttribute('onclick');
            clearButton.addEventListener('click', clearFilters);
        }
        
        // Set up export button
        const exportButton = document.querySelector('[onclick="exportTableData()"]');
        if (exportButton) {
            exportButton.removeAttribute('onclick');
            exportButton.addEventListener('click', exportTableData);
        }
    }

    // Auto-initialize
    initialize();

    // Public API
    return {
        initialize: initialize,
        sortTable: sortTable,
        enhancedSearch: enhancedSearch,
        clearFilters: clearFilters,
        exportTableData: exportTableData
    };
})();

// For backward compatibility, expose functions globally
window.initializeSortable = window.TableSort.initialize;
window.enhancedSearch = window.TableSort.enhancedSearch;
window.clearFilters = window.TableSort.clearFilters;
window.exportTableData = window.TableSort.exportTableData;