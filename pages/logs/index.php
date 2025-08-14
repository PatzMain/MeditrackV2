<?php
include '../../api/logs.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Activity Logs</title>
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
                <a href="../logs/" class="nav-item active">
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
                <h1 class="page-title">Activity Logs</h1>
                <p class="section-subtitle">Monitor all system activities and track user actions</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon logs">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['total_logs'] ?? 0; ?></div>
                        <div class="stat-label">Total Logs</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon today">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['today_logs'] ?? 0; ?></div>
                        <div class="stat-label">Today's Activities</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon medicine">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['medicine_logs'] ?? 0; ?></div>
                        <div class="stat-label">Medicine Activities</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon patient">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $stats['patient_logs'] ?? 0; ?></div>
                        <div class="stat-label">Patient Activities</div>
                    </div>
                </div>
            </div>

            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Activity Log List</h2>
                <div class="action-buttons">
                    <button class="btn btn-secondary" onclick="exportLogs()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                        </svg>
                        Export Logs
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search logs..."
                        value="<?php echo htmlspecialchars($search); ?>" onkeyup="enhancedSearch()" autocomplete="off">

                    <select id="filterSelect" class="filter-select" onchange="enhancedSearch()">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Activities</option>
                        <option value="medicine" <?php echo $filter === 'medicine' ? 'selected' : ''; ?>>Medicine</option>
                        <option value="supply" <?php echo $filter === 'supply' ? 'selected' : ''; ?>>Supply</option>
                        <option value="equipment" <?php echo $filter === 'equipment' ? 'selected' : ''; ?>>Equipment</option>
                        <option value="patient" <?php echo $filter === 'patient' ? 'selected' : ''; ?>>Patient</option>
                        <option value="authentication" <?php echo $filter === 'authentication' ? 'selected' : ''; ?>>Authentication</option>
                        <option value="system" <?php echo $filter === 'system' ? 'selected' : ''; ?>>System</option>
                    </select>

                    <select id="dateFilter" class="filter-select" onchange="enhancedSearch()">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                </div>
            </div>

            <!-- Activity Logs Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sortable">Timestamp</th>
                            <th class="sortable">User</th>
                            <th class="sortable">Activity Type</th>
                            <th class="sortable">Item</th>
                            <th class="sortable">Description</th>
                            <th class="sortable">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <p>No activity logs found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <div class="timestamp">
                                            <?php echo date('M d, Y', strtotime($log['logs_timestamp'])); ?>
                                        </div>
                                        <div class="time">
                                            <?php echo date('H:i:s', strtotime($log['logs_timestamp'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <?php echo htmlspecialchars($log['username'] ?? 'System'); ?>
                                        </div>
                                        <?php if (isset($log['patient_name'])): ?>
                                            <div class="patient-info">
                                                Patient: <?php echo htmlspecialchars($log['patient_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="activity-badge <?php echo strtolower($log['logs_item_type'] ?? 'system'); ?>">
                                            <?php echo ucfirst($log['logs_item_type'] ?? 'System'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($log['logs_item_name']): ?>
                                            <div class="item-name">
                                                <?php echo htmlspecialchars($log['logs_item_name']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                        <?php if ($log['logs_quantity']): ?>
                                            <div class="quantity">
                                                Qty: <?php echo $log['logs_quantity']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="description">
                                            <?php echo htmlspecialchars($log['logs_description'] ?? 'N/A'); ?>
                                        </div>
                                        <?php if ($log['logs_reason']): ?>
                                            <div class="reason">
                                                Reason: <?php echo htmlspecialchars($log['logs_reason']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($log['logs_status']): ?>
                                            <span class="status-badge <?php echo getStatusClass($log['logs_status']); ?>">
                                                <?php echo ucfirst($log['logs_status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Log Details Modal -->
    <div id="logDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="logModalTitle">Activity Log Details</h2>
                <span class="close" onclick="closeModal('logDetailsModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <label>Log ID:</label>
                    <div id="logId" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Timestamp:</label>
                    <div id="logTimestamp" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>User:</label>
                    <div id="logUser" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Activity Type:</label>
                    <div id="logActivityType" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Item Name:</label>
                    <div id="logItemName" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Description:</label>
                    <div id="logDescription" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Reason:</label>
                    <div id="logReason" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Quantity:</label>
                    <div id="logQuantity" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Status:</label>
                    <div id="logStatus" class="detail-value">N/A</div>
                </div>
                <div class="detail-row">
                    <label>Patient:</label>
                    <div id="logPatient" class="detail-value">N/A</div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-primary" onclick="closeModal('logDetailsModal')">Close</button>
            </div>
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
            <div class="delete-message">Are you sure you want to delete this log entry?</div>
            <div class="delete-submessage">This action cannot be undone.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteLog()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Date Range Modal -->
    <div id="dateRangeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Select Date Range</h2>
                <span class="close" onclick="closeModal('dateRangeModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" id="startDate" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" id="endDate" class="form-input">
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('dateRangeModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyDateRange()">Apply</button>
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script src="../js/sort.js"></script>
    <script src="../js/logs.js"></script>
</body>

</html>