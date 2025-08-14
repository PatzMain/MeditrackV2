// pages/js/charts.js

class DashboardCharts {
    constructor() {
        this.charts = {};
        this.chartConfigs = this.initializeChartConfigs();
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.loadAllCharts());
        } else {
            this.loadAllCharts();
        }
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Auto refresh every 5 minutes
        setInterval(() => this.refreshAllCharts(), 300000);
    }

    initializeChartConfigs() {
        return {
            // Chart 1: Inventory Overview (Doughnut)
            inventoryOverview: {
                type: 'doughnut',
                endpoint: 'inventory_overview',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.label}: ${context.parsed} items`
                            }
                        }
                    }
                }
            },

            // Chart 2: Stock Levels (Polar Area)
            stockLevels: {
                type: 'polarArea',
                endpoint: 'stock_levels',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            ticks: { backdropColor: 'transparent' }
                        }
                    }
                }
            },

            // Chart 3: Expiry Timeline (Bar)
            expiryTimeline: {
                type: 'bar',
                endpoint: 'expiry_timeline',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            },

            // Chart 4: Category Distribution (Horizontal Bar)
            categoryDistribution: {
                type: 'bar',
                endpoint: 'category_distribution',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            },

            // Chart 5: Monthly Usage (Line)
            monthlyUsage: {
                type: 'line',
                endpoint: 'monthly_usage',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    },
                    elements: {
                        point: { hoverRadius: 8 }
                    }
                }
            },

            // Chart 6: Medicine Types (Pie)
            medicineTypes: {
                type: 'pie',
                endpoint: 'medicine_types',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            },

            // Chart 7: Equipment Conditions (Doughnut)
            equipmentConditions: {
                type: 'doughnut',
                endpoint: 'equipment_conditions',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            },

            // Chart 8: Supply Quantities (Bar)
            supplyQuantities: {
                type: 'bar',
                endpoint: 'supply_quantities',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            },

            // Chart 9: Patient Status (Doughnut)
            patientStatus: {
                type: 'doughnut',
                endpoint: 'patient_status',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            },

            // Chart 10: Vital Trends (Multi-axis Line)
            vitalTrends: {
                type: 'line',
                endpoint: 'vital_trends',
                hasFilters: true,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left'
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            },

            // Chart 11: Assessment Types (Radar)
            assessmentTypes: {
                type: 'radar',
                endpoint: 'assessment_types',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    elements: {
                        line: { borderWidth: 3 },
                        point: { borderWidth: 2 }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            ticks: { backdropColor: 'transparent' }
                        }
                    }
                }
            },

            // Chart 12: Nursing Shifts (Polar Area)
            nursingShifts: {
                type: 'polarArea',
                endpoint: 'nursing_shifts',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            },

            // Chart 13: Activity Logs (Bar)
            activityLogs: {
                type: 'bar',
                endpoint: 'activity_logs',
                hasFilters: true,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            },

            // Chart 14: Expiry Alerts (Doughnut with center text)
            expiryAlerts: {
                type: 'doughnut',
                endpoint: 'expiry_alerts',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '60%'
                }
            },

            // Chart 15: Medicine Classification (Horizontal Bar)
            medicineClassification: {
                type: 'bar',
                endpoint: 'medicine_classification',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            },

            // Chart 16: Patient Demographics (Pie)
            patientDemographics: {
                type: 'pie',
                endpoint: 'patient_demographics',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            }
        };
    }

    async loadAllCharts() {
        const chartPromises = Object.keys(this.chartConfigs).map(chartId => 
            this.loadChart(chartId)
        );
        
        try {
            await Promise.all(chartPromises);
            console.log('All charts loaded successfully');
        } catch (error) {
            console.error('Error loading charts:', error);
        }
    }

    async loadChart(chartId) {
        const config = this.chartConfigs[chartId];
        const canvas = document.getElementById(`${chartId}Chart`);
        
        if (!canvas) {
            console.warn(`Canvas element for ${chartId} not found`);
            return;
        }

        this.showLoading(chartId);

        try {
            const period = this.getFilterValue(chartId, 'period') || '30';
            const data = await this.fetchChartData(config.endpoint, { period });
            
            // Destroy existing chart if it exists
            if (this.charts[chartId]) {
                this.charts[chartId].destroy();
            }

            // Create new chart
            const ctx = canvas.getContext('2d');
            this.charts[chartId] = new Chart(ctx, {
                type: config.type,
                data: data,
                options: {
                    ...config.options,
                    plugins: {
                        ...config.options.plugins,
                        tooltip: {
                            ...config.options.plugins?.tooltip,
                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                            titleColor: '#00cc44',
                            bodyColor: '#ffffff',
                            borderColor: '#0f7b0f',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutCubic'
                    }
                }
            });

            this.hideLoading(chartId);
            this.updateChartStats(chartId, data);

        } catch (error) {
            console.error(`Error loading chart ${chartId}:`, error);
            this.showError(chartId, error.message);
        }
    }

    async fetchChartData(endpoint, params = {}) {
        const url = new URL('../../api/dashboard_charts.php', window.location);
        url.searchParams.append('chart', endpoint);
        
        Object.keys(params).forEach(key => {
            url.searchParams.append(key, params[key]);
        });

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.error) {
            throw new Error(data.error);
        }
        
        return data;
    }

    showLoading(chartId) {
        const container = document.querySelector(`#${chartId}Chart`)?.closest('.chart-container');
        if (container) {
            container.classList.add('loading');
            container.innerHTML = `
                <div class="chart-loading">
                    <div class="chart-loading-spinner"></div>
                    <p>Loading chart data...</p>
                </div>
            `;
        }
    }

    hideLoading(chartId) {
        const container = document.querySelector(`#${chartId}Chart`)?.closest('.chart-container');
        if (container) {
            container.classList.remove('loading');
            // Restore canvas if it was removed
            if (!container.querySelector('canvas')) {
                container.innerHTML = `<canvas id="${chartId}Chart"></canvas>`;
            }
        }
    }

    showError(chartId, message) {
        const container = document.querySelector(`#${chartId}Chart`)?.closest('.chart-container');
        if (container) {
            container.innerHTML = `
                <div class="chart-error">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <p>Error loading chart</p>
                    <small>${message}</small>
                </div>
            `;
        }
    }

    updateChartStats(chartId, data) {
        const statsContainer = document.querySelector(`[data-chart="${chartId}"] .chart-stats`);
        if (!statsContainer || !data.datasets || !data.datasets[0]) return;

        const dataset = data.datasets[0];
        const values = dataset.data || [];
        
        if (values.length === 0) return;

        const total = values.reduce((sum, val) => sum + (val || 0), 0);
        const max = Math.max(...values);
        const min = Math.min(...values);
        const avg = total / values.length;

        statsContainer.innerHTML = `
            <div class="chart-stat">
                <div class="chart-stat-value">${total}</div>
                <div class="chart-stat-label">Total</div>
            </div>
            <div class="chart-stat">
                <div class="chart-stat-value">${max}</div>
                <div class="chart-stat-label">Highest</div>
            </div>
            <div class="chart-stat">
                <div class="chart-stat-value">${Math.round(avg)}</div>
                <div class="chart-stat-label">Average</div>
            </div>
        `;
    }

    setupEventListeners() {
        // Filter change handlers
        document.addEventListener('change', async (e) => {
            if (e.target.matches('.chart-filter')) {
                const chartCard = e.target.closest('[data-chart]');
                if (chartCard) {
                    const chartId = chartCard.getAttribute('data-chart');
                    await this.loadChart(chartId);
                }
            }
        });

        // Refresh button handlers
        document.addEventListener('click', async (e) => {
            if (e.target.matches('.chart-refresh-btn') || e.target.closest('.chart-refresh-btn')) {
                const chartCard = e.target.closest('[data-chart]');
                if (chartCard) {
                    const chartId = chartCard.getAttribute('data-chart');
                    await this.loadChart(chartId);
                }
            }
        });

        // Export button handlers
        document.addEventListener('click', (e) => {
            if (e.target.matches('.chart-export-btn') || e.target.closest('.chart-export-btn')) {
                const chartCard = e.target.closest('[data-chart]');
                if (chartCard) {
                    const chartId = chartCard.getAttribute('data-chart');
                    this.exportChart(chartId);
                }
            }
        });

        // Fullscreen handlers
        document.addEventListener('click', (e) => {
            if (e.target.matches('.chart-fullscreen-btn') || e.target.closest('.chart-fullscreen-btn')) {
                const chartCard = e.target.closest('[data-chart]');
                if (chartCard) {
                    this.toggleFullscreen(chartCard);
                }
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'r':
                        e.preventDefault();
                        this.refreshAllCharts();
                        break;
                    case 's':
                        e.preventDefault();
                        this.exportAllCharts();
                        break;
                }
            }
        });
    }

    getFilterValue(chartId, filterType) {
        const chartCard = document.querySelector(`[data-chart="${chartId}"]`);
        const filter = chartCard?.querySelector(`.chart-filter[data-filter="${filterType}"]`);
        return filter?.value;
    }

    async refreshAllCharts() {
        const refreshPromises = Object.keys(this.charts).map(chartId => 
            this.loadChart(chartId)
        );
        
        try {
            await Promise.all(refreshPromises);
            this.showNotification('Charts refreshed successfully', 'success');
        } catch (error) {
            this.showNotification('Error refreshing charts', 'error');
        }
    }

    exportChart(chartId) {
        const chart = this.charts[chartId];
        if (!chart) return;

        const link = document.createElement('a');
        link.download = `${chartId}-chart.png`;
        link.href = chart.toBase64Image();
        link.click();
    }

    exportAllCharts() {
        Object.keys(this.charts).forEach(chartId => {
            setTimeout(() => this.exportChart(chartId), 100 * Object.keys(this.charts).indexOf(chartId));
        });
    }

    toggleFullscreen(chartCard) {
        if (chartCard.classList.contains('fullscreen')) {
            this.exitFullscreen(chartCard);
        } else {
            this.enterFullscreen(chartCard);
        }
    }

    enterFullscreen(chartCard) {
        chartCard.classList.add('fullscreen');
        document.body.style.overflow = 'hidden';
        
        // Resize chart after fullscreen
        const chartId = chartCard.getAttribute('data-chart');
        setTimeout(() => {
            if (this.charts[chartId]) {
                this.charts[chartId].resize();
            }
        }, 300);
    }

    exitFullscreen(chartCard) {
        chartCard.classList.remove('fullscreen');
        document.body.style.overflow = '';
        
        // Resize chart after exit fullscreen
        const chartId = chartCard.getAttribute('data-chart');
        setTimeout(() => {
            if (this.charts[chartId]) {
                this.charts[chartId].resize();
            }
        }, 300);
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `chart-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
        
        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }

    // Utility method to get chart statistics
    getChartStatistics(chartId) {
        const chart = this.charts[chartId];
        if (!chart) return null;

        const data = chart.data.datasets[0].data;
        const total = data.reduce((sum, val) => sum + val, 0);
        const max = Math.max(...data);
        const min = Math.min(...data);
        const avg = total / data.length;

        return { total, max, min, avg };
    }

    // Method to update chart data without full reload
    updateChartData(chartId, newData) {
        const chart = this.charts[chartId];
        if (!chart) return;

        chart.data = newData;
        chart.update('active');
    }

    // Method to add chart animations
    addChartAnimation(chartId, animationType = 'bounce') {
        const chart = this.charts[chartId];
        if (!chart) return;

        chart.options.animation = {
            ...chart.options.animation,
            duration: 1500,
            easing: animationType
        };
        chart.update();
    }

    // Cleanup method
    destroy() {
        Object.keys(this.charts).forEach(chartId => {
            if (this.charts[chartId]) {
                this.charts[chartId].destroy();
            }
        });
        this.charts = {};
    }
}

// Initialize charts when DOM is ready
let dashboardCharts;

document.addEventListener('DOMContentLoaded', () => {
    dashboardCharts = new DashboardCharts();
});

// Export for global access
window.DashboardCharts = DashboardCharts;