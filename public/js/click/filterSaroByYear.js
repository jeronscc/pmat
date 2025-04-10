// Fetch SARO data from the API
// Wait for the window to load
window.onload = function () {
    // Initially load all SAROs when the page loads (with no balance)
    fetchSaroData('');
};

// Function to fetch and display SARO list based on the selected year or "all"
function filterSaroByYear(year) {
    // Reset the balance and SARO info
    const remainingBalance = document.getElementById('remainingBalance');
    const currentViewingSaro = document.getElementById('currentViewingSaro');
    const ntcaList = document.getElementById('ntcaBreakdownList');
    const ntcaLabelElement = document.getElementById('ntcaLabel');
    const ntcaBalanceElement = document.getElementById('ntcaBalance');

    if (remainingBalance) remainingBalance.textContent = '₱0';
    if (currentViewingSaro) currentViewingSaro.textContent = '';
    if (ntcaList) ntcaList.innerHTML = '';
    if (ntcaLabelElement) ntcaLabelElement.textContent = 'NTCA:';
    if (ntcaBalanceElement) ntcaBalanceElement.textContent = '₱0';

    // URL for fetching SARO data (by year or all)
    const url = year === '' ? '/api/click/fetch-saro-click' : `/api/click/fetch-saro-click?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            if (!saroList) return;

            saroList.innerHTML = ''; // Clear previous entries

            if (data.length > 0) {
                data.forEach(saro => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `${saro.saro_no}`;

                    listItem.addEventListener('click', function () {
                        displayCurrentBudget(saro);
                        fetchProcurementForSaro(saro.saro_no, year);
                        fetchNTCAForSaro(saro.saro_no); // Fetch NTCA for the selected SARO
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

function fetchNTCAForSaro(saroNo) {
    fetch(`/api/click/fetch-ntca-by-saro/${saroNo}`)
        .then(response => response.json())
        .then(data => {
            const ntcaList = document.getElementById('ntcaBreakdownList');
            const ntcaLabelElement = document.getElementById('ntcaLabel');
            const ntcaBalanceElement = document.getElementById('ntcaBalance');
            if (!ntcaList || !ntcaLabelElement || !ntcaBalanceElement) return;

            ntcaList.innerHTML = ''; // Clear existing NTCA records

            if (data.success) {
                data.ntca.forEach(ntca => {
                    // Determine the current quarter dynamically
                    const currentQuarter = getCurrentQuarter(ntca);

                    // Update NTCA container label and balance for the current quarter
                    ntcaLabelElement.textContent = `NTCA (${ntca.ntca_no} | ${currentQuarter ? currentQuarter : 'No Quarter'})`;
                    const currentQuarterBalance = currentQuarter ? ntca[currentQuarter] : 0;
                    ntcaBalanceElement.textContent = currentQuarterBalance
                        ? `₱${Number(currentQuarterBalance).toLocaleString()}`
                        : '₱0';

                    // Add NTCA breakdown to the list
                    ntcaList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>NTCA No:</strong>
                        <span class="fw-bold">
                            ${ntca.ntca_no}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>NTCA Budget Allocated:</strong>
                        <span class="fw-bold">
                            ${ntca.ntca_budget ? "₱" + Number(ntca.ntca_budget).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Unassigned NTCA Budget:</strong>
                        <span class="fw-bold">
                            ${ntca.current_budget ? "₱" + Number(ntca.current_budget).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter: 
                        <span class="fw-bold">
                            ${ntca.first_q ? "₱" + Number(ntca.first_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter: 
                        <span class="fw-bold">
                            ${ntca.second_q ? "₱" + Number(ntca.second_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter: 
                        <span class="fw-bold">
                            ${ntca.third_q ? "₱" + Number(ntca.third_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter: 
                        <span class="fw-bold">
                            ${ntca.fourth_q ? "₱" + Number(ntca.fourth_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                `;

                    // Fetch breakdown for the NTCA
                    fetchNTCABreakdown(ntca.ntca_no);
                });
            } else {
                ntcaList.innerHTML = `
                    <li class="list-group-item text-danger">${data.message}</li>
                `;
                ntcaLabelElement.textContent = 'NTCA:';
                ntcaBalanceElement.textContent = '₱0';
            }
        })
        .catch(error => {
            console.error('Error fetching NTCA records:', error);
        });
}

// Helper function to determine the most recent quarter with a value
function getCurrentQuarter(ntca) {
    if (ntca.fourth_q > 0) return 'fourth_q';
    if (ntca.third_q > 0) return 'third_q';
    if (ntca.second_q > 0) return 'second_q';
    if (ntca.first_q > 0) return 'first_q';
    return null; // No quarter has a value
}
