document.addEventListener('DOMContentLoaded', function () {
    initializeStatusTracking();
    toggleBudgetSpent();  // Initial check to lock/unlock budgetSpent
    checkAndLockFields(); // Initial lock check on page load

    const honorariaUpdateUrl = "/api/honoraria/update";  // Use the correct API route

    document.getElementById('saveChanges').addEventListener('click', function (e) {
        e.preventDefault();

        const form = document.getElementById('honorariaForm');
        const formData = new FormData(form);

        const dateSubmitted = document.getElementById('dateSubmitted').value;

        // Validate required fields
        if (!dateSubmitted) {
            alert('Error: Date Submitted must be filled.');
            return;
        }

        fetch(honorariaUpdateUrl, {
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
                disableSaveButton();  // Disable the save button after success
                window.location.href = '/homepage-ilcdb' // Refresh to reflect changes
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving data:', error);
            alert('Error saving data. Check console for details.');
        });
    });

    document.getElementById('cancelChanges').addEventListener('click', function(e) {
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
