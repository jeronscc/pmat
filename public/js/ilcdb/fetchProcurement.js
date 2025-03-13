// Consistent status class function
function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'badge bg-secondary text-white';  // Gray for pending
        case 'ongoing':
            return 'badge bg-warning text-dark';  // Orangeish yellow for ongoing
        case 'done':
            return 'badge bg-success text-white';  // Green for done
        case'overdue':
            return 'badge bg-danger text-white';
        default:
            return 'badge bg-light text-dark';  // Default for no status or unknown status
    }
}

document.addEventListener('click', function (event) {
    const row = event.target.closest('tr'); // Get the clicked row
    if (row && row.dataset.procurementId) {
        openProcurementModal({ procurement_id: row.dataset.procurementId });
    }
});

function fetchProcurementForSaro(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBodies = {
                all: document.getElementById('procurementTable'),
                pending: document.getElementById('procurementTablePending'),
                ongoing: document.getElementById('procurementTableOngoing'),
                overdue: document.getElementById('procurementTableOverdue'),
                done: document.getElementById('procurementTableDone')
            };

            Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = ''); // ✅ Clear old rows

            if (data.length > 0) {
                // Sort data by status order and maintain original order for FIFO
                const statusOrder = { 'overdue': 1, 'ongoing': 2, 'pending': 3, 'done': 4 };
            
                data.sort((a, b) => {
                    const statusA = (a.status || 'unknown').toLowerCase();
                    const statusB = (b.status || 'unknown').toLowerCase();
            
                    // Sort by status first
                    const statusComparison = (statusOrder[statusA] || 5) - (statusOrder[statusB] || 5);
            
                    // If statuses are the same, maintain original FIFO order
                    return statusComparison !== 0 ? statusComparison : data.indexOf(a) - data.indexOf(b);
                });
            
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-procurement-id', item.procurement_id); // ✅ Trackable row ID

                    // PR NUMBER cell
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

                    // Use honoraria status if available
                    if (item.honoraria_status && item.honoraria_status.toLowerCase() !== 'no status') {
                        statusMessage = item.honoraria_status;
                    }

                    // If status is "done", remove the unit part
                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = '';
                    }

                    badge.className = getStatusClass(statusMessage || '');
                    badge.textContent = statusMessage + unitMessage; 

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    tableBodies.all.appendChild(row); // Append the original row to "All" only

                    if (statusMessage.toLowerCase() === 'done') {
                        tableBodies.done.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'ongoing') {
                        tableBodies.ongoing.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'overdue') {
                        tableBodies.overdue.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'pending') {
                        tableBodies.pending.appendChild(row.cloneNode(true));
                    }


                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found for the selected SARO.';
                emptyMessage.appendChild(emptyCell);
                tableBodies.all.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement requirements:', error));
}

