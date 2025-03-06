document.addEventListener('DOMContentLoaded', function() {
    initializeStatusTracking();
    toggleBudgetSpent(); // Initial check on page load

    const procurementUpdateUrl = "/api/procurement/update";

    document.getElementById('saveChanges').addEventListener('click', function(e) {
        e.preventDefault();

        let formData = new FormData(document.getElementById('procurementForm'));

        let activeStage = 1;
        for (let i = 6; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }
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

            const isCompleted = dateSubmitted?.value && dateReturned?.value;
            if (!isCompleted && activeStage === 1) activeStage = i;

            if (!isCompleted) {
                allCompleted = false;
            }

            if (dateSubmitted && dateReturned) {
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

                dateSubmitted.addEventListener('change', refreshStatus);
                dateReturned.addEventListener('change', refreshStatus);
            }
        }

        if (allCompleted) {
            lockEntireForm();
            hideAllIndicators();
        }

        // Check if budget is already locked and fully disable if so
        const budgetSpentField = document.getElementById('budgetSpent');
        if (budgetSpentField && budgetSpentField.dataset.locked === 'true') {
            budgetSpentField.setAttribute('readonly', 'true');
        }
    }

    function refreshStatus() {
        let activeStage = 1;

        for (let i = 6; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }

        for (let i = 1; i <= 6; i++) {
            const isCompleted = document.getElementById(`dateSubmitted${i}`).value && document.getElementById(`dateReturned${i}`).value;

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
        budgetSpentField.setAttribute('readonly', 'true');
    }

    function toggleBudgetSpent() {
        let allCompleted = true;

        for (let i = 1; i <= 6; i++) {
            const submitted = document.getElementById(`dateSubmitted${i}`).value;
            const received = document.getElementById(`dateReturned${i}`).value;

            if (!(submitted && received)) {
                allCompleted = false;
                break;
            }
        }

        const budgetSpentField = document.getElementById('budgetSpent');

        if (budgetSpentField.dataset.locked === 'true') {
            budgetSpentField.setAttribute('readonly', 'true');
        } else if (allCompleted) {
            budgetSpentField.removeAttribute('readonly');
        } else {
            budgetSpentField.setAttribute('readonly', 'true');
        }
    }
});
