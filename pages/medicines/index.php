<?php
include '../../api/medicines.php';

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'medicine_name';
$direction = $_GET['direction'] ?? 'asc';

// Validate sort column and direction
$allowed_columns = ['medicine_name', 'medicine_type', 'medicine_stock', 'medicine_expiry_date'];
$sort = in_array($sort, $allowed_columns) ? $sort : 'medicine_name';
$direction = ($direction === 'desc') ? 'DESC' : 'ASC';

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(medicine_name LIKE ? OR medicine_generic_name LIKE ? OR medicine_brand_name LIKE ? OR medicine_type LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

// Filter conditions
switch ($filter) {
    case 'low-stock':
        $where_conditions[] = "medicine_stock <= 10";
        break;
    case 'expired':
        $where_conditions[] = "medicine_expiry_date < CURDATE()";
        break;
    case 'expiring-soon':
        $where_conditions[] = "medicine_expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        break;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get medicines
$sql = "SELECT * FROM medicines $where_clause ORDER BY $sort $direction";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN medicine_stock <= 10 THEN 1 ELSE 0 END) as low_stock,
    SUM(CASE WHEN medicine_expiry_date < CURDATE() THEN 1 ELSE 0 END) as expired,
    SUM(CASE WHEN medicine_expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_soon
FROM medicines";
$stats_stmt = $pdo->query($stats_sql);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
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
            $config = include '../../config/table_config.php';
            $data = [
                [
                    'medicine_id' => 1,
                    'medicine_name' => 'Paracetamol',
                    'medicine_brand_name' => 'Biogesic',
                    'medicine_type' => 'Tablet',
                    'medicine_dosage' => '500',
                    'medicine_unit' => 'mg',
                    'medicine_stock' => 25,
                    'medicine_expiry_date' => '2025-12-01'
                ],
                [
                    'medicine_id' => 2,
                    'medicine_name' => 'Amoxicillin',
                    'medicine_brand_name' => 'Amoxil',
                    'medicine_type' => 'Capsule',
                    'medicine_dosage' => '250',
                    'medicine_unit' => 'mg',
                    'medicine_stock' => 5,
                    'medicine_expiry_date' => '2025-08-20'
                ]
            ];

            renderMedicinesTable($data, $config['medicines']);

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