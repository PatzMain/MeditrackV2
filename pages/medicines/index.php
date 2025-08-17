<?php
/**
 * Example Usage of the Table Management System
 * This shows how to implement the table components in your pages
 */

// Include required files
require_once '../../api/connection.php';
require_once '../../api/table_config.php'; // Adjust path as necessary
require_once '../../api/TableLayout.php';

// Example: Display a users table
$tableName = 'medical_medicines'; // This should match a key in your table_config.php

// Create table layout with options
$tableLayout = new TableLayout($tableName, [
    'show_search' => true,
    'show_filters' => true,
    'show_add_button' => true,
    'show_export' => true,
    'show_bulk_actions' => true,
    'per_page' => 25,
    'ajax_url' => '../../api/table_ajax.php',
    'add_url' => 'add_user.php',
    'edit_url' => 'edit_user.php',
    'delete_url' => 'delete_user.php',
    'export_formats' => ['csv', 'pdf', 'docx']
]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Inventory - Health Center Management System</title>
    <?php include '../includes/styles.php'; ?>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $currentPage = 'medicines';
        include '../includes/navbar.php';
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <?php
            $pageKey = 'medicines';
            include '../includes/page-header.php';
            ?>
            <!-- Statistics Cards -->
            <?php
            $pageKey = 'medicines';
            include '../includes/stats-cards.php';
            ?>



            <!-- Medicine Table -->
            <!-- Render the table -->
                <?php echo $tableLayout->render(); ?>
        </main>
    </div>

    <!-- Description Modal -->
    <div id="descriptionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="descModalTitle">Medicine Details</h2>
                <span class="close" onclick="closeModal('descriptionModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <label>Medicine Name:</label>
                    <div id="descName" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Generic Name:</label>
                    <div id="descGeneric" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Brand Name:</label>
                    <div id="descBrand" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Type:</label>
                    <div id="descType" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Classification:</label>
                    <div id="descClassification" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Description:</label>
                    <div id="descDescription" class="detail-value">N/A</div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-primary" onclick="closeModal('descriptionModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Medicine Modal -->
    <div id="medicineModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Medicine</h2>
                <span class="close" onclick="closeModal('medicineModal')">&times;</span>
            </div>
            <form id="medicineForm">
                <input type="hidden" id="medicine_id" name="medicine_id">
                <input type="hidden" id="form_action" name="action" value="add_medicine">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Medicine Name *</label>
                        <input type="text" name="medicine_name" class="form-input" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Generic Name</label>
                        <input type="text" name="medicine_generic_name" class="form-input" autocomplete="off">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Brand Name</label>
                        <input type="text" name="medicine_brand_name" class="form-input" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type *</label>
                        <select name="medicine_type" class="form-input" required>
                            <option value="">Select Type</option>
                            <option value="Medical">Medical</option>
                            <option value="Dental">Dental</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Dosage *</label>
                        <input type="number" name="medicine_dosage" class="form-input" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit *</label>
                        <input type="text" name="medicine_unit" class="form-input" placeholder="e.g., tablets, ml"
                            autocomplete="off" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="medicine_stock" class="form-input" min="0" autocomplete="off"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="medicine_expiry_date" class="form-input" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Classification</label>
                    <input type="text" name="medicine_classification" class="form-input"
                        placeholder="e.g., Antibiotic, Painkiller" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="medicine_description" class="form-input" rows="3"
                        placeholder="Enter medicine description..." autocomplete="off"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal('medicineModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Medicine</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal delete-modal">
        <div class="modal-content">
            <div class="delete-icon">⚠</div>
            <h3 class="delete-message">Delete Medicine?</h3>
            <p class="delete-submessage">This action cannot be undone. The medicine will be permanently removed from the
                inventory.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteMedicine()">Delete</button>
            </div>
        </div>
    </div>
    <script src="../js/navbar.js"></script>
    <script src="../js/sort.js"></script>
    <script src="../js/medicines.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include your sort.js file here -->
    <script src="sort.js"></script>
    
    <!-- Additional JavaScript for enhanced functionality -->
    <script>
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Row selection enhancement
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                const row = e.target.closest('tr');
                if (e.target.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            }
        });
        
        // Double-click to edit
        document.addEventListener('dblclick', function(e) {
            const row = e.target.closest('tr[data-row-id]');
            if (row && !e.target.closest('.btn')) {
                const editBtn = row.querySelector('.action-edit');
                if (editBtn) {
                    editBtn.click();
                }
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+A to select all
            if (e.ctrlKey && e.key === 'a' && document.activeElement.tagName !== 'INPUT') {
                e.preventDefault();
                const selectAll = document.querySelector('#select-all-' + getCurrentTableName());
                if (selectAll) {
                    selectAll.click();
                }
            }
            // Esc to deselect all
            if (e.key === 'Escape') {
                e.preventDefault();
                // Deselect all rows
                const selectedRows = document.querySelectorAll('tr.selected');
                selectedRows.forEach(row => {
                    const checkbox = row.querySelector('.row-checkbox');
                    if (checkbox) {
                        checkbox.checked = false;
                        row.classList.remove('selected');
                    }
                });
            }
            
            // Ctrl+F to focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('[id^="table-search-"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });
        
        // Helper function to get current table name
        function getCurrentTableName() {
            const container = document.querySelector('.table-layout-container');
            return container ? container.dataset.table : '';
        }
        
        // Auto-refresh option (optional)
        let autoRefreshInterval = null;
        function toggleAutoRefresh(enable, intervalSeconds = 30) {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
            
            if (enable) {
                autoRefreshInterval = setInterval(() => {
                    const refreshBtn = document.querySelector('[id^="refresh-table-"]');
                    if (refreshBtn) {
                        refreshBtn.click();
                    }
                }, intervalSeconds * 1000);
            }
        }
        
        // Example: Enable auto-refresh every 30 seconds
        // toggleAutoRefresh(true, 30);
    </script>
</body>

</html>