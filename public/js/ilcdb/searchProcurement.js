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
        case "for dv creation":
            return "badge bg-primary text-white"; // Blue for this specific status
        case "returned to user": // Corrected and added possible typo from the controller
            return "badge bg-pink text-white"; // Gray for returned
        case "for iar / par / ics / rfi creation":
            return "badge bg-primary text-white"; // Light blue for ongoing documentation
        case "for ors creation":
            return "badge bg-primary text-white"; // Yellow for pre-obligation steps
        case "for obligation":
            return "badge bg-primary text-white"; // Green for budget obligation
        case "for payment processing":
            return "badge bg-primary text-white"; // Darker style for accounting status
        case "waiting for budget":
            return "badge bg-pink text-dark"; // Light for pending budget
        case "done":
            return "badge bg-success text-white"; // Green for completion
        case "request for abstract, philgeps posting (if applicable)":
            return "badge bg-primary text-white"; // Yellow for initial processing steps
        case "overdue":
            return "badge bg-danger text-white"; // Red for overdue
        default:
            return "badge bg-light text-dark"; // Default for unknown or no status
    }
}


// Function to search for procurement based on the search term
function searchProcurement() {
    const query = document.getElementById('searchBar').value;
    console.log("Search query: ", query); // Debugging log to check the query value

    // If the query is empty, do not fetch the data
    if (!query.trim()) {
        console.log("No query entered. Exiting search.");
        return;
    }

    fetch(`/api/search-procurement-ilcdb?query=${query}`)
        .then((response) => {
            if (!response.ok) {
                console.error('Failed to fetch data:', response.status);
                return [];
            }
            return response.json();
        })
        .then((data) => {
            const tableBodies = {
                all: document.getElementById('procurementTable'),
                overdue: document.getElementById('procurementTableOverdue'),
                done: document.getElementById('procurementTableDone'),
            };

            // Clear any existing rows in the tables
            Object.values(tableBodies).forEach((tableBody) => (tableBody.innerHTML = ''));

            if (data.length > 0) {
                data.forEach((item) => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-procurement-id", item.procurement_id);

                    // PR NUMBER cell
                    const prNumberCell = document.createElement("td");
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // CATEGORY cell
                    const categoryCell = document.createElement("td");
                    categoryCell.textContent = item.category ;
                    row.appendChild(categoryCell);

                    // ACTIVITY NAME cell
                    const activityCell = document.createElement("td");
                    activityCell.textContent = item.activity || "N/A";
                    row.appendChild(activityCell);

                    // STATUS cell
                    const statusCell = document.createElement("td");
                    const badge = document.createElement("span");

                    const statusMessage = (item.status || "").toLowerCase();
                    badge.className = getStatusClass(statusMessage);
                    badge.textContent = item.status || "Unknown Status";
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    
                    // Append row to the appropriate table body
                    if (statusMessage === "done") {
                        tableBodies.done.appendChild(row);
                        tableBodies.all.appendChild(row.cloneNode(true)); // Clone the row for the all table
                    } else if (statusMessage === "overdue") {
                        tableBodies.overdue.appendChild(row);
                        tableBodies.all.appendChild(row.cloneNode(true)); // Clone the row for the all table
                    } else {
                        tableBodies.all.appendChild(row);
                    }
                });
            } else {
                Object.values(tableBodies).forEach((tableBody) => {
                    const emptyMessage = document.createElement('tr');
                    const emptyCell = document.createElement('td');
                    emptyCell.setAttribute('colspan', '4'); // Match column count
                    emptyCell.textContent = 'No procurement records found for the search term.';
                    emptyMessage.appendChild(emptyCell);
                    tableBody.appendChild(emptyMessage);
                });
            }
        })
        .catch((error) => {
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

            const categoryCell = document.createElement('td');
            categoryCell.textContent = item.category  ; // Add category cell
            row.appendChild(categoryCell);

            // ACTIVITY cell
            const activityCell = document.createElement('td');
            activityCell.textContent = item.activity;
            row.appendChild(activityCell);

            // STATUS & UNIT cell
            const statusCell = document.createElement('td');
            const badge = document.createElement('span');

            let statusMessage = item.status || '';
            

            if (statusMessage.toLowerCase() === 'done') {
                unitMessage = ''; // Don't append unit when status is "done"
            }

            badge.className = getStatusClass(item.status || '');
            badge.textContent = statusMessage;

            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            tableBody.appendChild(row);
        });
    } else {
        const emptyMessage = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.setAttribute('colspan', '4');
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