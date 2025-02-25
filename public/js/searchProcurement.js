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

// Function to search for procurement based on the search term
function searchProcurement() {
    const query = document.getElementById('searchBar').value;

    fetch(`/api/search-procurement-ilcdb?query=${query}`)
        .then(response => response.json())
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

                    // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; // Placeholder
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append the row to the table body
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
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}