<?php
include '../../api/dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Enhanced Dashboard</title>
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/cards.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/search.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/charts.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-adapter-date-fns/3.0.0/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php
        $currentPage = 'dashboard';
        include '../includes/navbar1.php';
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Enhanced Dashboard</h1>
                <p class="section-subtitle">Comprehensive analytics and insights for your medical inventory system</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon medicines">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $medicines_count; ?></div>
                        <div class="stat-label">Total Medicines</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $supplies_count; ?></div>
                        <div class="stat-label">Total Supplies</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon danger">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $equipment_count; ?></div>
                        <div class="stat-label">Equipment Items</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon expiring">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm3.55 13.8l-4.08-2.51c-.3-.18-.48-.5-.48-.85V8c0-.55.45-1 1-1s1 .45 1 1v4.07l3.27 2.01c.46.28.6.91.32 1.37-.17.29-.48.45-.8.45-.17 0-.35-.04-.5-.14-.01.01-.01.01-.02.01z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo $expiring_soon; ?></div>
                        <div class="stat-label">Items Expiring Soon</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- Chart 1: Inventory Overview -->
                <div class="chart-card donut-chart" data-chart="inventoryOverview">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Inventory Overview</h3>
                            <p class="chart-subtitle">Distribution of inventory items</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="inventoryOverviewChart"></canvas>
                    </div>
                    <div class="chart-stats" data-chart="inventoryOverview"></div>
                </div>

                <!-- Chart 2: Stock Levels -->
                <div class="chart-card" data-chart="stockLevels">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Stock Levels</h3>
                            <p class="chart-subtitle">Current stock status distribution</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="stockLevelsChart"></canvas>
                    </div>
                    <div class="chart-stats" data-chart="stockLevels"></div>
                </div>

                <!-- Chart 3: Expiry Timeline -->
                <div class="chart-card full-width" data-chart="expiryTimeline">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Expiry Timeline</h3>
                            <p class="chart-subtitle">Items expiring over time periods</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                            <button class="chart-export-btn" title="Export Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                    <div class="chart-container large">
                        <canvas id="expiryTimelineChart"></canvas>
                    </div>
                    <div class="chart-stats" data-chart="expiryTimeline"></div>
                </div>

                <!-- Chart 4: Category Distribution -->
                <div class="chart-card horizontal" data-chart="categoryDistribution">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Category Distribution</h3>
                            <p class="chart-subtitle">Top medicine classifications</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="categoryDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Chart 5: Monthly Usage Trend -->
                <div class="chart-card line-chart" data-chart="monthlyUsage">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Monthly Usage Trend</h3>
                            <p class="chart-subtitle">Activity trends over time</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="monthlyUsageChart"></canvas>
                    </div>
                </div>

                <!-- Chart 6: Medicine Types -->
                <div class="chart-card" data-chart="medicineTypes">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Medicine Types</h3>
                            <p class="chart-subtitle">Medical vs Dental distribution</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="medicineTypesChart"></canvas>
                    </div>
                </div>

                <!-- Chart 7: Equipment Conditions -->
                <div class="chart-card donut-chart" data-chart="equipmentConditions">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Equipment Conditions</h3>
                            <p class="chart-subtitle">Current equipment status</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="equipmentConditionsChart"></canvas>
                    </div>
                </div>

                <!-- Chart 8: Supply Quantities -->
                <div class="chart-card bar-chart" data-chart="supplyQuantities">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Supply Quantities</h3>
                            <p class="chart-subtitle">Top 10 supplies by quantity</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="supplyQuantitiesChart"></canvas>
                    </div>
                </div>

                <!-- Chart 9: Patient Status -->
                <div class="chart-card donut-chart" data-chart="patientStatus">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Patient Status</h3>
                            <p class="chart-subtitle">Current patient distribution</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="patientStatusChart"></canvas>
                    </div>
                </div>

                <!-- Chart 10: Vital Signs Trends -->
                <div class="chart-card line-chart multi-axis full-width" data-chart="vitalTrends">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Vital Signs Trends</h3>
                            <p class="chart-subtitle">Average vital signs over time</p>
                        </div>
                        <div class="chart-controls">
                            <select class="chart-filter" data-filter="period">
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container large">
                        <canvas id="vitalTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Chart 11: Assessment Types -->
                <div class="chart-card radar-chart" data-chart="assessmentTypes">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Assessment Types</h3>
                            <p class="chart-subtitle">Medical assessments distribution</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="assessmentTypesChart"></canvas>
                    </div>
                </div>

                <!-- Chart 12: Nursing Shifts -->
                <div class="chart-card" data-chart="nursingShifts">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Nursing Shifts</h3>
                            <p class="chart-subtitle">Notes by shift distribution</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="nursingShiftsChart"></canvas>
                    </div>
                </div>

                <!-- Chart 13: Activity Logs -->
                <div class="chart-card bar-chart" data-chart="activityLogs">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Activity Logs</h3>
                            <p class="chart-subtitle">System activities by type</p>
                        </div>
                        <div class="chart-controls">
                            <select class="chart-filter" data-filter="period">
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="activityLogsChart"></canvas>
                    </div>
                </div>

                <!-- Chart 14: Expiry Alerts -->
                <div class="chart-card donut-chart" data-chart="expiryAlerts">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Expiry Alerts</h3>
                            <p class="chart-subtitle">Critical expiration warnings</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="expiryAlertsChart"></canvas>
                        <div class="donut-center-text">
                            <div class="donut-center-value" id="totalAlerts">-</div>
                            <div class="donut-center-label">Total Alerts</div>
                        </div>
                    </div>
                </div>

                <!-- Chart 15: Medicine Classification -->
                <div class="chart-card horizontal" data-chart="medicineClassification">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Medicine Classification</h3>
                            <p class="chart-subtitle">Top classifications by count</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="medicineClassificationChart"></canvas>
                    </div>
                </div>

                <!-- Chart 16: Patient Demographics -->
                <div class="chart-card" data-chart="patientDemographics">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Patient Demographics</h3>
                            <p class="chart-subtitle">Age group distribution</p>
                        </div>
                        <div class="chart-controls">
                            <button class="chart-refresh-btn" title="Refresh Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                            <button class="chart-export-btn" title="Export Chart">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="patientDemographicsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="quick-actions-panel">
                <div class="panel-header">
                    <h3>Quick Actions</h3>
                    <p>Frequently used dashboard functions</p>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="dashboardCharts.refreshAllCharts()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                        </svg>
                        Refresh All Charts
                    </button>
                    <button class="btn btn-secondary" onclick="dashboardCharts.exportAllCharts()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                        Export All Charts
                    </button>
                    <button class="btn btn-success" onclick="generateReport()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 2H6C5 2 4 3 4 4V20C4 21 5 22 6 22H18C19 22 20 21 20 20V8L14 2Z"/>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="recent-activities">
                <h3>Recent System Activities</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon add">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">New medicine inventory added</div>
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
                            <div class="activity-title">Low stock alert triggered</div>
                            <div class="activity-time">4 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon update">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Patient record updated</div>
                            <div class="activity-time">6 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon add">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">New equipment registered</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon update">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Supply order processed</div>
                            <div class="activity-time">2 days ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <!-- Chart Notification Styles -->
    <style>
        .chart-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--form-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-heavy);
            border: 2px solid rgba(15, 123, 15, 0.1);
            z-index: 10000;
            animation: slideInNotification 0.3s ease-out;
            min-width: 300px;
            max-width: 400px;
        }

        .chart-notification.success {
            border-color: #4CAF50;
        }

        .chart-notification.error {
            border-color: #f44336;
        }

        .chart-notification.warning {
            border-color: #FF9800;
        }

        .notification-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
        }

        .notification-message {
            font-weight: 600;
            color: var(--text-primary);
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 0;
            margin-left: 10px;
        }

        .notification-close:hover {
            color: var(--error-color);
        }

        @keyframes slideInNotification {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .quick-actions-panel {
            background: var(--form-bg);
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-medium);
            border: 2px solid rgba(15, 123, 15, 0.1);
        }

        .panel-header {
            margin-bottom: 20px;
        }

        .panel-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .panel-header p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
            background: var(--form-bg) !important;
            margin: 0 !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .fullscreen .chart-container {
            flex: 1 !important;
            height: auto !important;
            margin: 20px !important;
        }

        .fullscreen .chart-header {
            padding: 20px !important;
            border-bottom: 2px solid rgba(15, 123, 15, 0.1) !important;
        }
    </style>

    <!-- Scripts -->
    <script src="../js/charts.js"></script>
    <script src="../js/navbar.js"></script>
    <script>
        // Additional dashboard functions
        function generateReport() {
            const reportData = {
                medicines: <?php echo $medicines_count; ?>,
                supplies: <?php echo $supplies_count; ?>,
                equipment: <?php echo $equipment_count; ?>,
                expiring: <?php echo $expiring_soon; ?>,
                timestamp: new Date().toISOString()
            };

            const reportContent = `
                MediTrack Dashboard Report
                Generated: ${new Date().toLocaleString()}
                
                Summary:
                - Total Medicines: ${reportData.medicines}
                - Total Supplies: ${reportData.supplies}
                - Total Equipment: ${reportData.equipment}
                - Items Expiring Soon: ${reportData.expiring}
                
                Charts included: 16 interactive visualizations
                
                This report was automatically generated from the MediTrack system.
            `;

            const blob = new Blob([reportContent], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `meditrack-report-${new Date().toISOString().split('T')[0]}.txt`;
            a.click();
            URL.revokeObjectURL(url);

            // Show success notification
            if (window.dashboardCharts) {
                dashboardCharts.showNotification('Report generated successfully!', 'success');
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'r':
                        e.preventDefault();
                        if (window.dashboardCharts) {
                            dashboardCharts.refreshAllCharts();
                        }
                        break;
                    case 's':
                        e.preventDefault();
                        if (window.dashboardCharts) {
                            dashboardCharts.exportAllCharts();
                        }
                        break;
                    case 'g':
                        e.preventDefault();
                        generateReport();
                        break;
                }
            }
        });

        // Auto-refresh toggle
        let autoRefresh = false;
        let refreshInterval;

        function toggleAutoRefresh() {
            autoRefresh = !autoRefresh;
            if (autoRefresh) {
                refreshInterval = setInterval(() => {
                    if (window.dashboardCharts) {
                        dashboardCharts.refreshAllCharts();
                    }
                }, 300000); // 5 minutes
                console.log('Auto-refresh enabled (5 minutes)');
            } else {
                clearInterval(refreshInterval);
                console.log('Auto-refresh disabled');
            }
        }

        // Update donut center text for expiry alerts
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const totalAlertsElement = document.getElementById('totalAlerts');
                if (totalAlertsElement && window.dashboardCharts) {
                    const expiryChart = dashboardCharts.charts.expiryAlerts;
                    if (expiryChart && expiryChart.data && expiryChart.data.datasets[0]) {
                        const total = expiryChart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                        totalAlertsElement.textContent = total;
                    }
                }
            }, 2000);
        });
    </script>
</body>
</html>