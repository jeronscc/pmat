document.addEventListener('DOMContentLoaded', function () {
    initializeStatusTracking();
    toggleBudgetSpent();  // Initial check on page load to lock/unlock budget spent
    checkCompletionAndLock(); // Check and lock on initial load

    const honorariaUpdateUrl = "/api/honoraria/update";  // Use the API route for procurement update

    document.getElementById('saveChanges').addEventListener('click', function (e) {
        e.preventDefault();

        const dateSubmitted = document.getElementById('dateSubmitted').value;
        const dateReturned = document.getElementById('dateReturned').value;

        // Check if date fields are empty
        if (!dateSubmitted || !dateReturned) {
            alert('Error: Both Date Submitted and Date Returned must be filled.');
            return;
        }

        let formData = new FormData(document.getElementById('honorariaForm'));
        let activeStage = (dateSubmitted && dateReturned) ? 1 : 0;

        formData.append('activeStage', activeStage);

        fetch(honorariaUpdateUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
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
                indicator.style.backgroundColor = "yellow";
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
            checkCompletionAndLock();
        });

        dateReturned.addEventListener("change", function () {
            updateIndicator(dateSubmitted, dateReturned, indicator);
            toggleBudgetSpent();
            checkCompletionAndLock();
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

    // Lock fields if form is completed
    function checkCompletionAndLock() {
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
    }

    // Initial check on load
    checkCompletionAndLock();
});
