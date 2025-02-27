// Fetch SARO data from the API
// Wait for the window to load
window.onload = function() {
    // Initially load all SAROs when the page loads (with no balance)
    fetchSaroData('');
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
                        fetchProcurementForSaro(saro.saro_no, year); // Fetch and display procurement for this SARO
                        highlightSelectedItem(this);
                    });

                    // Append the list item to the SARO list
                    saroList.appendChild(listItem);
                });
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