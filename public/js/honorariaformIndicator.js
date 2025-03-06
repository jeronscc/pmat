document.addEventListener('DOMContentLoaded', function () {
    initializeStatusTracking();
    toggleBudgetSpent();  // Initial check on page load to lock/unlock budget spent

    const honorariaUpdateUrl = "/api/honoraria/update";  // Use the API route for procurement update

    // Save changes functionality
    document.getElementById('saveChanges').addEventListener('click', function (e) {
        e.preventDefault();

        let formData = new FormData(document.getElementById('honorariaForm'));

        // Check if both dateSubmitted and dateReturned are filled
        const dateSubmitted = document.getElementById('dateSubmitted').value;
        const dateReturned = document.getElementById('dateReturned').value;
        let activeStage = (dateSubmitted && dateReturned) ? 1 : 0; // Send activeStage as part of the data

        formData.append('activeStage', activeStage);  // Append the activeStage to form data

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
                location.reload();  // Refresh the page after successful update
            })
            .catch(error => {
                console.error('Error saving data:', error);
                alert('Error saving data. Check console for details.');
            });
    });

    function initializeStatusTracking() {
        const dateSubmitted = document.getElementById('dateSubmitted');
        const dateReturned = document.getElementById('dateReturned');
        const indicator = document.getElementById('indicator');

        function updateIndicator(dateSubmitted, dateReturned, indicator) {
            const submitted = dateSubmitted.value;
            const returned = dateReturned.value;

            if (submitted && returned) {
                indicator.style.backgroundColor = "green";  // Green if both dates are filled
                indicator.textContent = "";
            } else if (submitted && !returned) {
                indicator.style.backgroundColor = "yellow";  // Yellow if only submitted date is filled
                indicator.textContent = "";
            } else {
                indicator.style.backgroundColor = "transparent";  // Transparent if no date is filled
                indicator.textContent = "";
            }
        }

        // Initially update the indicator based on dates
        updateIndicator(dateSubmitted, dateReturned, indicator);

        // Add event listeners to both date fields
        dateSubmitted.addEventListener("change", function () {
            updateIndicator(dateSubmitted, dateReturned, indicator);
            toggleBudgetSpent();
        });

        dateReturned.addEventListener("change", function () {
            updateIndicator(dateSubmitted, dateReturned, indicator);
            toggleBudgetSpent();
        });
    }

    // Restrict Budget Spent Field until both dateSubmitted and dateReturned are filled
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
});
