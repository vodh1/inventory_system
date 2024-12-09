document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log data received from PHP
    console.log('Borrowing Trends Data:', borrowingTrendsData);
    console.log('Equipment Categories Data:', equipmentCategoriesData);

    // Format dates and counts for the chart
    const dates = borrowingTrendsData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const counts = borrowingTrendsData.map(item => parseInt(item.count));

    // Debug: Log formatted data
    console.log('Formatted Dates:', dates);
    console.log('Formatted Counts:', counts);

    // Borrowing Trends Chart
    console.log('Initializing borrowingTrendsChart');
    const borrowingTrendsChart = new Chart(document.getElementById('borrowingTrendsChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Daily Borrowings',
                data: counts,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        drawBorder: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Equipment Categories Chart
    console.log('Initializing equipmentCategoriesChart');
    const equipmentCategoriesChart = new Chart(document.getElementById('equipmentCategoriesChart'), {
        type: 'doughnut',
        data: {
            labels: equipmentCategoriesData.map(item => item.name),
            datasets: [{
                data: equipmentCategoriesData.map(item => item.count),
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#ec4899'
                ]
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

    // Menu Toggle Functionality
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('mainContent').classList.toggle('ml-0');
        document.getElementById('mainContent').classList.toggle('ml-64');
    });
});