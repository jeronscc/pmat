// FETCH PROCUREMENT DATA BY SARO FILTER
function fetchProcurementForSaro(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

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
                    prNumberCell.textContent = item.procurement_id; // Assuming "procurement_id" is returned
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity; // Assuming "activity" is returned
                    row.appendChild(activityCell);

                    // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending';  // Placeholder, since we don't have the status in the API response
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
        .catch(error => console.error('Error fetching procurement requirements:', error));
}

// FETCH PROCUREMENT DATA BY YEAR FILTER
function fetchProcurementForYear(year) {
    const url = year === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?year=${year}`;

    fetch(url)
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

                    // STATUS cell (this is just a placeholder as we don't have status in the data)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; 
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

                // Set Edit button link to the procurementform page (fixing the variable reference)
                const editButton = document.getElementById('editProcurementFormBtn');
                editButton.href = `/procurementform/${data.procurement_id}`;

                // Show modal (no need to re-declare 'modal')
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            }
        })
        .catch(error => {
            console.error('Error fetching procurement details:', error);
            alert('Failed to load procurement details.');
        });
}



// Fetch procurement data when the page loads (optional)
document.addEventListener('DOMContentLoaded', () => fetchProcurementRequirements(''));

function highlightSelectedItem(selectedItem) {
        const items = document.querySelectorAll('.saro-list .list-group-item');
        items.forEach(item => item.classList.remove('active'));
        selectedItem.classList.add('active');
}


// Custom function to format numbers with commas
function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function filterProcurementByYear(year) {
    fetchProcurementData(year);
}

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

                    // STATUS cell (this is just a placeholder as we don't have status in the data)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; 
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to table
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

document.getElementById('year').addEventListener('change', function() {
    // When the year changes, fetch the procurement data based on selected year
    fetchProcurementData(this.value);
});
const apiUrl = '/api/fetch-procurement-ilcdb'; // replace with your actual API endpoint

// Fetch data from the API and populate the table
window.addEventListener('DOMContentLoaded', (event) => {
    fetch(apiUrl)
    .then(response => response.json())  // Parse the response as JSON
    .then(data => {
        console.log(data); // Log the data to the console to check the response
        const tableBody = document.getElementById('procurementTable');
        
        // Clear any existing rows in the table
        tableBody.innerHTML = '';

        // Loop through the fetched data and create table rows
        data.forEach(item => {
            const row = document.createElement('tr');

            // PR NUMBER cell (procurement_id)
            const prNumberCell = document.createElement('td');
            prNumberCell.textContent = item.procurement_id; // Assuming "procurement_id" is returned
            row.appendChild(prNumberCell);

            // ACTIVITY cell
            const activityCell = document.createElement('td');
            activityCell.textContent = item.activity; // Assuming "activity" is returned
            row.appendChild(activityCell);

            // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
            const statusCell = document.createElement('td');
            const badge = document.createElement('span');
            badge.classList.add('badge', 'bg-warning', 'text-dark');
            badge.textContent = 'Pending';  // Placeholder, since we don't have the status in the API response
            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            // Append the row to the table body
            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

});

