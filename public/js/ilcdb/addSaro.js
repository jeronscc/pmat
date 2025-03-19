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
                    const addSaroModal = bootstrap.Modal.getInstance(document.getElementById('addSaroModal'));
                    addSaroModal.hide();
                    document.getElementById('saroForm').reset();
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
    }
});

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

    const currentMonthYear = new Date().toLocaleDateString('en-GB', { year: '2-digit', month: '2-digit' }).replace('/', '');
    const lastDigits = selectedSaro.slice(-6); // Extract last 5 digits of SARO number
    const ntcaNumber = `NTCA-${lastDigits}-${currentMonthYear}`;
    document.getElementById('ntca_number').value = ntcaNumber;
}
// Function to populate the SARO dropdown dynamically (fetch from the server)
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
        })
        .catch(error => {
            console.error('Error fetching SAROs:', error);
        });
    // Trigger NTCA number generation when a SARO is selected
    saroSelect.addEventListener('change', generateNTCANumber);
}
document.getElementById('addSaroModal').addEventListener('shown.bs.modal', function () {
    populateSARODropdown();
});
// Call populateSARODropdown when the modal is opened
document.getElementById('addSaroModal').addEventListener('shown.bs.modal', populateSARODropdown);