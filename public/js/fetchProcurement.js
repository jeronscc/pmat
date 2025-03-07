// Consistent status class function
function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'badge bg-secondary text-white';  // Gray for pending
        case 'ongoing':
            return 'badge bg-warning text-dark';  // Orangeish yellow for ongoing
        case 'done':
            return 'badge bg-success text-white';  // Green for done
        default:
            return 'badge bg-light text-dark';  // Default for no status or unknown status
    }
}

// FETCH PROCUREMENT DATA BY SARO FILTER
function fetchProcurementForSaro(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear existing rows

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS & UNIT cell
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');

                    let statusMessage = item.status || ''; 
                    let unitMessage = item.unit ? ` at ${item.unit}` : ''; 

                    // Check if honoraria form status is available and use it if it's not 'No Status'
                    if (item.honoraria_status && item.honoraria_status.toLowerCase() !== 'no status') {
                        statusMessage = item.honoraria_status;  // Update status to honoraria form status if available
                    }

                    // If status is "done", don't append the unit
                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = ''; // Don't append the unit when status is "done"
                    }

                    badge.className = getStatusClass(statusMessage || ''); // Apply appropriate badge class
                    badge.textContent = statusMessage + unitMessage; // Combine status and unit for display

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to table
                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found for the selected SARO.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement requirements:', error));
}

// Fetch procurement data by year filter
function fetchProcurementForYear(year) {
    const url = year === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear existing rows

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS & UNIT cell
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');

                    let statusMessage = item.status || ''; // Default to empty if no status
                    let unitMessage = item.unit ? ` at ${item.unit}` : ''; // Default to empty if no unit

                    // Check if honoraria form status is available
                    if (item.honoraria_status && item.honoraria_status.toLowerCase() !== 'no status') {
                        statusMessage = item.honoraria_status; // Use honoraria form status if available
                    }

                    // If status is "done", don't append the unit
                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = ''; // Don't append the unit when status is "done"
                    }

                    badge.className = getStatusClass(statusMessage || ''); // Apply appropriate badge class
                    badge.textContent = statusMessage + unitMessage; // Combine status and unit for display

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found for the selected year.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}

// FETCH PROCUREMENT DATA FOR MODAL
function fetchProcurementRequirements(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear existing table rows

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    prNumberCell.style.cursor = 'pointer'; // Add cursor pointer for visual feedback
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS & UNIT cell (dynamically set from the API response)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');

                    let statusMessage = item.status || ''; // Default to empty if no status
                    let unitMessage = item.unit ? ` at ${item.unit}` : ''; // Default to empty if no unit

                    // Check if honoraria status is available
                    if (item.honoraria_status && item.honoraria_status.toLowerCase() !== 'no status') {
                        statusMessage = item.honoraria_status; // Use honoraria status if it's not 'no status'
                    }

                    // If status is "done", remove the unit part
                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = ''; // Don't append the unit when status is "done"
                    }

                    // Combine status and unit for display
                    badge.className = getStatusClass(statusMessage || ''); // Apply appropriate badge class
                    badge.textContent = statusMessage + unitMessage; // Combine status and unit for display

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to table
                    tableBody.appendChild(row);
                });

                // Add event listener to table body for delegation
                tableBody.addEventListener('click', function(event) {
                    const prNumberCell = event.target.closest('td');
                    if (prNumberCell && prNumberCell.parentElement) {
                        const procurementId = prNumberCell.textContent;
                        const row = prNumberCell.parentElement;
                        openProcurementModal({ procurement_id: procurementId });
                    }
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement requirements:', error));
}

// Function to open modal and display procurement details
function openProcurementModal(item) {
    const procurementId = item.procurement_id; // Get procurement ID from clicked item
    const modal = document.getElementById('procurementDetailsModal');

    // Fetch detailed data from the API using the procurement_id
    const url = `/api/fetch-procurement-details?procurement_id=${procurementId}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                // If the response contains a message, that means procurement wasn't found
                alert(data.message);
            } else {
                // Populate modal fields with the fetched procurement data
                document.getElementById('modalProcurementCategory').textContent = data.procurement_category || 'N/A';
                document.getElementById('modalProcurementNo').textContent = data.procurement_id || 'N/A';
                document.getElementById('modalSaroNo').textContent = data.saro_no || 'N/A';
                document.getElementById('modalYear').textContent = data.year || 'N/A';
                document.getElementById('modalDescription').textContent = data.description || 'N/A';
                document.getElementById('modalActivity').textContent = data.activity || 'N/A';

                // Show the modal using Bootstrap Modal API
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            }
        })
        .catch(error => {
            console.error('Error fetching procurement details:', error);
            alert('Failed to load procurement details.');
        });
}

// Event listener for table row click
const tableBody = document.getElementById('procurementTable');
tableBody.addEventListener('click', function(event) {
    const prNumberCell = event.target.closest('td');
    if (prNumberCell && prNumberCell.parentElement) {
        const procurementId = prNumberCell.textContent;
        const row = prNumberCell.parentElement;
        openProcurementModal({ procurement_id: procurementId });
    }
});

// Function to fetch combined procurement data
function fetchProcurementData(year) {
    const url = year === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear any existing rows in the table

            if (data.length > 0) {
                // Loop through the fetched data and create table rows
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS & UNIT cell (dynamically set from the API response)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');

                    let statusMessage = item.status || ''; // Default to empty if no status
                    let unitMessage = item.unit ? ` at ${item.unit}` : ''; // Default to empty if no unit

                    // If status is "done", remove the unit part
                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = ''; // Don't append the unit when status is "done"
                    }

                    // Combine status and unit for display
                    badge.className = getStatusClass(item.status || ''); // Apply appropriate badge class
                    badge.textContent = statusMessage + unitMessage; // Combine status and unit for display

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append the row to the table body
                    tableBody.appendChild(row);
                });
            } else {
                // Show message if no procurement data is available
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}

// Event listener for the year filter
document.getElementById('year')?.addEventListener('change', function() {
    // When the year changes, fetch the procurement data based on selected year
    fetchProcurementData(this.value);
});

// Fetch procurement data when the page loads
document.addEventListener('DOMContentLoaded', () => {
    // Check if procurementTable exists before trying to populate it
    if (document.getElementById('procurementTable')) {
        fetchProcurementData('');
    }
});

// Utility functions
function highlightSelectedItem(selectedItem) {
    const items = document.querySelectorAll('.saro-list .list-group-item');
    items.forEach(item => item.classList.remove('active'));
    selectedItem.classList.add('active');
}

function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function filterProcurementByYear(year) {
    fetchProcurementData(year);
}
