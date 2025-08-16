<?php
include '../../api/auth.php';
// Include the required files
require_once '../../api/TableRenderer.php';
require_once '../../api/TableFetcher.php';

$config = include '../../config/medicines_table.php'; // Your configuration array
// Get request parameters
$params = [
    'sort_column' => $_GET['sort_column'] ?? $config['medical_medicines']['default_sort']['column'],
    'sort_direction' => $_GET['sort_direction'] ?? $config['medical_medicines']['default_sort']['direction'],
    'search_query' => $_GET['search_query'] ?? '',
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'items_per_page' => isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 10
];

// Initialize classes
$fetcher = new TableFetcher($pdo, $config);
$renderer = new TableRenderer($config);

// Fetch data
try {
    $result = $fetcher->fetchTableData('medical_medicines', $params);
    $data = $result['data'];
    $pagination = $result['pagination'];
    $activeFilters = $result['filters'];
} catch (Exception $e) {
    $data = [];
    $pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
    $activeFilters = [];
    $error_message = "Error fetching data: " . $e->getMessage();
}
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
        include '../includes/navbar1.php';
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


            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Medicine List</h2>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Add Medicine
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <?php include '../includes/search.php'; ?>

            <!-- Medicine Table -->
            <?php
            $tableKey = 'medical_medicines';
            include '../includes/render_table.php';
            ?>
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
</body>

</html>