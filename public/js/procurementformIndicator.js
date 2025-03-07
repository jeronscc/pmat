document.addEventListener('DOMContentLoaded', function() {
    initializeStatusTracking();
    toggleBudgetSpent();  // Initial check on page load to lock/unlock budget spent

    const procurementUpdateUrl = "/api/procurement/update";  // Use the API route for procurement update

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

    document.getElementById('cancelChanges').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/homepage-ilcdb';
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
        for (let i = 6; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }

        lockPreviousStages(activeStage);
        checkIfFormCompleted();

        for (let i = 1; i <= 6; i++) {
            const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
            const dateReturned = document.getElementById(`dateReturned${i}`);

            if (dateSubmitted && dateReturned) {
                updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);

                dateSubmitted.addEventListener("change", function() {
                    let newActiveStage = 1;
                    for (let j = 6; j >= 1; j--) {
                        if (document.getElementById(`dateSubmitted${j}`).value) {
                            newActiveStage = j;
                            break;
                        }
                    }

                    lockPreviousStages(newActiveStage); // Added
                    checkIfFormCompleted(); // Added

                    for (let j = 1; j <= 6; j++) {
                        updateIndicator(`dateSubmitted${j}`, `dateReturned${j}`, `indicator${j}`, j === newActiveStage);
                    }
                    toggleBudgetSpent();
                });

                dateReturned.addEventListener("change", function() {
                    updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);
                    toggleBudgetSpent();
                });
            }
        }
    }

    function toggleStageFields(stageNumber) {
        const dateReturned = document.getElementById(`dateReturned${stageNumber}`);
        const dateSubmittedNext = document.getElementById(`dateSubmitted${stageNumber + 1}`);
        const dateReturnedNext = document.getElementById(`dateReturned${stageNumber + 1}`);

        if (dateReturned.value) {
            dateSubmittedNext.removeAttribute('readonly');
            dateReturnedNext.removeAttribute('readonly');
        } else {
            dateSubmittedNext.setAttribute('readonly', 'true');
            dateReturnedNext.setAttribute('readonly', 'true');
        }
    }

    for (let i = 1; i <= 5; i++) {
        const dateReturnedField = document.getElementById(`dateReturned${i}`);
        dateReturnedField.addEventListener('change', function() {
            toggleStageFields(i);
        });

        if (!document.getElementById(`dateReturned${i}`).value) {
            document.getElementById(`dateSubmitted${i + 1}`).setAttribute('readonly', 'true');
            document.getElementById(`dateReturned${i + 1}`).setAttribute('readonly', 'true');
        }
    }

    if (!document.getElementById('dateReturned1').value) {
        document.getElementById('dateSubmitted2').setAttribute('readonly', 'true');
        document.getElementById('dateReturned2').setAttribute('readonly', 'true');
    }

    if (!document.getElementById('dateReturned2').value) {
        document.getElementById('dateSubmitted3').setAttribute('readonly', 'true');
        document.getElementById('dateReturned3').setAttribute('readonly', 'true');
    }

    if (!document.getElementById('dateReturned3').value) {
        document.getElementById('dateSubmitted4').setAttribute('readonly', 'true');
        document.getElementById('dateReturned4').setAttribute('readonly', 'true');
    }

    if (!document.getElementById('dateReturned4').value) {
        document.getElementById('dateSubmitted5').setAttribute('readonly', 'true');
        document.getElementById('dateReturned5').setAttribute('readonly', 'true');
    }

    if (!document.getElementById('dateReturned5').value) {
        document.getElementById('dateSubmitted6').setAttribute('readonly', 'true');
        document.getElementById('dateReturned6').setAttribute('readonly', 'true');
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

    function lockPreviousStages(activeStage) {
        for (let i = 1; i < activeStage; i++) {
            document.getElementById(`dateSubmitted${i}`).setAttribute('readonly', 'true');
            document.getElementById(`dateReturned${i}`).setAttribute('readonly', 'true');
        }
    }

    function checkIfFormCompleted() {
        let allCompleted = true;
        for (let i = 1; i <= 6; i++) {
            const submitted = document.getElementById(`dateSubmitted${i}`).value;
            const returned = document.getElementById(`dateReturned${i}`).value;

            if (!(submitted && returned)) {
                allCompleted = false;
                break;
            }
        }

        if (allCompleted) {
            lockAllRows();
            document.getElementById('budgetSpent').setAttribute('readonly', 'true');
        }
    }

    function lockAllRows() {
        for (let i = 1; i <= 6; i++) {
            document.getElementById(`dateSubmitted${i}`).setAttribute('readonly', 'true');
            document.getElementById(`dateReturned${i}`).setAttribute('readonly', 'true');
        }
    }
});
