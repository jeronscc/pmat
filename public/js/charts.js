window.addEventListener('DOMContentLoaded', async () => {
    try {
        // Set the refresh date
        const reportDateElement = document.getElementById('reportDate');
            if (reportDateElement) {
                const currentDate = new Date();
                
                // Format the date and time
                const formattedDate = currentDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                const formattedTime = currentDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });

                // Set the content with italic style
                reportDateElement.innerHTML = `Report as of <i>${formattedDate} ${formattedTime}</i>`;
            }

        // Set default filter values
        const projectDropdown = document.getElementById('projectDropdown');
        if (projectDropdown) {
            projectDropdown.textContent = 'ILCDB'; // Default dropdown text
        }

        const projectFilter = document.getElementById('projectFilter');
        if (projectFilter) {
            projectFilter.value = 'ILCDB'; // Default project
        }

        const quarterFilter = document.getElementById('quarterFilter');
        if (quarterFilter) {
            quarterFilter.value = 'ALL'; // Default quarter
        }

        // Load all data synchronously
        await Promise.all([
            selectProject('ILCDB'),
            updateProcurementChart(),
            updateCategoryChart('ILCDB'),
            updateExpenditureChart('ALL'),
            updateCostSavingsChart()
        ]);

        console.log('All default data loaded successfully.');
    } catch (error) {
        console.error('Error loading default data:', error);
    }
});

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

// Event listener for filter change
document.getElementById('quarterFilter').addEventListener('change', function () {
    const selectedQuarter = this.value; // Get the selected quarter from the filter
    updateExpenditureChart(selectedQuarter);
});

// Cost Savings Chart
const costSavingsCtx = document.getElementById('costSavingsChart').getContext('2d');
const costSavingsChart = new Chart(costSavingsCtx, {
    type: 'bar',
    data: {
        labels: [], // Labels for each saro_no
        datasets: [
            {
                label: 'Purchase Request',
                data: [], // Total PR amount for each saro_no
                backgroundColor: 'rgba(54, 162, 235, 0.5)', // Blue color for Purchase Request
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Budget Spent',
                data: [], // Total Budget Spent for each saro_no
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
            const purchaseRequestData = data.map(item => item.total_pr_amount); // Extract total PR amounts per saro_no
            const budgetSpentData = data.map(item => item.total_budget_spent); // Extract total budget spent per saro_no

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

// Report Data
document.addEventListener('DOMContentLoaded', () => {
    // Set up event listeners for each dropdown item
    const projectDropdown = document.getElementById('projectDropdown');
    const dropdownItems = document.querySelectorAll('.dropdown-item');

    // Initialize with the default project name in the button
    projectDropdown.textContent = 'ILCDB'; // Default text

    // Set up event listeners for each dropdown item
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(event) {
            // Prevent default anchor behavior
            event.preventDefault();
            
            const selectedProject = this.textContent; // Get the project name from the clicked item
            
            // Call the selectProject function to load data for the selected project
            selectProject(selectedProject);
        });
    });

});

// The selectProject function to fetch project data and update the UI
async function selectProject(project) {
    console.log(`Selected project: ${project}`);  // Log the selected project for debugging
    document.getElementById('projectDropdown').textContent = project;  // Update dropdown button text
    document.getElementById('projectName').textContent = project;  // Update project name display
    const content = document.getElementById('reportData');
    
    // Clear previous content
    content.innerHTML = '';

    try {
        // Fetch the project-specific data from the backend
        const response = await fetch(`/api/project-report?project=${project}`);
        
        // Check for successful response
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();

        // Log the API response for debugging
        console.log(result);

        if (result.success && result.data) {
            const data = result.data;

            // Insert project-specific data dynamically
            content.innerHTML = `
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-dark">Total Budget Spent</h6>
                                <p class="card-text fs-5 text-navy">₱${data.totalBudgetSpent ? Number(data.totalBudgetSpent).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-dark">Average Budget Spent</h6>
                                <p class="card-text fs-5 text-navy">₱${data.avgBudgetSpent ? Number(data.avgBudgetSpent).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-dark">Average Allocated Budget (SARO)</h6>
                            <p class="card-text fs-5 text-navy">₱${data.avgAllocatedBudget ? Number(data.avgAllocatedBudget).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-dark">Average Approved Budget (NTCA)</h6>
                            <p class="card-text fs-5 text-navy">₱${data.avgApprovedBudget ? Number(data.avgApprovedBudget).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-dark">Processing Rate (Per Unit)</h6>
                            <p class="card-text fs-5 text-success">${data.processingRate ? `${Math.round(data.processingRate)} hrs` : 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-dark">Overdue Counter</h6>
                                <p class="card-text fs-5 text-danger">${data.overdueCount !== undefined ? data.overdueCount : 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            console.error("Failed to fetch project data:", result.message);
            alert(result.message || "Failed to fetch project data.");
        }
    } catch (error) {
        console.error('Error fetching project data:', error);
        alert("Error fetching project data. Please try again later.");
    }
}

// Add event listeners for all dropdown items
document.querySelectorAll('.project-option').forEach(item => {
    item.addEventListener('click', async (e) => {
        e.preventDefault();
        const project = item.getAttribute('data-project');

        // Update project dropdown label
        document.getElementById('projectDropdown').textContent = project;

        // Call project-specific update functions
        await selectProject(project);
        updateCategoryChart(project);
        updateExpenditureChart(project);
        document.getElementById('projectFilter').value = project; // sync other filters
        updateCostSavingsChart();
    });
});