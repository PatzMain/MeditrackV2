<?php
include '../../api/dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Dashboard</title>
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/cards.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/search.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>MediTrack</h2>
            </div>
            <div class="nav-menu">
                <a href="../dashboard/" class="nav-item active">
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
            <div class="page-header">
                <h1 class="page-title">Dashboard Overview</h1>
                <p class="section-subtitle">Comprehensive view of your medical inventory system</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon medicines">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                            <div class="stat-number"><?php echo $medicines_count; ?></div>
                            <div class="stat-label">Total Medicines</div>
                            <!-- <div class="stat-change positive">+12% from last month</div> -->
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon supplies">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 2H6C5 2 4 3 4 4V20C4 21 5 22 6 22H18C19 22 20 21 20 20V8L14 2Z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                            <div class="stat-number"><?php echo $supplies_count; ?></div>
                            <div class="stat-label">Total Supplies</div>
                            <!-- <div class="stat-change positive">+8% from last month</div> -->
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon equipment">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                            <div class="stat-number"><?php echo $equipment_count; ?></div>
                            <div class="stat-label">Equipment Items</div>
                            <!-- <div class="stat-change neutral">No change</div> -->
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon alerts">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                            <div class="stat-number"><?php echo $expiring_soon; ?></div>
                            <div class="stat-label">Items Expiring Soon</div>
                            <!-- <div class="stat-change negative">Needs attention</div> -->
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-container">
                <!-- Inventory Overview Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Inventory Overview</h3>
                        <select class="chart-filter">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>Last 90 days</option>
                        </select>
                    </div>
                    <canvas id="inventoryChart"></canvas>
                </div>

                <!-- Stock Levels Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Stock Level Distribution</h3>
                    </div>
                    <canvas id="stockChart"></canvas>
                </div>

                <!-- Expiry Timeline Chart -->
                <div class="chart-card full-width">
                    <div class="chart-header">
                        <h3>Expiry Timeline</h3>
                    </div>
                    <canvas id="expiryChart"></canvas>
                </div>

                <!-- Category Distribution -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Category Distribution</h3>
                    </div>
                    <canvas id="categoryChart"></canvas>
                </div>

                <!-- Monthly Usage Trend -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Monthly Usage Trend</h3>
                    </div>
                    <canvas id="usageChart"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="recent-activities">
                <h3>Recent Activities</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon add">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">50 units of Paracetamol added</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Amoxicillin stock running low</div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon update">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Equipment maintenance completed</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>