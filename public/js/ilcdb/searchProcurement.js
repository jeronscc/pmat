// Add event listener for the search input to reset when empty
document.getElementById('searchBar').addEventListener('input', function (event) {
    if (!event.target.value.trim()) {
        console.log("Search bar cleared, resetting table...");
        fetchProcurementData(''); // Call your function to reload default data
    }
});
//Add eventlistener for the search button
document.getElementById('searchBar').addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        searchProcurement();
    }
});

document.querySelector('.search-button').addEventListener('click', function () {
    searchProcurement();
});

function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'badge bg-secondary text-white';  // Gray for pending
        case 'ongoing':
            return 'badge bg-warning text-dark';  // Orangeish yellow for ongoing
        case 'done':
            return 'badge bg-success text-white';  // Green for done
        case 'overdue':
            return 'badge bg-danger text-white';
        default:
            return 'badge bg-light text-dark';  // Default for no status or unknown status
    }
}

// Function to search for procurement based on the search term
function searchProcurement() {
    const query = document.getElementById('searchBar').value;
    console.log("Search query: ", query);  // Debugging log to check the query value

    // If the query is empty, do not fetch the data
    if (!query.trim()) {
        console.log("No query entered. Exiting search.");
        return;
    }

    fetch(`/api/search-procurement-ilcdb?query=${query}`)
        .then(response => {
            if (!response.ok) {
                console.error('Failed to fetch data:', response.status);
                return [];
            }
            return response.json();
        })
        .then(data => {
            const tableBodies = {
                all: document.getElementById('procurementTable'),
                pending: document.getElementById('procurementTablePending'),
                ongoing: document.getElementById('procurementTableOngoing'),
                overdue: document.getElementById('procurementTableOverdue'),
                done: document.getElementById('procurementTableDone')
            };

            // Clear any existing rows in the tables
            Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = '');

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-procurement-id', item.procurement_id); // Ensure each row has a unique identifier

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

                    let statusMessage = item.status || ''; // If no status, it will be empty
                    let unitMessage = item.unit ? ` at ${item.unit}` : ''; // If unit exists, append it

                    // If status is "done", do not append the unit
                    if (statusMessage.toLowerCase() === 'done') {
                        unitMessage = ''; // Don't append the unit when status is "done"
                    }

                    // Combine status and unit
                    badge.className = getStatusClass(item.status || ''); // Apply appropriate badge class
                    badge.textContent = statusMessage + unitMessage; // Status and Unit

                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append the row to the appropriate table based on the status
                    tableBodies.all.appendChild(row);
                    if (statusMessage.toLowerCase() === 'done') tableBodies.done.appendChild(row.cloneNode(true));
                    else if (statusMessage.toLowerCase() === 'pending') tableBodies.pending.appendChild(row.cloneNode(true));
                    else if (statusMessage.toLowerCase() === 'ongoing') tableBodies.ongoing.appendChild(row.cloneNode(true));
                    else if (statusMessage.toLowerCase() === 'overdue') tableBodies.overdue.appendChild(row.cloneNode(true));
                });
            } else {
                Object.values(tableBodies).forEach(tableBody => {
                    const emptyMessage = document.createElement('tr');
                    const emptyCell = document.createElement('td');
                    emptyCell.setAttribute('colspan', '3');
                    emptyCell.textContent = 'No procurement records found for the search term.';
                    emptyMessage.appendChild(emptyCell);
                    tableBody.appendChild(emptyMessage);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching procurement data:', error);
        });
}

// Function to update the procurement table
function updateProcurementTable(data) {
    const tableBody = document.getElementById('procurementTable');
    tableBody.innerHTML = ''; // Clear any existing rows

    if (data.length > 0) {
        data.forEach(item => {
            const row = document.createElement('tr');

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

            if (statusMessage.toLowerCase() === 'done') {
                unitMessage = ''; // Don't append unit when status is "done"
            }

            badge.className = getStatusClass(item.status || '');
            badge.textContent = statusMessage + unitMessage;

            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            tableBody.appendChild(row);
        });
    } else {
        const emptyMessage = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.setAttribute('colspan', '3');
        emptyCell.textContent = 'No procurement records found.';
        emptyMessage.appendChild(emptyCell);
        tableBody.appendChild(emptyMessage);
    }
}

function checkOverdue() {
    fetch('/api/check-overdue')
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

setInterval(checkOverdue, 5000); // Check overdue status every 5 seconds