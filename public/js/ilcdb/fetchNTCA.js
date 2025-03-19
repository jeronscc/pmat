function fetchNTCABreakdown(ntcaNo) {
    fetch(`/api/ntca-breakdown/${ntcaNo}`)
        .then(response => response.json())
        .then(data => {
            const breakdownList = document.getElementById('ntcaBreakdownList');
            breakdownList.innerHTML = ''; // Clear existing items

            if (data.success) {
                const { first_q, second_q, third_q, fourth_q, current_budget } = data.ntca;
                const currentQuarter = getCurrentQuarter();

                // Add balances for each quarter
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter <span class="fw-bold">₱${first_q || 0}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter <span class="fw-bold">₱${second_q || 0}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter <span class="fw-bold">₱${third_q || 0}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter <span class="fw-bold">₱${fourth_q || 0}</span>
                    </li>
                `;

                // Highlight the current quarter
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        Current Quarter (${currentQuarter}) <span class="fw-bold text-primary">₱${data.ntca[currentQuarter.toLowerCase()] || 0}</span>
                    </li>
                `;

                // Add unclaimed NTCA balance
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Unclaimed NTCA Balance <span class="fw-bold text-success">₱${current_budget || 0}</span>
                    </li>
                `;
            } else {
                breakdownList.innerHTML = `
                    <li class="list-group-item text-danger">Failed to fetch NTCA breakdown.</li>
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
    fetchNTCABreakdown(ntcaNo);
});