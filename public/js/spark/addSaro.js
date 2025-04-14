document.getElementById('saveSaro').addEventListener('click', function () {
    const saroNumber = document.getElementById('saro_number').value;
    const saroDesc = document.getElementById('saroDesc').value;
    const saroYear = document.getElementById('saro_year').value;
    const saroBudget = document.getElementById('saro_budget').value;
    const saroDateReleased = document.getElementById('saro_date_released').value;

    const ntcaNumber = document.getElementById('ntca_number').value;
    const ntcaBudget = document.getElementById('budget').value;
    const ntcaQuarter = document.getElementById('quarter').value;
    const saroSelect = document.getElementById('saro_select').value;
    const ntcaDateReleased = document.getElementById('ntca_date_released').value;

    if (document.getElementById('ntcaFields').classList.contains('d-none')) {
        // Save SARO
        if (!saroNumber || !saroDesc || !saroYear || !saroBudget || !saroDateReleased) {
            alert('All SARO fields must be filled out.');
            return;
        }

        fetch('/api/spark/add-saro-spark', {
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
                date_released: saroDateReleased,
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
        if (!ntcaNumber || !ntcaBudget || !ntcaQuarter || !saroSelect || !ntcaDateReleased) {
            alert('All NTCA fields must be filled out.');
            return;
        }

        fetch('/api/spark/save-ntca', {
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
                date_released: ntcaDateReleased,
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
    }
});

document.getElementById('categorySelect').addEventListener('change', function () {
    const category = this.value;

    // Show or hide fields based on the selected category
    if (category === 'NTCA') {
        document.getElementById('ntcaFields').classList.remove('d-none');
        document.getElementById('saroFields').classList.add('d-none');
        populateSARODropdown();
    } else if (category === 'SARO') {
        document.getElementById('saroFields').classList.remove('d-none');
        document.getElementById('ntcaFields').classList.add('d-none');
    }
});

// Function to populate the SARO dropdown dynamically (fetch from the server)
function populateSARODropdown() {
    const saroSelect = document.getElementById('saro_select');
    saroSelect.innerHTML = ''; // Clear existing options

    fetch('/api/spark/fetch-saro-spark')
        .then(response => response.json())
        .then(data => {
            data.forEach(saro => {
                const option = document.createElement('option');
                option.value = saro.saro_no;
                option.textContent = `${saro.saro_no} (${saro.year})`;
                saroSelect.appendChild(option);
            });
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
        const ntcaNo = document.getElementById('ntca_number').value;
        const quarter = document.getElementById('quarter').value;
        if (ntcaNo && quarter) {
            fetchNTCABalance(ntcaNo, quarter);
        }
    }
});