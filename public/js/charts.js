window.addEventListener('DOMContentLoaded', () => {
    // Procurement Distribution Chart
const procurementCtx = document.getElementById('procurementChart').getContext('2d');
const procurementChart = new Chart(procurementCtx, {
    type: 'bar',
    data: {
        labels: ['ILCDB', 'DTC', 'SPARK', 'PROJECT CLICK'], // Labels for the projects
        datasets: [{
            label: 'Procurement Distribution',
            data: [0, 0, 0, 0], // Initial empty data
            backgroundColor: 'rgba(0, 123, 255, 0.5)', // Blue background color
            borderColor: 'rgba(0, 123, 255, 1)', // Blue border color
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true, // Ensure the y-axis starts from 0
                ticks: {
                    stepSize: 1,      // Set the interval to 1
                    min: 0,           // Set the minimum value of the y-axis to 0
                    max: 10           // Set the maximum value of the y-axis to 10 (adjust as necessary)
                }
            }
        }
    }
});

// Function to fetch and update procurement data
async function updateProcurementChart() {
    try {
        const response = await fetch(`/api/procurement-distribution`);
        const result = await response.json();

        if (result.success) {
            const counts = result.data;

            // Update the chart with fetched data
            procurementChart.data.datasets[0].data = [
                counts['ILCDB'] || 0,
                counts['DTC'] || 0,
                counts['SPARK'] || 0,
                counts['PROJECT CLICK'] || 0
            ];
            procurementChart.update();
        } else {
            console.error('Failed to fetch procurement data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching procurement data:', error);
    }
}

// Call the function to load the procurement distribution data when the page loads
updateProcurementChart();


    // Category Distribution Chart with Filter
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: ['SVP', 'Honoraria', 'Daily Travel Expense'],
        datasets: [{
            label: 'Category Distribution',
            data: [0, 0, 0], // Initial empty data
            backgroundColor: 'rgba(40, 167, 69, 0.5)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,   // Ensure the y-axis starts from 0
                ticks: {
                    stepSize: 1,      // Set the interval to 1 (instead of default decimals)
                    min: 0,           // Set the minimum value of the y-axis to 0
                }
            }
        }
    }
});

// Function to fetch and update category data
async function updateCategoryChart(project) {
    try {
        const response = await fetch(`/api/procurement-category-count?project=${project}`);
        const result = await response.json();

        if (result.success) {
            const counts = result.data;

            // Update the chart with fetched data
            categoryChart.data.datasets[0].data = [
                counts.svp || 0,
                counts.honoraria || 0,
                counts.dte || 0
            ];
            categoryChart.update();
        } else {
            console.error('Failed to fetch category data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching category data:', error);
    }
}

// Event listener for filter change
document.getElementById('categoryFilter').addEventListener('change', function () {
    const selectedProject = this.value;

    // Fetch and update the chart based on the selected project
    if (selectedProject === 'all') {
        categoryChart.data.datasets[0].data = [0, 0, 0]; // Reset data for "all"
        categoryChart.update();
    } else {
        updateCategoryChart(selectedProject);
    }
});

// Default: Show 'ILCDB' data when the page loads
updateCategoryChart('ILCDB');

// Quarter Expenditure Distribution Chart
const quarterCtx = document.getElementById('quarterChart').getContext('2d');
const quarterChart = new Chart(quarterCtx, {
    type: 'bar',
    data: {
        labels: ['First Quarter', 'Second Quarter', 'Third Quarter', 'Fourth Quarter'],
        datasets: [{
            label: 'Expenditure by Quarter',
            data: [0, 0, 0, 0], // Initial data (empty)
            backgroundColor: 'rgba(255, 193, 7, 0.5)', // Yellow background color
            borderColor: 'rgba(255, 193, 7, 1)', // Yellow border color
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,  // Set the interval to 1
                    min: 0,       // Set the minimum value to 0
                    max: 10       // Adjust the max value accordingly, or remove it to auto-scale
                }
            }
        }
    }
});