// Fetch procurement data by year filter
function fetchProcurementForYear(year) {
    const url = year === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?year=${year}`;
    
    console.log("Fetching procurement data from:", url); // Debugging log

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log("Fetched procurement data:", data); // Debugging log

            const tableBodies = {
                all: document.getElementById('procurementTable'),
                pending: document.getElementById('procurementTablePending'),
                ongoing: document.getElementById('procurementTableOngoing'),
                overdue: document.getElementById('procurementTableOverdue'),
                done: document.getElementById('procurementTableDone')
            };

            // Clear all table bodies
            Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = '');

            if (data.length > 0) {
                // Sort data by status
                const statusOrder = { 'overdue': 1, 'ongoing': 2, 'pending': 3, 'done': 4 };

                data.sort((a, b) => {
                    const statusA = (a.status || 'unknown').toLowerCase();
                    const statusB = (b.status || 'unknown').toLowerCase();

                    return (statusOrder[statusA] || 5) - (statusOrder[statusB] || 5);
                });

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

                    if (item.honoraria_status && item.honoraria_status.toLowerCase() !== 'no status') {
                        statusMessage = item.honoraria_status;
                    }

                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = ''; 
                    }

                    badge.className = getStatusClass(statusMessage || '');
                    badge.textContent = statusMessage + unitMessage;

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    tableBodies.all.appendChild(row);

                    // Clone row properly
                    if (statusMessage.toLowerCase() === 'done') {
                        tableBodies.done.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'ongoing') {
                        tableBodies.ongoing.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'overdue') {
                        tableBodies.overdue.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'pending') {
                        tableBodies.pending.appendChild(row.cloneNode(true));
                    }
                });

                // Add event listener for row click
                document.querySelectorAll('#procurementTable tr').forEach(row => {
                    row.addEventListener('click', function(event) {
                        const prNumberCell = event.target.closest('td');
                        if (prNumberCell && prNumberCell.parentElement) {
                            const procurementId = prNumberCell.textContent;
                            openProcurementModal({ procurement_id: procurementId });
                        }
                    });
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found for the selected year.';
                emptyMessage.appendChild(emptyCell);
                tableBodies.all.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error("Error fetching procurement data:", error));
}

// FETCH PROCUREMENT DATA FOR MODAL
function fetchProcurementRequirements(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBodies = {
                all: document.getElementById('procurementTable'),
                pending: document.getElementById('procurementTablePending'),
                ongoing: document.getElementById('procurementTableOngoing'),
                overdue: document.getElementById('procurementTableOverdue'),
                done: document.getElementById('procurementTableDone')
            };

            // Clear all table bodies
            Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = '');

            if (data.length > 0) {
                // Sort data by status order and maintain original order for FIFO
                const statusOrder = { 'overdue': 1, 'ongoing': 2, 'pending': 3, 'done': 4 };
            
                data.sort((a, b) => {
                    const statusA = (a.status || 'unknown').toLowerCase();
                    const statusB = (b.status || 'unknown').toLowerCase();
            
                    // Sort by status first
                    const statusComparison = (statusOrder[statusA] || 5) - (statusOrder[statusB] || 5);
            
                    // If statuses are the same, maintain original FIFO order
                    return statusComparison !== 0 ? statusComparison : data.indexOf(a) - data.indexOf(b);
                });
            
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

                    tableBodies.all.appendChild(row); // Append the original row to "All" only

                    if (statusMessage.toLowerCase() === 'done') {
                        tableBodies.done.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'ongoing') {
                        tableBodies.ongoing.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'overdue') {
                        tableBodies.overdue.appendChild(row.cloneNode(true));
                    } else if (statusMessage.toLowerCase() === 'pending') { 
                        tableBodies.pending.appendChild(row.cloneNode(true));
                    }


                });

                // Add event listener to each table body for delegation
                Object.values(tableBodies).forEach(tableBody => {
                    tableBody.addEventListener('click', function(event) {
                        const prNumberCell = event.target.closest('td');
                        if (prNumberCell && prNumberCell.parentElement) {
                            const procurementId = prNumberCell.textContent;
                            const row = prNumberCell.parentElement;
                            openProcurementModal({ procurement_id: procurementId });
                        }
                    });
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBodies.all.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement requirements:', error));
}
// Store Bootstrap modal instance globally
let bootstrapModalInstance = null;

// Function to open modal and display procurement details
function openProcurementModal(item) {
    const procurementId = item.procurement_id; // Get procurement ID from clicked item
    const modal = document.getElementById('procurementDetailsModal');

    if (!modal) {
        console.error("Modal element not found.");
        return;
    }

    // Fetch detailed data from the API using the procurement_id
    const url = `/api/fetch-procurement-details?procurement_id=${procurementId}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message); // Show error if procurement is not found
            } else {
                // Populate modal fields
                document.getElementById('modalProcurementCategory').textContent = data.procurement_category || 'N/A';
                document.getElementById('modalProcurementNo').textContent = data.procurement_id || 'N/A';
                document.getElementById('modalSaroNo').textContent = data.saro_no || 'N/A';
                document.getElementById('modalYear').textContent = data.year || 'N/A';
                document.getElementById('modalDescription').textContent = data.description || 'N/A';
                document.getElementById('modalActivity').textContent = data.activity || 'N/A';

                // Initialize Bootstrap modal only once
                if (!bootstrapModalInstance) {
                    bootstrapModalInstance = new bootstrap.Modal(modal);
                }

                // Show the modal
                bootstrapModalInstance.show();
            }
        })
        .catch(error => {
            console.error('Error fetching procurement details:', error);
            alert('Failed to load procurement details.');
        });
}

