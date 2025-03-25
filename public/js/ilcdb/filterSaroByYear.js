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
    const url = year === '' ? '/api/fetch-saro-ilcdb' : `/api/fetch-saro-ilcdb?year=${year}`;

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

    // Fetch NTCA data for the selected year
    fetchNTCAForYear(year);
}

// Function to fetch NTCA data for a specific year
function fetchNTCAForYear(year) {
    const url = year === '' ? '/api/fetch-ntca' : `/api/fetch-ntca?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const ntcaList = document.getElementById('ntcaBreakdownList');
            ntcaList.innerHTML = ''; // Clear existing NTCA records

            if (data.length > 0) {
                data.forEach(ntca => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `NTCA No: ${ntca.ntca_no}, Budget: ₱${ntca.current_budget ? ntca.current_budget.toLocaleString() : '0'}`;
                    ntcaList.appendChild(listItem);
                });
            } else {
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No NTCA records found for the selected year.';
                ntcaList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching NTCA data:', error));
}

// Function to display the remaining balance for the clicked SARO
function displayCurrentBudget(saro) {
    // Set the current SARO name in the container
    document.getElementById('currentViewingSaro').textContent = `${saro.saro_no}`;
    document.getElementById('currentSaroName').textContent = `${saro.saro_no}`;

    // Check if current_budget exists and format it with comma separation
    const currentBudget = saro.current_budget && !isNaN(saro.current_budget)
        ? `₱${saro.current_budget.toLocaleString()}`
        : "₱0";

    // Display the current budget in the "remainingBalance" container
    document.getElementById('remainingBalance').textContent = currentBudget;

    // Fetch and display the requirements associated with the selected SARO
    fetchProcurementForSaro(saro.saro_no);

    // Fetch and display NTCA records for the selected SARO
    fetchNTCAForSaro(saro.saro_no);
}
