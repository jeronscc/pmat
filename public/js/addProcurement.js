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
            // Redirect with both pr_number and activity in the query string
            if (category === 'SVP') {
                window.location.href = '/procurementform?pr_number=' + encodeURIComponent(prNumber) + '&activity=' + encodeURIComponent(activity);
            } else if (category === 'Honoraria') {
                window.location.href = '/honorariaform?pr_number=' + encodeURIComponent(prNumber) + '&activity=' + encodeURIComponent(activity);
            } else if (category === 'Other expense') {
                window.location.href = '/otherexpenseform?pr_number=' + encodeURIComponent(prNumber) + '&activity=' + encodeURIComponent(activity);
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

// OPTIONS FOR EXISTING SARO IN PROC MODAL
document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('pr-year');
    const saroSelect = document.getElementById('saro-number');
    
    // Function to fetch SARO data based on selected year
    function fetchSaroByYear(year) {
        fetch(`/api/fetch-saro-ilcdb?year=${year}`)
            .then(response => response.json())
            .then(data => {
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
                    emptyOption.textContent = 'No SARO records found for the selected year';
                    saroSelect.appendChild(emptyOption);
                }
            })
            .catch(error => console.error('Error fetching SARO data:', error));
    }

    // Listen for the year selection change
    yearSelect.addEventListener('change', function() {
        const selectedYear = this.value;
        if (selectedYear) {
            fetchSaroByYear(selectedYear); // Fetch SARO numbers for the selected year
        }
    });
});

// edit procurement redirection
document.addEventListener('DOMContentLoaded', function() {
    // Attach event listener to the modal's Edit button
    document.querySelector('#procurementDetailsModal .btn-primary').addEventListener('click', function() {
        // Get values from the modal's spans
        var prNumber = document.getElementById('modalProcurementNo').textContent.trim();
        var activity = document.getElementById('modalActivity').textContent.trim();

        // Redirect to the procurement form page with these values in the query string
        window.location.href = '/procurementform?pr_number=' + encodeURIComponent(prNumber) +
                                 '&activity=' + encodeURIComponent(activity);
    });
});