// Ensure modal closes when the close button is clicked
document.getElementById('closeModalBtn').addEventListener('click', function () {
    if (bootstrapModalInstance) {
        bootstrapModalInstance.hide();
    }
});

// Event listener for table row click
const tableBodies = {
    all: document.getElementById('procurementTable'),
    pending: document.getElementById('procurementTablePending'),
    ongoing: document.getElementById('procurementTableOngoing'),
    overdue: document.getElementById('procurementTableOverdue'),
    done: document.getElementById('procurementTableDone')
};

document.addEventListener('click', function (event) {
    const row = event.target.closest('tr'); // Find closest row
    if (row && row.dataset.procurementId) {
        openProcurementModal({ procurement_id: row.dataset.procurementId });
    }
});

function fetchProcurementData(year = '', status = 'all') {
    let url = '/api/fetch-combined-procurement-data';

    if (year !== '') url += `?year=${year}`;
    if (status !== 'all') url += year !== '' ? `&status=${status}` : `?status=${status}`;
    if (currentSaroNo !== '') url += (year !== '' || status !== 'all') ? `&saro_no=${currentSaroNo}` : `?saro_no=${currentSaroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBodies = {
                all: document.getElementById('procurementTable'),
                pending: document.getElementById('procurementTablePending'),
                ongoing: document.getElementById('procurementTableOngoing'),
                overdue: document.getElementById('procurementTableOverdue'),
                done: document.getElementById('procurementTableDone')
            };

            Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = ''); // ✅ Clear old rows

            data.forEach(item => {
                const row = document.createElement('tr');
                row.setAttribute('data-procurement-id', item.procurement_id); // ✅ Ensure each row has a unique identifier
            
                const prNumberCell = document.createElement('td');
                prNumberCell.textContent = item.procurement_id;
                row.appendChild(prNumberCell);
            
                const activityCell = document.createElement('td');
                activityCell.textContent = item.activity;
                row.appendChild(activityCell);
            
                const statusCell = document.createElement('td');
                const badge = document.createElement('span');
            
                let statusMessage = item.status || '';
                let unitMessage = item.unit ? ` at ${item.unit}` : '';
                if (statusMessage.toLowerCase() === 'done') unitMessage = '';

                badge.className = getStatusClass(item.status || '');
                badge.textContent = statusMessage + unitMessage;

                statusCell.appendChild(badge);
                row.appendChild(statusCell);
            
                tableBodies.all.appendChild(row);
                if (statusMessage.toLowerCase() === 'done') tableBodies.done.appendChild(row);
                else if (statusMessage.toLowerCase() === 'pending') tableBodies.pending.appendChild(row);
                else if (statusMessage.toLowerCase() === 'ongoing') tableBodies.ongoing.appendChild(row);
                else if (statusMessage.toLowerCase() === 'overdue') tableBodies.overdue.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}
// Event listener for the year filter
document.getElementById('year')?.addEventListener('change', function () {
    const yearFilter = this.value;
    const activeTab = document.querySelector('.nav-link.active');
    const status = activeTab ? activeTab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase() : 'all';

    fetchProcurementData(yearFilter, status);
});

// Fetch procurement data when the page loads
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('procurementTable')) {
        fetchProcurementData('');
    }
});

// Event listener for tab clicks to switch between tabs
document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default Bootstrap behavior
        const filter = tab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase();

        // Remove active class from all tabs and add to clicked tab
        document.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        // Fetch and render table with new filter
        const yearFilter = document.getElementById('year')?.value || '';
        fetchProcurementData(yearFilter, filter);
    });
});

function checkOverdue() {
    fetch('/check-overdue')
        .then(response => response.json())
        .then(data => {
            if (data.updated) {
                console.log('Overdue status updated.');
                updateOverdueUI(data.overdue_items); // Update table without refreshing
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateOverdueUI(overdueItems) {
    overdueItems.forEach(item => {
        const row = document.querySelector(`[data-procurement-id="${item.procurement_id}"]`);
        if (row) {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = 'badge bg-danger text-white'; // Set to red
                statusBadge.textContent = 'Overdue';
            }
        }
    });
}

setInterval(checkOverdue, 5000); // Check every 5 seconds

// Function to update procurement table dynamically
function updateProcurementTable(data) {
    const tableBodies = {
        all: document.getElementById('procurementTable'),
        pending: document.getElementById('procurementTablePending'),
        ongoing: document.getElementById('procurementTableOngoing'),
        overdue: document.getElementById('procurementTableOverdue'),
        done: document.getElementById('procurementTableDone')
    };

    // Clear all table bodies
    Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = '');

    if (data.length > 0) {
        // Sort data by status order and maintain original order for FIFO
        const statusOrder = { 'overdue': 1, 'ongoing': 2, 'pending': 3, 'done': 4 };
    
        data.sort((a, b) => {
            const statusA = (a.status || 'unknown').toLowerCase();
            const statusB = (b.status || 'unknown').toLowerCase();
    
            // Sort by status first
            const statusComparison = (statusOrder[statusA] || 5) - (statusOrder[statusB] || 5);
    
            // If statuses are the same, maintain original FIFO order
            return statusComparison !== 0 ? statusComparison : data.indexOf(a) - data.indexOf(b);
        });
    
        data.forEach(item => {
            const row = document.createElement('tr');

            const prNumberCell = document.createElement('td');
            prNumberCell.textContent = item.procurement_id;
            row.appendChild(prNumberCell);

            const activityCell = document.createElement('td');
            activityCell.textContent = item.activity;
            row.appendChild(activityCell);

            const statusCell = document.createElement('td');
            const badge = document.createElement('span');

            let statusMessage = item.status || '';
            let unitMessage = item.unit ? ` at ${item.unit}` : '';

            if (statusMessage.toLowerCase() === 'done') {
                unitMessage = '';
            }

            badge.className = getStatusClass(item.status || '');
            badge.textContent = statusMessage + unitMessage;

            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            tableBodies.all.appendChild(row); // Append the original row to "All" only

            if (statusMessage.toLowerCase() === 'done') {
                tableBodies.done.appendChild(row.cloneNode(true));
            } else if (statusMessage.toLowerCase() === 'ongoing') {
                tableBodies.ongoing.appendChild(row.cloneNode(true));
            } else if (statusMessage.toLowerCase() === 'overdue') {
                tableBodies.overdue.appendChild(row.cloneNode(true));
            } else if (statusMessage.toLowerCase() === 'pending') {
                tableBodies.pending.appendChild(row.cloneNode(true));
            }


        });

        // Add event listener to each table body for delegation
        Object.values(tableBodies).forEach(tableBody => {
            tableBody.addEventListener('click', function(event) {
                const prNumberCell = event.target.closest('td');
                if (prNumberCell && prNumberCell.parentElement) {
                    const procurementId = prNumberCell.textContent;
                    const row = prNumberCell.parentElement;
                    openProcurementModal({ procurement_id: procurementId });
                }
            });
        });
    } else {
        const emptyMessage = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.setAttribute('colspan', '3');
        emptyCell.textContent = 'No procurement records found.';
        emptyMessage.appendChild(emptyCell);
        tableBodies.all.appendChild(emptyMessage);
    }
}

// Event listener for the year filter
document.getElementById('year')?.addEventListener('change', function () {
    const yearFilter = this.value;
    const activeTab = document.querySelector('.nav-link.active');
    const status = activeTab ? activeTab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase() : 'all';

    fetchProcurementData(yearFilter, status);
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
