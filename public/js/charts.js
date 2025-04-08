window.addEventListener('DOMContentLoaded', () => {
    // Procurement Distribution Chart
    const procurementCtx = document.getElementById('procurementChart').getContext('2d');
    const procurementChart = new Chart(procurementCtx, {
        type: 'bar',
        data: {
            labels: ['ILCDB', 'DTC', 'SPARK', 'PROJECT CLICK'], 
            datasets: [{
                label: 'Procurement Distribution',
                data: [120, 150, 180, 130], // Example data
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Distribution Chart with Filter
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: ['SVP', 'Honoraria', 'Daily Travel Expense'], 
            datasets: [{
                label: 'Category Distribution',
                data: [50, 70, 90], // Example data
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Event listener for filter change
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const filterValue = this.value;

        // Update the chart based on filter value
        if (filterValue === 'all') {
            categoryChart.data.datasets[0].data = [50, 70, 90]; // Data for all categories
        } else if (filterValue === 'category1') {
            categoryChart.data.datasets[0].data = [100, 0, 0]; // Data for Category 1 only
        } else if (filterValue === 'category2') {
            categoryChart.data.datasets[0].data = [0, 100, 0]; // Data for Category 2 only
        } else if (filterValue === 'category3') {
            categoryChart.data.datasets[0].data = [0, 0, 100]; // Data for Category 3 only
        }

        // Re-render the chart with updated data
        categoryChart.update();
    });

    // Highest Expenditure by Quarter
    const quarterCtx = document.getElementById('quarterChart').getContext('2d');
    const quarterChart = new Chart(quarterCtx, {
        type: 'bar',
        data: {
            labels: ['First Quarter', 'Second Quarter', 'Third Quarter', 'Fourth Quarter'], 
            datasets: [{
                label: 'Expenditure by Quarter',
                data: [200, 300, 400, 500], // Example data
                backgroundColor: 'rgba(255, 193, 7, 0.5)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

// Cost Savings Chart (Updated to show Purchase Request and Budget Spent)
const costSavingsCtx = document.getElementById('costSavingsChart').getContext('2d');
const costSavingsChart = new Chart(costSavingsCtx, {
    type: 'bar',
    data: {
        labels: ['SARO 1', 'SARO 2', 'SARO 3', 'SARO 4'], // Initial labels for projects
        datasets: [
            {
                label: 'Purchase Request',
                data: [500000, 400000, 350000, 450000], // Example data for Purchase Request
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // Blue color for Purchase Request
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Budget Spent',
                data: [450000, 380000, 330000, 400000], // Example data for Budget Spent
                backgroundColor: 'rgba(255, 99, 132, 0.5)', // Red color for Budget Spent
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.dataset.label + ': ₱' + tooltipItem.raw.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Function to update the chart based on selected project
function updateCostSavingsChart() {
    const selectedProject = document.getElementById('projectFilter').value;

    let purchaseRequestData;
    let budgetSpentData;

    // Set data based on selected project
    switch (selectedProject) {
        case 'ILCDB':
            purchaseRequestData = 500000;
            budgetSpentData = 450000;
            break;
        case 'DTC':
            purchaseRequestData = 400000;
            budgetSpentData = 380000;
            break;
        case 'SPARK':
            purchaseRequestData = 350000;
            budgetSpentData = 330000;
            break;
        case 'PROJECT CLICK':
            purchaseRequestData = 450000;
            budgetSpentData = 400000;
            break;
        default:
            purchaseRequestData = 0;
            budgetSpentData = 0;
            break;
    }

    // Update the chart with the new data for the selected project
    costSavingsChart.data.datasets[0].data = [purchaseRequestData];
    costSavingsChart.data.datasets[1].data = [budgetSpentData];

    // Update the chart labels (if needed)
    costSavingsChart.data.labels = [selectedProject];

    // Re-render the chart to reflect changes
    costSavingsChart.update();
}

});
