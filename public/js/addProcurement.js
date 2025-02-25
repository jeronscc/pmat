document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addProcurement').addEventListener('click', function() {
        const category = document.getElementById('category').value;
        const prNumber = document.getElementById('pr-number').value;
        const saroNumber = document.getElementById('saro-number').value;
        const prYear = document.getElementById('pr-year').value;
        const activity = document.getElementById('activity').value;
        const description = document.getElementById('description').value;

        if (!category || !prNumber || !saroNumber || !prYear || !activity || !description) {
            alert('All fields must be filled out.');
            return;
        }

        fetch('/api/add-procurement-ilcdb', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                category: category,
                pr_number: prNumber,
                saro_number: saroNumber,
                pr_year: prYear,
                activity: activity,
                description: description
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => {
                    console.error('Validation/Error:', errData);
                    alert('Error: ' + JSON.stringify(errData));
                    throw new Error('Request failed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.message === 'Procurement added successfully') {
                alert('New Procurement added successfully');
                const procurementModal = bootstrap.Modal.getInstance(document.getElementById("procurementModal"));
                procurementModal.hide();

                // Redirect based on category
                if (category === 'SVP') {
                    window.location.href = '/procurementform';
                } else if (category === 'Honoraria') {
                    window.location.href = '/honorariaform';
                } else if (category === 'Other expense') {
                    window.location.href = '/otherexpenseform';
                }
            } else {
                alert('Failed to add procurement');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding procurement');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Populate SARO options for the current year
    const currentYear = new Date().getFullYear();
    fetch(`/api/fetch-saro-ilcdb?year=${currentYear}`)
        .then(response => response.json())
        .then(data => {
            const saroSelect = document.getElementById('saro-number');
            saroSelect.innerHTML = '<option value="" disabled selected>Select SARO Number</option>'; // Clear existing options

            if (data.length > 0) {
                data.forEach(saro => {
                    const option = document.createElement('option');
                    option.value = saro.saro_no;
                    option.textContent = saro.saro_no;
                    saroSelect.appendChild(option);
                });
            } else {
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = 'No SARO records found for the current year';
                saroSelect.appendChild(emptyOption);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
});