// Add event listener for the search input to reset when empty
document.getElementById('searchBar').addEventListener('input', function (event) {
    const query = event.target.value.trim();
    localStorage.setItem('searchQuery', query); // Save query to localStorage
    if (!query) {
        localStorage.removeItem('searchQuery'); // Remove query if empty
        console.log("Search bar cleared, resetting table...");
        fetchProcurementData(''); // Ensure the correct function is called to reload default data
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

// Trigger search on page load if there is a query in localStorage
document.addEventListener('DOMContentLoaded', function () {
    const searchBar = document.getElementById('searchBar');
    const savedQuery = localStorage.getItem('searchQuery'); // Retrieve query from localStorage
    if (savedQuery) {
        searchBar.value = savedQuery; // Set the search bar value
        searchProcurement(); // Perform search if query exists
    }
});

function getStatusClass(status) {
    const baseClass = "custom-font-size"
    switch (status.toLowerCase()) {
        case 'pending':
            return `badge bg-secondary text-white p-2 ${baseClass}`;  // Gray for pending
        case 'ongoing':
            return `badge bg-warning text-dark p-2 ${baseClass}`;  // Orangeish yellow for ongoing
        case 'done':
            return `badge bg-success text-white p-2 ${baseClass}`;  // Green for done
        case 'overdue':
            return `badge bg-danger text-white p-2 ${baseClass}`;
        default:
            return `badge bg-light text-dark p-2 ${baseClass}`;  // Default for no status or unknown status
    }
}

const modules = [
    { apiUrl: '/api/fetch-procurement-ilcdb', tableSelector: '#procurementTable' },
    { apiUrl: '/api/dtc/fetch-procurement-dtc', tableSelector: '#procurementTable' },
    { apiUrl: '/api/click/fetch-procurement-click', tableSelector: '#procurementTable' },
    { apiUrl: '/api/spark/fetch-procurement-spark', tableSelector: '#procurementTable' }
];

// Function to search for procurement based on the search term
function searchProcurement() {
    const query = document.getElementById('searchBar').value.trim().toLowerCase(); // Trim and convert query to lowercase
    console.log("Search query: ", query);  // Debugging log to check the query value

    // If the query is empty, do not fetch the data
    if (!query) {
        console.log("No query entered. Exiting search.");
        return;
    }

    const unifiedResults = []; // Array to store results from all modules
    const tableBody = document.querySelector('#procurementTable'); // Unified table selector

    if (!tableBody) {
        console.warn("Unified table element not found.");
        return;
    }

    const fetchPromises = modules.map(module => {
        return fetch(`${module.apiUrl}?query=${encodeURIComponent(query)}`) // Ensure the query is properly encoded
            .then(response => {
                if (!response.ok) {
                    console.error(`Failed to fetch data from ${module.apiUrl}:`, response.status);
                    return [];
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    // Filter results to match the query
                    const filteredData = data.filter(item => {
                        return (
                            item.procurement_id.toLowerCase().includes(query) ||
                            (item.procurement_category && item.procurement_category.toLowerCase().includes(query)) ||
                            (item.activity && item.activity.toLowerCase().includes(query)) ||
                            (item.status && item.status.toLowerCase().includes(query))
                        );
                    });
                    unifiedResults.push(...filteredData); // Add filtered results to the unified array
                }
            })
            .catch(error => {
                console.error(`Error fetching procurement data from ${module.apiUrl}:`, error);
            });
    });

    Promise.all(fetchPromises).then(() => {
        tableBody.innerHTML = ''; // Clear the table only after all fetches are complete

        if (unifiedResults.length > 0) {
            unifiedResults.forEach(item => {
                const row = document.createElement('tr');
                row.setAttribute('data-procurement-id', item.procurement_id); // Ensure each row has a unique identifier

                // PR NUMBER cell (procurement_id)
                const prNumberCell = document.createElement('td');
                prNumberCell.textContent = item.procurement_id;
                row.appendChild(prNumberCell);

                // CATEGORY cell
                const categoryCell = document.createElement('td');
                categoryCell.textContent = item.procurement_category || 'N/A'; // Add category cell
                row.appendChild(categoryCell);

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
            emptyCell.setAttribute('colspan', '4');
            emptyCell.textContent = 'No procurement records found for the search term.';
            emptyMessage.appendChild(emptyCell);
            tableBody.appendChild(emptyMessage);
        }
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

            // CATEGORY cell
            const categoryCell = document.createElement('td');
            categoryCell.textContent = item.category || 'N/A'; // Add category cell
            row.appendChild(categoryCell);
            
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

