document.addEventListener('DOMContentLoaded', function () {
    const ntcaSelect = document.getElementById('ntca-number');
    const saroNo = document.getElementById('saro-no-value').value;
    let selectedNtcaValue = ''; // Placeholder for the pre-saved NTCA value

    // Simulate an existing record value (e.g., from the backend, like $record->ntca_no)
    selectedNtcaValue = document.getElementById('selected-ntca-value').value; // Hidden input for existing value

    if (saroNo) {
        // Fetch NTCA options based on the saro_no
        fetch(`/api/ntca-by-saro?saro_no=${encodeURIComponent(saroNo)}`)
            .then(res => res.json())
            .then(data => {
                // Clear previous options and add the default one
                ntcaSelect.innerHTML = '<option value="" disabled>Select NTCA Number</option>';

                if (data.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No NTCA found';
                    ntcaSelect.appendChild(option);
                    return;
                }

                // Populate the dropdown with fetched NTCA numbers
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.ntca_no;
                    option.textContent = item.ntca_no;
                    ntcaSelect.appendChild(option);
                });

                // Set the dropdown to the pre-saved value (if any)
                if (selectedNtcaValue) {
                    ntcaSelect.value = selectedNtcaValue;
                } else {
                    // If there's no saved value, keep the placeholder as selected
                    ntcaSelect.value = '';
                }
            })
            .catch(err => console.error('Error fetching NTCA numbers:', err));
    }

    // Handle save changes
    document.getElementById('saveChanges').addEventListener('click', function (e) {
        e.preventDefault();

        const form = document.getElementById('otherexpenseForm');
        const formData = new FormData(form);
        const ntcaNumber = ntcaSelect.value;
        const dateSubmitted = document.getElementById('dateSubmitted').value;

        // Validate required fields
        if (!dateSubmitted) {
            alert('Error: Date Submitted must be filled.');
            return;
        }

        // Append selected NTCA number to the formData
        if (ntcaNumber) {
            formData.append('ntca_number', ntcaNumber);
        }

        fetch('/api/otherexpense/update', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);

                    // Update the selected value after saving
                    selectedNtcaValue = data.saved_ntca_number;

                    // Reflect the saved value in the dropdown
                    if (selectedNtcaValue) {
                        ntcaSelect.value = selectedNtcaValue;
                    } else {
                        // If no value was saved, reset to default placeholder
                        ntcaSelect.value = '';
                    }

                    // Optionally redirect or refresh
                    window.location.href = '/homepage-ilcdb';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error saving data:', error);
                alert('Error saving data. Check console for details.');
            });
    });

    initializeStatusTracking();
    
    document.getElementById('cancelChanges').addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = '/homepage-ilcdb';
    });

    function initializeStatusTracking() {
        const dateSubmitted = document.getElementById('dateSubmitted');
        const dateReturned = document.getElementById('dateReturned');
        const indicator = document.getElementById('indicator');

        function updateIndicator(dateSubmitted, dateReturned, indicator) {
            const submitted = dateSubmitted.value;
            const returned = dateReturned.value;

            if (submitted && returned) {
                indicator.style.backgroundColor = "green";
                indicator.textContent = "";
            } else if (submitted && !returned) {
                indicator.style.backgroundColor = "green";
                indicator.textContent = "";
            } else {
                indicator.style.backgroundColor = "transparent";
                indicator.textContent = "";
            }
        }

        updateIndicator(dateSubmitted, dateReturned, indicator);

        dateSubmitted.addEventListener("change", function () {
            updateIndicator(dateSubmitted, dateReturned, indicator);
            toggleBudgetSpent();
            checkAndLockFields();  // Check and lock after date change
        });

        dateReturned.addEventListener("change", function () {
            updateIndicator(dateSubmitted, dateReturned, indicator);
            toggleBudgetSpent();
            checkAndLockFields();  // Check and lock after date change
        });
    }

    function toggleBudgetSpent() {
        const dateSubmitted = document.getElementById('dateSubmitted').value;
        const dateReturned = document.getElementById('dateReturned').value;

        const budgetSpentField = document.getElementById('budgetSpent');
        if (dateSubmitted && dateReturned) {
            budgetSpentField.removeAttribute('readonly');
        } else {
            budgetSpentField.setAttribute('readonly', 'true');
        }
    }

    // Check if the form is completed and lock all fields if needed
    function checkAndLockFields() {
        const dateSubmitted = document.getElementById('dateSubmitted').value;
        const dateReturned = document.getElementById('dateReturned').value;
        const budgetSpent = document.getElementById('budgetSpent').value;

        if (dateSubmitted && dateReturned && budgetSpent) {
            lockAllFields();
        }
    }

    function lockAllFields() {
        document.getElementById('dateSubmitted').setAttribute('readonly', 'true');
        document.getElementById('dateReturned').setAttribute('readonly', 'true');
        document.getElementById('budgetSpent').setAttribute('readonly', 'true');

        // Optionally disable the save button
        const saveButton = document.getElementById('saveChanges');
        if (saveButton) saveButton.setAttribute('disabled', 'true');
    }

    // Disable the save button after data is saved
    function disableSaveButton() {
        const saveButton = document.getElementById('saveChanges');
        if (saveButton) {
            saveButton.setAttribute('disabled', 'true');
        }
    }

    // Initial lock check when page loads
    checkAndLockFields();
});
