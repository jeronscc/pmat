document.addEventListener('DOMContentLoaded', function() {
    initializeStatusTracking();
    toggleBudgetSpent();  // Initial check on page load to lock/unlock budget spent

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
        function updateIndicator(dateSubmittedId, dateReturnedId, indicatorId, isActive) {
            const dateSubmitted = document.getElementById(dateSubmittedId).value;
            const dateReturned = document.getElementById(dateReturnedId).value;
            const indicator = document.getElementById(indicatorId);

            if (!isActive) {
                indicator.style.backgroundColor = "transparent";
                indicator.textContent = "";
                return;
            }

            if (dateSubmitted && !dateReturned) {
                indicator.style.backgroundColor = "green";
                indicator.textContent = " ";
            } else if (dateSubmitted && dateReturned) {
                indicator.style.backgroundColor = "green";
                indicator.textContent = " ";
            } else {
                indicator.style.backgroundColor = "transparent";
                indicator.textContent = "";
            }
        }

        let activeStage = 1;
        let allCompleted = true;

        for (let i = 1; i <= 6; i++) {
            const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
            const dateReturned = document.getElementById(`dateReturned${i}`);

            if (dateSubmitted && dateReturned) {
                const isCompleted = dateSubmitted.value && dateReturned.value;
                
                if (!isCompleted) {
                    allCompleted = false;
                    if (activeStage === 1) activeStage = i;  // Set first incomplete stage as active
                }

                updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);

                if (isCompleted) {
                    dateSubmitted.setAttribute('readonly', 'true');
                    dateReturned.setAttribute('readonly', 'true');
                } else if (i === activeStage) {
                    dateSubmitted.removeAttribute('readonly');
                    dateReturned.removeAttribute('readonly');
                } else {
                    dateSubmitted.setAttribute('readonly', 'true');
                    dateReturned.setAttribute('readonly', 'true');
                }

                dateSubmitted.addEventListener("change", function() {
                    refreshStatus();
                });

                dateReturned.addEventListener("change", function() {
                    refreshStatus();
                });
            }
        }

        if (allCompleted) {
            lockEntireForm();
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
                document.getElementById(`dateSubmitted${i}`).setAttribute('readonly', 'true');
                document.getElementById(`dateReturned${i}`).setAttribute('readonly', 'true');
            } else if (i === activeStage) {
                document.getElementById(`dateSubmitted${i}`).removeAttribute('readonly');
                document.getElementById(`dateReturned${i}`).removeAttribute('readonly');
            } else {
                document.getElementById(`dateSubmitted${i}`).setAttribute('readonly', 'true');
                document.getElementById(`dateReturned${i}`).setAttribute('readonly', 'true');
            }
        }

        toggleBudgetSpent();
    }

    function lockEntireForm() {
        for (let i = 1; i <= 6; i++) {
            document.getElementById(`dateSubmitted${i}`).setAttribute('readonly', 'true');
            document.getElementById(`dateReturned${i}`).setAttribute('readonly', 'true');
        }

        document.getElementById('budgetSpent').removeAttribute('readonly');  // Allow budget spent editing if all steps complete
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
        if (allCompleted) {
            budgetSpentField.removeAttribute('readonly');
        } else {
            budgetSpentField.setAttribute('readonly', 'true');
        }
    }
});
