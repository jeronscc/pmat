// Fetch SARO data from the API when the page loads
window.onload = function() {
    // Initially load all SAROs when the page loads (with no balance)
    filterSaroByYear('');  // Default to all years
};

// Function to fetch and display SARO list based on the selected year or "all"
function filterSaroByYear(year) {
    // Reset the balance display to "₱0" when the year filter changes
    document.getElementById('remainingBalance').textContent = '₱0';
    document.getElementById('currentViewingSaro').textContent = '';

    // Update SARO list based on year
    const url = year === '' ? '/api/fetch-saro-ilcdb' : `/api/fetch-saro-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            saroList.innerHTML = ''; // Clear previous entries

            if (data.length > 0) {
                data.forEach(saro => {
                    // Create list item for each SARO number
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `${saro.saro_no}`;

                    // Add click event to each SARO number
                    listItem.addEventListener('click', function() {
                        displayCurrentBudget(saro); // Show balance when SARO is clicked
                        fetchProcurementForSaro(saro.saro_no); // Fetch and display procurement for this SARO
                        highlightSelectedItem(this); // Optional: Highlight the selected item
                    });

                    // Append the list item to the SARO list
                    saroList.appendChild(listItem);
                });
                reinitializeTooltips(); // Reinitialize tooltips after appending items
            } else {
                // Show message if no SARO data is available
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found for the selected year.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));

    // Also fetch and display procurement data for the selected year
    fetchProcurementForYear(year);
}

// Function to initialize Bootstrap tooltips
function reinitializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// SARO hover functionality to show description
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/fetch-saro-ilcdb')
        .then(response => response.json())
        .then(data => {
            const saroContainer = document.querySelector('.saro-container');
            const remainingBalance = document.querySelector('.balance-container p');

            saroContainer.innerHTML = ''; // Clear any existing SARO entries

            if (data.length > 0) {
                data.forEach(saro => {
                    const saroElement = document.createElement('p');
                    saroElement.textContent = saro.saro_no;
                    saroElement.style.margin = '5px 0';
                    saroElement.style.padding = '5px';
                    saroElement.style.cursor = 'pointer';
                    saroElement.setAttribute('data-bs-toggle', 'tooltip');
                    saroElement.setAttribute('data-bs-placement', 'right');
                    saroElement.setAttribute('title', `Description: ${saro.description}`);

                    // Click event to fetch procurement data when SARO is clicked
                    saroElement.addEventListener('click', function() {
                        remainingBalance.textContent = `₱${Number(saro.current_budget).toLocaleString()}`;
                        fetchProcurementData(saro.saro_no); // Fetch and display procurement data for selected SARO
                    });

                    saroContainer.appendChild(saroElement);
                });

                // Initialize Bootstrap tooltips with a short delay
                setTimeout(reinitializeTooltips, 100);
            } else {
                const emptyMessage = document.createElement('p');
                emptyMessage.textContent = 'No SARO records found.';
                emptyMessage.style.margin = "5px 0";
                emptyMessage.style.padding = "5px";
                saroContainer.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
});

// Event delegation for SARO list in the filter functionality
document.querySelector('.saro-list').addEventListener('click', function(event) {
    if (event.target.matches('li')) {
        const saroNo = event.target.textContent;
        // Handle SARO click for procurement and balance display
        fetchProcurementForSaro(saroNo);
        displayCurrentBudget(saroNo);
    }
});

// Event delegation for SARO hover functionality
document.querySelector('.saro-container').addEventListener('click', function(event) {
    if (event.target.matches('p')) {
        const saroNo = event.target.textContent;
        const remainingBalance = document.querySelector('.balance-container p');
        // Handle the click for this SARO number
        remainingBalance.textContent = `₱${Number(saroNo.current_budget).toLocaleString()}`;
        fetchProcurementData(saroNo.saro_no);
    }
});
