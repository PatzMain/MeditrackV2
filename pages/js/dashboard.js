// Dashboard Chart Initialization

document.addEventListener('DOMContentLoaded', function() {
    // Chart.js default configuration
    Chart.defaults.font.family = 'Inter, system-ui, -apple-system, sans-serif';
    Chart.defaults.color = '#666';
    
    // Color palette
    const colors = {
        primary: '#0f7b0f',
        secondary: '#00cc44',
        accent: '#4CAF50',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#2196F3',
        success: '#28a745'
    };

    // Inventory Overview Chart
    const inventoryCtx = document.getElementById('inventoryChart');
    if (inventoryCtx) {
        new Chart(inventoryCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Medicines',
                    data: [65, 59, 80, 81, 76, 85, 90],
                    borderColor: colors.primary,
                    backgroundColor: `${colors.primary}20`,
                    tension: 0.4
                }, {
                    label: 'Supplies',
                    data: [45, 49, 60, 70, 66, 75, 80],
                    borderColor: colors.info,
                    backgroundColor: `${colors.info}20`,
                    tension: 0.4
                }, {
                    label: 'Equipment',
                    data: [28, 30, 32, 35, 33, 38, 40],
                    borderColor: colors.warning,
                    backgroundColor: `${colors.warning}20`,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Stock Level Distribution Chart
    const stockCtx = document.getElementById('stockChart');
    if (stockCtx) {
        new Chart(stockCtx, {
            type: 'doughnut',
            data: {
                labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Expired'],
                datasets: [{
                    data: [65, 20, 10, 5],
                    backgroundColor: [
                        colors.success,
                        colors.warning,
                        colors.danger,
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Expiry Timeline Chart
    const expiryCtx = document.getElementById('expiryChart');
    if (expiryCtx) {
        new Chart(expiryCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Items Expiring',
                    data: [12, 19, 3, 5, 2, 3, 8, 15, 22, 18, 24, 30],
                    backgroundColor: function(context) {
                        const value = context.parsed.y;
                        if (value > 20) return colors.danger;
                        if (value > 10) return colors.warning;
                        return colors.success;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: ['Antibiotics', 'Analgesics', 'Vitamins', 'First Aid', 'Others'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
                    backgroundColor: [
                        colors.primary,
                        colors.secondary,
                        colors.accent,
                        colors.info,
                        colors.warning
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Monthly Usage Trend Chart
    const usageCtx = document.getElementById('usageChart');
    if (usageCtx) {
        new Chart(usageCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Medical',
                    data: [120, 150, 180, 140],
                    backgroundColor: colors.primary
                }, {
                    label: 'Dental',
                    data: [80, 90, 100, 85],
                    backgroundColor: colors.secondary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Handle chart filter changes
    const chartFilters = document.querySelectorAll('.chart-filter');
    chartFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            // Placeholder for filter functionality
            console.log('Filter changed to:', this.value);
            // In production, this would fetch new data and update charts
        });
    });
});