// Function to fetch and update expenditure data
async function updateExpenditureChart(project) {
    console.log(`Fetching data for project: ${project}`); // Debugging line
    try {
        const response = await fetch(`/api/expenditure-by-quarter?project=${project}`);
        const result = await response.json();

        if (result.success) {
            const counts = result.data;

            // Update the chart with fetched data
            quarterChart.data.datasets[0].data = [
                counts['First Quarter'] || 0,
                counts['Second Quarter'] || 0,
                counts['Third Quarter'] || 0,
                counts['Fourth Quarter'] || 0
            ];
            quarterChart.update();
        } else {
            console.error('Failed to fetch expenditure data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching expenditure data:', error);
    }
}

// Call the function to load the expenditure data for 'all' projects when the page loads
document.addEventListener('DOMContentLoaded', function () {
    // Set "all" as the default value for the project filter
    const filterElement = document.getElementById('quarterFilter');
    if (filterElement) {
        filterElement.value = 'all'; // Set the default filter to 'all'
    }

    // Load the data for all projects
    updateExpenditureChart('all'); // Trigger the data load for 'all' projects
});

// Event listener for filter change
document.getElementById('quarterFilter').addEventListener('change', function () {
    const selectedProject = this.value;

    // Fetch and update the chart based on the selected project
    updateExpenditureChart(selectedProject);
});

// Cost Savings Chart
const costSavingsCtx = document.getElementById('costSavingsChart').getContext('2d');
const costSavingsChart = new Chart(costSavingsCtx, {
    type: 'bar',
    data: {
        labels: [], // Empty initially, will be populated with saro_no
        datasets: [
            {
                label: 'Purchase Request',
                data: [], // Empty initially, will be populated with purchase request values
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // Blue color for Purchase Request
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Budget Spent',
                data: [], // Empty initially, will be populated with budget spent values
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
async function updateCostSavingsChart() {
    const selectedProject = document.getElementById('projectFilter').value;

    try {
        const response = await fetch(`/api/cost-savings?project=${selectedProject}`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            // Prepare labels (saro_no) and data for the chart
            const labels = data.map(item => item.saro_no); // Extract saro_no
            const purchaseRequestData = data.map(item => item.pr_amount); // Extract purchase request amounts
            const budgetSpentData = data.map(item => item.budget_spent); // Extract budget spent amounts

            // Update the chart with the new data for the selected project
            costSavingsChart.data.labels = labels;
            costSavingsChart.data.datasets[0].data = purchaseRequestData;
            costSavingsChart.data.datasets[1].data = budgetSpentData;

            // Re-render the chart to reflect changes
            costSavingsChart.update();
        } else {
            console.error('Failed to fetch cost savings data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching cost savings data:', error);
    }
}

// Call the function to load the cost savings data for ILCDB when the page loads
document.addEventListener('DOMContentLoaded', function () {
    // Set ILCDB as the default value for the project filter
    document.getElementById('projectFilter').value = 'ILCDB';

    // Load the data for ILCDB
    updateCostSavingsChart();
});

// Event listener for filter change
document.getElementById('projectFilter').addEventListener('change', function () {
    updateCostSavingsChart();
});

// Event listener for filter change
document.getElementById('projectFilter').addEventListener('change', function () {
    const selectedProject = this.value; // Get the selected project from filter
    selectProject(selectedProject); // Update project data based on filter
});

async function selectProject(project) {
    console.log(`Selected project: ${project}`);  // Check the project being selected
    document.getElementById('projectDropdown').textContent = project;
    document.getElementById('projectName').textContent = project;
    const content = document.getElementById('reportData');
    
    // Clear previous content
    content.innerHTML = '';

    try {
        // Fetch the project-specific data from the backend
        const response = await fetch(`/api/project-report?project=${project}`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            // Insert project-specific data dynamically
            content.innerHTML = `
                <!-- Average Budget Spend (with filter) -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-dark">Average Budget Spend</h6>
                                <p class="card-text fs-5 text-navy">₱${data.avgBudgetSpent.toLocaleString()}</p> <!-- Change color to navy -->
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
                            <p class="card-text fs-5 text-navy">₱${data.avgAllocatedBudget.toLocaleString()}</p> <!-- Change color to navy -->
                        </div>
                    </div>
                </div>
                <!-- Average Approved Budget (NTCA) -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-dark">Average Approved Budget (NTCA)</h6>
                            <p class="card-text fs-5 text-navy">₱${data.avgApprovedBudget.toLocaleString()}</p> <!-- Change color to navy -->
                        </div>
                    </div>
                </div>
                <!-- Processing Rate (Per Unit) -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-dark">Processing Rate (Per Unit)</h6>
                            <p class="card-text fs-5 text-success">${data.processingRate}%</p>
                        </div>
                    </div>
                </div>
                <!-- Overdue Counter (with filter) -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-dark">Overdue Counter</h6>
                                <p class="card-text fs-5 text-danger">${data.overdueCount}</p>
                            </div>
                            <img src="assets/filter.png" alt="Filter" width="20">
                        </div>
                    </div>
                </div>
            `;
        } else {
            console.error("Failed to fetch project data:", result.message);
        }
    } catch (error) {
        console.error('Error fetching project data:', error);
    }
}

// Default project on page load
window.addEventListener('DOMContentLoaded', () => {
    selectProject('ILCDB');  // Default project on page load
});

});