// Fetch SARO data from the API
// Wait for the window to load
window.onload = function() {
    // Initially load all SAROs when the page loads (with no balance)
    fetchSaroData('');
};

// Function to fetch and display SARO list based on the selected year or "all"
function filterSaroByYear(year) {
    // Reset the balance and SARO info
    document.getElementById('remainingBalance').textContent = '₱0';
    document.getElementById('currentViewingSaro').textContent = '';

    // URL for fetching SARO data (by year or all)
    const url = year === '' ? '/api/dtc/fetch-saro-dtc' : `/api/dtc/fetch-saro-dtc?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            saroList.innerHTML = ''; // Clear previous entries

            if (data.length > 0) {
                data.forEach(saro => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `${saro.saro_no}`;

                    listItem.addEventListener('click', function() {
                        displayCurrentBudget(saro);
                        fetchProcurementForSaro(saro.saro_no, year);
                        highlightSelectedItem(this);
                    });

                    saroList.appendChild(listItem);
                });
            } else {
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found for the selected year.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));

    // Fetch procurement data for the selected year
    fetchProcurementForYear(year);
}
