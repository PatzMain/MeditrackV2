<?php
include '../../api/supplies.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Supplies</title>
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
        <?php
        $currentPage = 'supplies';
        include '../includes/navbar1.php'; 
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Supplies Inventory</h1>
                <p class="section-subtitle">Manage your medical and dental supplies stock</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon supplies">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10,9 9,9 8,9"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['total_supplies'] ?? 0; ?></div>
                        <div class="stat-label">Total Supplies</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['low_stock'] ?? 0; ?></div>
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon danger">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['expired'] ?? 0; ?></div>
                        <div class="stat-label">Expired Items</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon expiring">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['expiring_soon'] ?? 0; ?></div>
                        <div class="stat-label">Expiring Soon</div>
                    </div>
                </div>
            </div>

            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Supply List</h2>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Add Supply
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search supplies..."
                        value="<?php echo htmlspecialchars($search); ?>" onkeyup="enhancedSearch()" autocomplete="off">

                    <select id="filterSelect" class="filter-select" onchange="enhancedSearch()">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Supplies</option>
                        <option value="low-stock" <?php echo $filter === 'low-stock' ? 'selected' : ''; ?>>Low Stock
                        </option>
                        <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                        <option value="expiring-soon" <?php echo $filter === 'expiring-soon' ? 'selected' : ''; ?>>
                            Expiring Soon</option>
                    </select>

                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                </div>
            </div>

            <!-- Supply Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sortable">Supply Name</th>
                            <th class="sortable">Type</th>
                            <th class="sortable">Classification</th>
                            <th class="sortable">Quantity</th>
                            <th class="sortable">Expiry Date</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($supplies)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <p>N/A</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($supplies as $supply): ?>
                                <tr>
                                    <td>
                                        <a href="#" class="supply-name-link"
                                            onclick="showDescription(event, <?php echo htmlspecialchars(json_encode($supply)); ?>)">
                                            <?php echo htmlspecialchars($supply['supply_name'] ?? 'N/A'); ?>
                                        </a>
                                        <?php if ($supply['supply_brand_name']): ?>
                                            <div class="supply-brand">
                                                <?php echo htmlspecialchars($supply['supply_brand_name']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="type-badge <?php echo strtolower($supply['supply_type'] ?? ''); ?>">
                                            <?php echo $supply['supply_type'] ?? 'N/A'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $supply['supply_classification'] ?? 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $quantity = $supply['supply_quantity'] ?? 0;
                                        $quantityClass = '';
                                        if ($quantity == 0) {
                                            $quantityClass = 'out-of-stock';
                                        } elseif ($quantity < 20) {
                                            $quantityClass = 'low-stock';
                                        } else {
                                            $quantityClass = 'available';
                                        }
                                        ?>
                                        <span class="availability-badge <?php echo $quantityClass; ?>">
                                            <?php echo $quantity; ?> <?php echo $supply['supply_unit'] ?? 'units'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($supply['supply_expiry_date']): ?>
                                            <span
                                                class="expiry-date <?php echo getExpiryClass($supply['supply_expiry_date']); ?>">
                                                <?php echo date('M d, Y', strtotime($supply['supply_expiry_date'])); ?>
                                            </span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="action-btn edit-btn"
                                            onclick="editSupply(<?php echo $supply['supply_id']; ?>)"
                                            title="Edit Supply">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                            </svg>
                                        </button>
                                        <button class="action-btn delete-btn"
                                            onclick="deleteSupply(<?php echo $supply['supply_id']; ?>)"
                                            title="Delete Supply">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
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
                <h2 class="modal-title" id="descModalTitle">Supply Details</h2>
                <span class="close" onclick="closeModal('descriptionModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <label>Supply Name:</label>
                    <div id="descName" class="detail-value">N/A</div>
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

    <!-- Add/Edit Supply Modal -->
    <div id="supplyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Supply</h2>
                <span class="close" onclick="closeModal('supplyModal')">&times;</span>
            </div>
            <form id="supplyForm">
                <input type="hidden" id="supply_id" name="supply_id">
                <input type="hidden" id="form_action" name="action" value="add_supply">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Supply Name *</label>
                        <input type="text" name="supply_name" class="form-input" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Brand Name</label>
                        <input type="text" name="supply_brand_name" class="form-input" autocomplete="off">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Type *</label>
                        <select name="supply_type" class="form-input" required>
                            <option value="">Select Type</option>
                            <option value="Medical">Medical</option>
                            <option value="Dental">Dental</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit</label>
                        <input type="text" name="supply_unit" class="form-input" placeholder="e.g., pieces, boxes"
                            autocomplete="off" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="supply_quantity" class="form-input" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="supply_expiry_date" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Classification</label>
                    <select name="supply_classification" class="form-input">
                        <option value="">Select Classification</option>
                        <option value="Syringe">Syringe</option>
                        <option value="Gloves">Gloves</option>
                        <option value="Bandage">Bandage</option>
                        <option value="Cotton">Cotton</option>
                        <option value="Alcohol Swab">Alcohol Swab</option>
                        <option value="Face Mask">Face Mask</option>
                        <option value="IV Set">IV Set</option>
                        <option value="Thermometer Cover">Thermometer Cover</option>
                        <option value="Dental Bib">Dental Bib</option>
                        <option value="Cotton Roll">Cotton Roll</option>
                        <option value="Dental Floss">Dental Floss</option>
                        <option value="Dental Impression Material">Dental Impression Material</option>
                        <option value="Saliva Ejector">Saliva Ejector</option>
                        <option value="Dental Tray Cover">Dental Tray Cover</option>
                        <option value="Disinfectant">Disinfectant</option>
                        <option value="Protective Gown">Protective Gown</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="supply_description" class="form-input" rows="4"
                        placeholder="Enter supply description..."></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal('supplyModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirm Deletion</h2>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <div class="delete-icon">⚠</div>
            <div class="delete-message">Are you sure you want to delete this supply?</div>
            <div class="delete-submessage">This action cannot be undone.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteSupply()">Delete</button>
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script src="../js/supplies.js"></script>
</body>

</html>