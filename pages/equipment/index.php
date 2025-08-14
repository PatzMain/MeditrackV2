<?php
include '../../api/equipment.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Equipment</title>
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
                <a href="../equipment/" class="nav-item active">
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
            <div class="page-header">
                <h1 class="page-title">Equipment Management</h1>
                <p class="section-subtitle">Manage medical and dental equipment inventory</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon equipment">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['total_equipment'] ?? 0; ?></div>
                        <div class="stat-label">Total Equipment</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['working'] ?? 0; ?></div>
                        <div class="stat-label">Working</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['needs_maintenance'] ?? 0; ?></div>
                        <div class="stat-label">Needs Maintenance</div>
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
                        <div class="stat-number"><?php echo $stats['out_of_order'] ?? 0; ?></div>
                        <div class="stat-label">Out of Order</div>
                    </div>
                </div>
            </div>

            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Equipment List</h2>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Add Equipment
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search equipment..."
                        value="<?php echo htmlspecialchars($search); ?>" onkeyup="enhancedSearch()" autocomplete="off">

                    <select id="filterSelect" class="filter-select" onchange="enhancedSearch()">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Equipment</option>
                        <option value="working" <?php echo $filter === 'working' ? 'selected' : ''; ?>>Working</option>
                        <option value="maintenance" <?php echo $filter === 'maintenance' ? 'selected' : ''; ?>>Needs Maintenance</option>
                        <option value="out-of-order" <?php echo $filter === 'out-of-order' ? 'selected' : ''; ?>>Out of Order</option>
                    </select>

                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                </div>
            </div>

            <!-- Equipment Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sortable">Equipment Name</th>
                            <th class="sortable">Type</th>
                            <th class="sortable">Classification</th>
                            <th class="sortable">Serial Number</th>
                            <th class="sortable">Condition</th>
                            <th class="sortable">Location</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipment)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <p>N/A</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($equipment as $item): ?>
                                <tr>
                                    <td>
                                        <a href="#" class="equipment-name-link"
                                            onclick="showDescription(event, <?php echo htmlspecialchars(json_encode($item)); ?>)">
                                            <?php echo htmlspecialchars($item['equipment_name'] ?? 'N/A'); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="type-badge <?php echo strtolower($item['equipment_type'] ?? ''); ?>">
                                            <?php echo $item['equipment_type'] ?? 'N/A'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item['equipment_classification'] ?? 'N/A'; ?></td>
                                    <td><?php echo $item['serial_number'] ?? 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $condition = $item['equipment_condition'] ?? '';
                                        $conditionClass = '';
                                        switch(strtolower($condition)) {
                                            case 'excellent':
                                            case 'good':
                                            case 'working':
                                                $conditionClass = 'working';
                                                break;
                                            case 'needs maintenance':
                                            case 'fair':
                                                $conditionClass = 'maintenance';
                                                break;
                                            case 'out of order':
                                            case 'broken':
                                            case 'poor':
                                                $conditionClass = 'out-of-order';
                                                break;
                                            default:
                                                $conditionClass = 'working';
                                        }
                                        ?>
                                        <span class="condition-badge <?php echo $conditionClass; ?>">
                                            <?php echo $condition ?: 'N/A'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item['equipment_location'] ?? 'N/A'; ?></td>
                                    <td class="actions-cell">
                                        <button class="action-btn edit-btn"
                                            onclick="editEquipment(<?php echo $item['equipment_id']; ?>)"
                                            title="Edit Equipment">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                            </svg>
                                        </button>
                                        <button class="action-btn delete-btn"
                                            onclick="deleteEquipment(<?php echo $item['equipment_id']; ?>)"
                                            title="Delete Equipment">
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
                <h2 class="modal-title" id="descModalTitle">Equipment Details</h2>
                <span class="close" onclick="closeModal('descriptionModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <label>Equipment Name:</label>
                    <div id="descName" class="detail-value">N/A</div>
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
                    <label>Serial Number:</label>
                    <div id="descSerial" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Condition:</label>
                    <div id="descCondition" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Location:</label>
                    <div id="descLocation" class="detail-value">N/A</div>
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

    <!-- Add/Edit Equipment Modal -->
    <div id="equipmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Equipment</h2>
                <span class="close" onclick="closeModal('equipmentModal')">&times;</span>
            </div>
            <form id="equipmentForm">
                <input type="hidden" id="equipment_id" name="equipment_id">
                <input type="hidden" id="form_action" name="action" value="add_equipment">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Equipment Name *</label>
                        <input type="text" name="equipment_name" class="form-input" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type *</label>
                        <select name="equipment_type" class="form-input" required>
                            <option value="">Select Type</option>
                            <option value="Medical">Medical</option>
                            <option value="Dental">Dental</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_number" class="form-input" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Condition</label>
                        <select name="equipment_condition" class="form-input">
                            <option value="">Select Condition</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Needs Maintenance">Needs Maintenance</option>
                            <option value="Out of Order">Out of Order</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="equipment_location" class="form-input" placeholder="e.g., Room 101, Dental Office"
                            autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Classification</label>
                        <select name="equipment_classification" class="form-input">
                            <option value="">Select Classification</option>
                            <option value="Stethoscope">Stethoscope</option>
                            <option value="Blood Pressure Monitor">Blood Pressure Monitor</option>
                            <option value="Otoscope">Otoscope</option>
                            <option value="Weighing Scale">Weighing Scale</option>
                            <option value="Nebulizer">Nebulizer</option>
                            <option value="Dental Chair">Dental Chair</option>
                            <option value="Dental Drill">Dental Drill</option>
                            <option value="Ultrasonic Scaler">Ultrasonic Scaler</option>
                            <option value="Curing Light">Curing Light</option>
                            <option value="X-ray Machine">X-ray Machine</option>
                            <option value="Sterilizer/Autoclave">Sterilizer/Autoclave</option>
                            <option value="Examination Light">Examination Light</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="equipment_description" class="form-input" rows="4"
                        placeholder="Enter equipment description..."></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal('equipmentModal')">Cancel</button>
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
            <div class="delete-message">Are you sure you want to delete this equipment?</div>
            <div class="delete-submessage">This action cannot be undone.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteEquipment()">Delete</button>
            </div>
        </div>
    </div>
    <script src="../js/sort.js"></script>
    <script src="../js/navbar.js"></script>
    <script src="../js/equipment.js"></script>
</body>

</html>