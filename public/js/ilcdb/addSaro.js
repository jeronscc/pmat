document.getElementById('saveSaro').addEventListener('click', function () {
    const saroNumber = document.getElementById('saro_number').value;
    const saroDesc = document.getElementById('saroDesc').value;
    const saroYear = document.getElementById('saro_year').value;
    const saroBudget = document.getElementById('saro_budget').value;

    const ntcaNumber = document.getElementById('ntca_number').value;
    const ntcaBudget = document.getElementById('budget').value;
    const ntcaQuarter = document.getElementById('quarter').value;
    const saroSelect = document.getElementById('saro_select').value;

    if (document.getElementById('ntcaFields').classList.contains('d-none')) {
        // Save SARO
        if (!saroNumber || !saroDesc || !saroYear || !saroBudget) {
            alert('All SARO fields must be filled out.');
            return;
        }

        fetch('/api/add-saro-ilcdb', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                saro_number: saroNumber,
                budget: saroBudget,
                saro_year: saroYear,
                saroDesc: saroDesc,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
    
                    // Close modal and reset form
                    const addSaroModal = bootstrap.Modal.getInstance(document.getElementById('addSaroModal'));
                    addSaroModal.hide();
                    document.getElementById('saroForm').reset();
    
                    // Call the function from fetchSaro.js to refresh the list
                    if (typeof fetchSaroDataAndRequirements === "function") {
                        fetchSaroDataAndRequirements("");  
                    } else {
                        console.error("fetchSaroDataAndRequirements is not defined");
                    }
                } else {
                    alert('Failed to add SARO: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error adding SARO:', error);
                alert('An error occurred while adding SARO.');
            });
    } else {
        // Save NTCA
        if (!ntcaNumber || !ntcaBudget || !ntcaQuarter || !saroSelect) {
            alert('All NTCA fields must be filled out.');
            return;
        }

        // Check if the selected quarter already has a value
        fetch(`/api/ntca-breakdown/${ntcaNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.ntca[ntcaQuarter] > 0) {
                        alert(`The selected quarter (${ntcaQuarter}) already has a balance. Please choose a different quarter.`);
                        return;
                    }
                }

                // Proceed with saving NTCA
                fetch('/api/save-ntca', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        ntca_no: ntcaNumber,
                        budget: ntcaBudget,
                        quarter: ntcaQuarter,
                        saro_no: saroSelect,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            const addSaroModal = bootstrap.Modal.getInstance(document.getElementById('addSaroModal'));
                            addSaroModal.hide();
                            document.getElementById('saroForm').reset();
                        } else {
                            alert('Failed to save NTCA: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving NTCA:', error);
                        alert('An error occurred while saving NTCA.');
                    });
            })
            .catch(error => {
                console.error('Error validating NTCA quarter:', error);
            });
    }
});

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
                const { ntca_no, first_q, second_q, third_q, fourth_q, current_budget } = data.ntca;

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

                // Calculate total of all quarters
                const totalQuarters = first_q + second_q + third_q + fourth_q;
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Total of All Quarters <span class="fw-bold text-primary">₱${totalQuarters.toLocaleString()}</span>
                    </li>
                `;

                // Calculate budget surplus or deficit
                const currentMonth = new Date().getMonth() + 1;
                const currentQuarter = currentMonth <= 3 ? 'first_q' :
                                       currentMonth <= 6 ? 'second_q' :
                                       currentMonth <= 9 ? 'third_q' : 'fourth_q';
                const remainingBalance = data.ntca[currentQuarter] ?? 0;

                if (remainingBalance > 0) {
                    breakdownList.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between">
                            Budget Surplus <span class="fw-bold text-success">₱${remainingBalance.toLocaleString()}</span>
                        </li>
                    `;
                } else if (remainingBalance < 0) {
                    breakdownList.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between">
                            Budget Deficit <span class="fw-bold text-danger">₱${Math.abs(remainingBalance).toLocaleString()}</span>
                        </li>
                    `;
                }
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

document.getElementById('categorySelect').addEventListener('change', function () {
    const category = this.value;

    // Show or hide fields based on the selected category
    if (category === 'NTCA') {
        document.getElementById('ntcaFields').classList.remove('d-none');
        document.getElementById('saroFields').classList.add('d-none');
        generateNTCANumber();
        populateSARODropdown();
    } else if (category === 'SARO') {
        document.getElementById('saroFields').classList.remove('d-none');
        document.getElementById('ntcaFields').classList.add('d-none');
    }
});

// Function to generate the NTCA number
function generateNTCANumber() {
    const saroSelect = document.getElementById('saro_select');
    const selectedSaro = saroSelect.options[saroSelect.selectedIndex]?.value; // Get selected SARO number

    if (!selectedSaro) {
        console.error('No SARO selected.');
        return;
    }

    const lastDigits = selectedSaro.slice(-6); // Extract last 6 digits of SARO number
    const ntcaNumber = `NTCA-${lastDigits}`;
    document.getElementById('ntca_number').value = ntcaNumber;

    // Update NTCA label dynamically
    const ntcaLabelElement = document.getElementById('ntcaLabel');
    ntcaLabelElement.textContent = `NTCA (${ntcaNumber})`;
}

// Function to populate the SARO dropdown dynamically (fetch from the server)
function populateSARODropdown() {
    const saroSelect = document.getElementById('saro_select');
    saroSelect.innerHTML = ''; // Clear existing options

    fetch('/api/fetch-saro-ilcdb')
        .then(response => response.json())
        .then(data => {
            data.forEach(saro => {
                const option = document.createElement('option');
                option.value = saro.saro_no;
                option.textContent = `${saro.saro_no} (${saro.year})`;
                saroSelect.appendChild(option);
            });

            // Trigger NTCA number generation when a SARO is selected
            saroSelect.addEventListener('change', generateNTCANumber);
        })
        .catch(error => console.error('Error fetching SAROs:', error));
}

// Call populateSARODropdown when the modal is opened
document.getElementById('addSaroModal').addEventListener('shown.bs.modal', populateSARODropdown);

document.getElementById('quarter').addEventListener('change', function () {
    const ntcaNo = document.getElementById('ntca_number').value;
    const quarter = this.value;
    if (ntcaNo && quarter) {
        fetchNTCABalance(ntcaNo, quarter);
    }
});

document.getElementById('saro_select').addEventListener('change', function () {
    const selectedSaro = this.value;
    if (selectedSaro) {
        generateNTCANumber();
        const ntcaNo = document.getElementById('ntca_number').value;
        const quarter = document.getElementById('quarter').value;
        if (ntcaNo && quarter) {
            fetchNTCABalance(ntcaNo, quarter);
        }
    }
});