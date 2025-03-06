//Add eventlistener for the search button
document.getElementById('searchBar').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        searchProcurement();
    }
});

document.querySelector('.search-button').addEventListener('click', function() {
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
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear any existing rows in the table

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

                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found for the search term.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => {
            console.error('Error fetching procurement data:', error);
        });
}

