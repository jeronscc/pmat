document.addEventListener('DOMContentLoaded', function() {
    initializeStatusTracking();
    toggleBudgetSpent(); // Initial check on page load

    const procurementUpdateUrl = "/api/procurement/update"; // Your existing API route

    document.getElementById('saveChanges').addEventListener('click', function(e) {
        e.preventDefault();

        let formData = new FormData(document.getElementById('procurementForm'));

        let activeStage = getActiveStage();
        formData.append('activeStage', activeStage);

        fetch(procurementUpdateUrl, {
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

    function initializeStatusTracking() {
        let activeStage = 1;
        let allCompleted = true;

        for (let i = 1; i <= 6; i++) {
            const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
            const dateReturned = document.getElementById(`dateReturned${i}`);

            const isSubmitted = !!dateSubmitted?.value;
            const isReturned = !!dateReturned?.value;
            const isCompleted = isSubmitted && isReturned;

            if (isCompleted) {
                lockRow(i);
                hideIndicator(i);
            } else {
                allCompleted = false;
                if (activeStage === 1 && !isSubmitted) {
                    activeStage = i;
                    unlockRow(i);
                    showIndicator(i);
                } else {
                    lockRow(i);
                    hideIndicator(i);
                }
            }

            if (dateSubmitted && dateReturned) {
                dateSubmitted.addEventListener('change', refreshStatus);
                dateReturned.addEventListener('change', refreshStatus);
            }
        }

        if (allCompleted) {
            lockEntireForm();
            hideAllIndicators();
        }
    }

    function refreshStatus() {
        let activeStage = getActiveStage();

        for (let i = 1; i <= 6; i++) {
            const isSubmitted = !!document.getElementById(`dateSubmitted${i}`).value;
            const isReturned = !!document.getElementById(`dateReturned${i}`).value;
            const isCompleted = isSubmitted && isReturned;

            if (isCompleted) {
                lockRow(i);
                hideIndicator(i);
            } else if (i === activeStage) {
                unlockRow(i);
                showIndicator(i);
            } else {
                lockRow(i);
                hideIndicator(i);
            }
        }

        toggleBudgetSpent();
    }

    function getActiveStage() {
        for (let i = 1; i <= 6; i++) {
            if (!document.getElementById(`dateSubmitted${i}`).value) {
                return i;
            }
        }
        return 7;  // If all are filled, we treat stage 7 as "done".
    }

    function lockRow(row) {
        document.getElementById(`dateSubmitted${row}`).setAttribute('readonly', 'true');
        document.getElementById(`dateReturned${row}`).setAttribute('readonly', 'true');
    }

    function unlockRow(row) {
        document.getElementById(`dateSubmitted${row}`).removeAttribute('readonly');
        document.getElementById(`dateReturned${row}`).removeAttribute('readonly');
    }

    function showIndicator(row) {
        const indicator = document.getElementById(`indicator${row}`);
        if (indicator) {
            indicator.style.backgroundColor = "green";
            indicator.textContent = " ";
        }
    }

    function hideIndicator(row) {
        const indicator = document.getElementById(`indicator${row}`);
        if (indicator) {
            indicator.style.backgroundColor = "transparent";
            indicator.textContent = "";
        }
    }

    function hideAllIndicators() {
        for (let i = 1; i <= 6; i++) {
            hideIndicator(i);
        }
    }

    function lockEntireForm() {
        for (let i = 1; i <= 6; i++) {
            lockRow(i);
        }

        const budgetSpentField = document.getElementById('budgetSpent');
        if (budgetSpentField) {
            budgetSpentField.setAttribute('readonly', 'true');
        }
    }

    function toggleBudgetSpent() {
        let allCompleted = true;

        for (let i = 1; i <= 6; i++) {
            const submitted = document.getElementById(`dateSubmitted${i}`).value;
            const returned = document.getElementById(`dateReturned${i}`).value;

            if (!(submitted && returned)) {
                allCompleted = false;
                break;
            }
        }

        const budgetSpentField = document.getElementById('budgetSpent');
        if (budgetSpentField) {
            if (budgetSpentField.value.trim() !== '') {
                budgetSpentField.setAttribute('readonly', 'true');
            } else if (allCompleted) {
                budgetSpentField.removeAttribute('readonly');
            } else {
                budgetSpentField.setAttribute('readonly', 'true');
            }
        }
    }
});
