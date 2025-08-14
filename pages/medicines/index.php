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
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/cards.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/search.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>MediTrack</h2>
            </div>
            <div class="nav-menu">
                <a href="../dashboard/" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Dashboard
                </a>

                <!-- Patient Monitoring Dropdown -->
                <div class="nav-dropdown">
                    <a href="#" class="nav-item dropdown-toggle" onclick="toggleDropdown(event)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Patient Monitoring
                        <svg class="dropdown-arrow" width="25" height="25" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z" />
                        </svg>
                    </a>
                    <div class="dropdown-content">
                        <a href="../patient_monitoring/patient_consultation/">Patient Consultation</a>
                        <a href="../patient_monitoring/vital_signs/">Vital Signs</a>
                        <a href="../patient_monitoring/medical_records/">Medical Records</a>
                        <a href="../patient_monitoring/nursing_notes/">Nursing Notes</a>
                    </div>
                </div>

                <a href="../medicines/" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="2" />
                        <path d="M21 15.5C21 15.5 16 10.5 12 15.5" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Medicines
                </a>
                <a href="../supplies/" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Supplies
                </a>
                <a href="../equipment/" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <line x1="8" y1="21" x2="16" y2="21" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Equipment
                </a>
                <?php if ($_SESSION['role'] === 'superadmin'): ?>
                    <a href="../admin_management/" class="nav-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                        </svg>
                        Admin Management
                    </a>
                <?php endif; ?>
                <a href="../logs/" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <polyline points="10,9 9,9 8,9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Activity Logs
                </a>
            </div>
            <div class="logout">
                <a href="../logout/" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Logout
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title">Medicines</h1>
                <p class="section-subtitle">Manage medical and dental medicines inventory</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['total'] ?: 0; ?></div>
                        <div class="stat-label">Total Medicines</div>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['low_stock'] ?: 0; ?></div>
                        <div class="stat-label">Low Stock</div>
                    </div>
                </div>
                <div class="stat-card error">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v2h-2v-2zm0-8h2v6h-2V9z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['expired'] ?: 0; ?></div>
                        <div class="stat-label">Expired</div>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['expiring_soon'] ?: 0; ?></div>
                        <div class="stat-label">Expiring Soon</div>
                    </div>
                </div>
            </div>

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
            <div class="filter-section">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search medicines..."
                        value="<?php echo htmlspecialchars($search); ?>" onkeyup="enhancedSearch()" autocomplete="off">

                    <select id="filterSelect" class="filter-select" onchange="enhancedSearch()">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Medicines</option>
                        <option value="low-stock" <?php echo $filter === 'low-stock' ? 'selected' : ''; ?>>Low Stock</option>
                        <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                        <option value="expiring-soon" <?php echo $filter === 'expiring-soon' ? 'selected' : ''; ?>>Expiring Soon</option>
                    </select>

                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                </div>
            </div>

            <!-- Medicine Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('medicine_name')">Medicine Name</th>
                            <th class="sortable" onclick="sortTable('medicine_type')">Type</th>
                            <th class="sortable">Dosage</th>
                            <th class="sortable" onclick="sortTable('medicine_stock')">Stock</th>
                            <th class="sortable" onclick="sortTable('medicine_expiry_date')">Expiry Date</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($medicines)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <p>No medicines found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($medicines as $medicine): ?>
                                <tr>
                                    <td>
                                        <a href="#" class="medicine-name-link"
                                            onclick="showDescription(event, <?php echo htmlspecialchars(json_encode($medicine)); ?>)">
                                            <?php echo htmlspecialchars($medicine['medicine_name'] ?? 'N/A'); ?>
                                        </a>
                                        <?php if (!empty($medicine['medicine_brand_name'])): ?>
                                            <div class="medicine-brand">
                                                <?php echo htmlspecialchars($medicine['medicine_brand_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="type-badge <?php echo strtolower($medicine['medicine_type'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($medicine['medicine_type'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($medicine['medicine_dosage'] ?? 'N/A'); ?>
                                        <?php if (!empty($medicine['medicine_unit'])): ?>
                                            <?php echo htmlspecialchars($medicine['medicine_unit']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="stock-count <?php echo ($medicine['medicine_stock'] ?? 0) <= 10 ? 'low-stock' : ''; ?>">
                                            <?php echo ($medicine['medicine_stock'] ?? 0); ?>
                                        </span>
                                        <span class="stock-unit">
                                            <?php echo !empty($medicine['medicine_unit']) ? htmlspecialchars($medicine['medicine_unit']) : 'units'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($medicine['medicine_expiry_date'])): ?>
                                            <span class="expiry-date <?php echo getExpiryClass($medicine['medicine_expiry_date']); ?>">
                                                <?php echo date('M d, Y', strtotime($medicine['medicine_expiry_date'])); ?>
                                            </span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="action-btn edit-btn"
                                            onclick="editMedicine(<?php echo $medicine['medicine_id']; ?>)"
                                            title="Edit Medicine">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                            </svg>
                                        </button>
                                        <button class="action-btn delete-btn"
                                            onclick="deleteMedicine(<?php echo $medicine['medicine_id']; ?>)"
                                            title="Delete Medicine">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
                        <input type="text" name="medicine_unit" class="form-input" placeholder="e.g., tablets, ml" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="medicine_stock" class="form-input" min="0" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="medicine_expiry_date" class="form-input" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Classification</label>
                    <input type="text" name="medicine_classification" class="form-input" placeholder="e.g., Antibiotic, Painkiller" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="medicine_description" class="form-input" rows="3" placeholder="Enter medicine description..." autocomplete="off"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('medicineModal')">Cancel</button>
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
            <p class="delete-submessage">This action cannot be undone. The medicine will be permanently removed from the inventory.</p>
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