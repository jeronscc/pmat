document.getElementById('saveSaro').addEventListener('click', function () {
    const category = document.getElementById('categorySelect').value;

    if (category === 'NTCA') {
        const ntcaNumber = document.getElementById('ntca_number').value;
        const budget = document.getElementById('budget').value;
        const quarter = document.getElementById('quarter').value;
        const saroNo = document.getElementById('saro_select').value;

        if (!ntcaNumber || !budget || !quarter || !saroNo) {
            alert('All fields must be filled out.');
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
                budget: budget,
                quarter: quarter,
                saro_no: saroNo,
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
    } else if (category === 'SARO') {
        const saroNumber = document.getElementById('saro_number').value;
        const budget = document.getElementById('saro_budget').value;
        const year = document.getElementById('saro_year').value;
        const desc = document.getElementById('saro_desc').value;

        if (!saroNumber || !budget || !year || !desc) {
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
                budget: budget,
                saro_year: year,
                saro_desc: desc,
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
    const saroNumber = document.getElementById('saro_number').value; // Get SARO number
    const currentMonthYear = new Date().toLocaleDateString('en-GB', { year: '2-digit', month: '2-digit' }).replace('/', '');
    const lastDigits = saroNumber.slice(-5); // Extract last 5 digits of SARO number
    const ntcaNumber = `NTCA-${lastDigits}-${currentMonthYear}`;
    document.getElementById('ntca_number').value = ntcaNumber;
}

// Function to populate the SARO dropdown dynamically (fetch from the server)
function populateSARODropdown() {
    const saroSelect = document.getElementById('ntca_saro_select');
    saroSelect.innerHTML = ''; // Clear existing options

    fetch('/api/saros') // Replace with actual endpoint to fetch SAROs
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.saros.forEach(saro => {
                    const option = document.createElement('option');
                    option.value = saro.id;
                    option.textContent = saro.number;
                    saroSelect.appendChild(option);
                });
            } else {
                console.error('Failed to fetch SAROs:', data.message);
            }
        })
        .catch(error => console.error('Error fetching SAROs:', error));
}