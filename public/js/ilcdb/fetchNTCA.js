function fetchNTCABreakdown(ntcaNo) {
    if (!ntcaNo) {
        console.error('NTCA No. is missing.');
        return;
    }

    fetch(`/api/ntca-breakdown/${ntcaNo}`)
        .then(response => response.json())
        .then(data => {
            const breakdownList = document.getElementById('ntcaBreakdownList');
            breakdownList.innerHTML = ''; // Clear existing items

            if (data.success) {
                const { ntca_no, first_q, second_q, third_q, fourth_q, current_budget, total_quarters } = data.ntca;

                // Add NTCA No.
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>NTCA No:</strong> <span>${ntca_no}</span>
                    </li>
                `;

                // Add Unclaimed NTCA Budget
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Unclaimed NTCA Budget <span class="fw-bold text-success">₱${current_budget.toLocaleString()}</span>
                    </li>
                `;

                // Add balances for each quarter
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter <span class="fw-bold">₱${first_q.toLocaleString()}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter <span class="fw-bold">₱${second_q.toLocaleString()}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter <span class="fw-bold">₱${third_q.toLocaleString()}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter <span class="fw-bold">₱${fourth_q.toLocaleString()}</span>
                    </li>
                `;

                // Add total of all quarters
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Total of All Quarters <span class="fw-bold text-primary">₱${total_quarters.toLocaleString()}</span>
                    </li>
                `;
            } else {
                breakdownList.innerHTML = `
                    <li class="list-group-item text-danger">${data.message}</li>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching NTCA breakdown:', error);
        });
}

// Helper function to get the current quarter
function getCurrentQuarter() {
    const month = new Date().getMonth() + 1; // Months are 0-based
    if (month <= 3) return 'First Quarter';
    if (month <= 6) return 'Second Quarter';
    if (month <= 9) return 'Third Quarter';
    return 'Fourth Quarter';
}

// Trigger NTCA breakdown fetch when the modal is opened
document.getElementById('ntcaBreakdownModal').addEventListener('shown.bs.modal', function () {
    const ntcaNo = document.getElementById('ntca_number').value; // Replace with the actual NTCA number
    if (!ntcaNo) {
        console.error('NTCA No. is missing.');
        return;
    }
    console.log(`Fetching NTCA breakdown for NTCA No: ${ntcaNo}`);
    fetchNTCABreakdown(ntcaNo);
});

function fetchNTCABalance(ntcaNo, quarter) {
    if (!ntcaNo || !quarter) {
        console.error('NTCA No. or Quarter is missing.');
        return;
    }

    fetch(`/api/ntca-balance/${ntcaNo}/${quarter}`)
        .then(response => response.json())
        .then(data => {
            const ntcaBalanceElement = document.getElementById('ntcaBalance');
            const ntcaLabelElement = document.getElementById('ntcaLabel'); // Add a label for NTCA

            if (data.success) {
                const { balance } = data;

                // Update NTCA label to include NTCA No. and Quarter
                ntcaLabelElement.textContent = `Unassigned Budget for NTCA (${ntcaNo})`;

                // Update NTCA balance for the current quarter
                ntcaBalanceElement.textContent = `₱${balance.toLocaleString()}`;
            } else {
                ntcaLabelElement.textContent = `Unassigned Budget for NTCA (${ntcaNo})`;
                ntcaBalanceElement.textContent = '₱0';
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching NTCA balance:', error);
            document.getElementById('ntcaBalance').textContent = '₱0';
        });
}

// Trigger NTCA balance fetch when a SARO is selected
document.getElementById('saro_select').addEventListener('change', function () {
    const selectedSaro = this.value;
    if (selectedSaro) {
        const ntcaNo = document.getElementById('ntca_number').value; // Ensure NTCA number is set
        const quarter = document.getElementById('quarter').value; // Get the selected quarter
        if (ntcaNo && quarter) {
            fetchNTCABalance(ntcaNo, quarter);
            fetchNTCABreakdown(ntcaNo);
        }
    }
});