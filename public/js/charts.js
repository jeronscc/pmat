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

async function updateCharts() {
    try {
        const response = await fetch('/api/average-budget-spent');
        const result = await response.json();

        if (result.success) {
            const averages = result.data;

            // Update the charts with the fetched data
            costSavingsChart.data.datasets[0].data = [
                averages.ilcdb,
                averages.dtc,
                averages.spark,
                averages.click,
            ];
            costSavingsChart.update();
        }
    } catch (error) {
        console.error('Error updating charts:', error);
    }
}

window.addEventListener('DOMContentLoaded', () => {
    updateCharts(); // Update charts on page load
    setInterval(updateCharts, 60000); // Refresh every 60 seconds
});

// Function to fetch and update the category chart based on the selected project
async function updateCategoryChart(project) {
    try {
        const response = await fetch(`/api/procurement-category-count?project=${project}`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            // Update the chart data
            categoryChart.data.datasets[0].data = [data.svp, data.honoraria, data.dte];
            categoryChart.update();
        } else {
            console.error('Failed to fetch category data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching category data:', error);
    }
}

// Event listener for category filter dropdown
document.getElementById('categoryFilter').addEventListener('change', function () {
    const selectedProject = this.value;
    updateCategoryChart(selectedProject); // Update the chart based on the selected project
});

// Initialize the chart with the default project (ILCDB)
window.addEventListener('DOMContentLoaded', () => {
    updateCategoryChart('ILCDB'); // Default project
});

function selectProject(project) {
    console.log(`Selected project: ${project}`);  // Check the project being selected

    document.getElementById('projectDropdown').textContent = project;
    document.getElementById('projectName').textContent = project;

    const content = document.getElementById('reportData');
    
    // Clear previous content
    content.innerHTML = '';

    // Insert project-specific data dynamically
    content.innerHTML = `
        <!-- Average Budget Spend (with filter) -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-dark">Average Budget Spend</h6>
                        <p class="card-text fs-5 text-navy">₱0.00</p> <!-- Change color to navy -->
                    </div>
                    <img src="assets/filter.png" alt="Filter" width="20">
                </div>
            </div>
        </div>

        <!-- Average Allocated Budget (SARO) -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-dark">Average Allocated Budget (SARO)</h6>
                    <p class="card-text fs-5 text-navy">₱0.00</p> <!-- Change color to navy -->
                </div>
            </div>
        </div>

        <!-- Average Approved Budget (NTCA) -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-dark">Average Approved Budget (NTCA)</h6>
                    <p class="card-text fs-5 text-navy">₱0.00</p> <!-- Change color to navy -->
                </div>
            </div>
        </div>

        <!-- Processing Rate (Per Unit) -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-dark">Processing Rate (Per Unit)</h6>
                    <p class="card-text fs-5 text-success">0%</p>
                </div>
            </div>
        </div>

        <!-- Overdue Counter (with filter) -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-dark">Overdue Counter</h6>
                        <p class="card-text fs-5 text-danger">0</p>
                    </div>
                    <img src="assets/filter.png" alt="Filter" width="20">
                </div>
            </div>
        </div>
    `;
}

async function fetchAverageBudgetSpent() {
    try {
        const response = await fetch('/api/average-budget-spent');
        const result = await response.json();

        if (result.success) {
            const averages = result.data;

            // Update the average budget spent in the UI
            document.querySelector('#averageBudgetSpentILCDB').textContent = `₱${averages.ilcdb.toLocaleString()}`;
            document.querySelector('#averageBudgetSpentDTC').textContent = `₱${averages.dtc.toLocaleString()}`;
            document.querySelector('#averageBudgetSpentSPARK').textContent = `₱${averages.spark.toLocaleString()}`;
            document.querySelector('#averageBudgetSpentCLICK').textContent = `₱${averages.click.toLocaleString()}`;
        }
    } catch (error) {
        console.error('Error fetching average budget spent:', error);
    }
}

window.addEventListener('DOMContentLoaded', () => {
    fetchAverageBudgetSpent(); // Fetch data on page load
    setInterval(fetchAverageBudgetSpent, 60000); // Refresh every 60 seconds
    selectProject('ILCDB');  // Default project on page load
});

});