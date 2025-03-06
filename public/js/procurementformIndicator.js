document.addEventListener('DOMContentLoaded', function() {
    // Initialize the status tracking system
    initializeStatusTracking();

    // Get the API endpoint URL
    const procurementUpdateUrl = "/api/procurement/update";  // Use the API route for procurement update

    document.getElementById('saveChanges').addEventListener('click', function(e) {
        e.preventDefault();

        // Serialize form data into an array of name/value pairs
        let formData = new FormData(document.getElementById('procurementForm'));

        // Add a flag to indicate which stage is active
        let activeStage = 1;
        for (let i = 6; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }
        // Append the active stage to formData
        formData.append('activeStage', activeStage);
        // Send form data using Fetch API
        fetch(procurementUpdateUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);  // Show success message
            // Reload the page to reflect updated data
            location.reload();
        })
        .catch(error => {
            console.error('Error saving data:', error);
            alert('Error saving data. Check console for details.');
        });
    });
    // Function to initialize status tracking
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
                indicator.style.backgroundColor = "green"; // Assuming green indicates in-progress
                indicator.textContent = " ";
            } else if (dateSubmitted && dateReturned) {
                indicator.style.backgroundColor = "green"; // Assuming green indicates completed
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
        // Loop through all stages to add event listeners for updating indicators
        for (let i = 1; i <= 6; i++) {
            const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
            const dateReturned = document.getElementById(`dateReturned${i}`);
            
            if (dateSubmitted && dateReturned) {
                updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);

                // Event listener for changes on dateSubmitted
                dateSubmitted.addEventListener("change", function() {
                    let newActiveStage = 1;
                    for (let j = 6; j >= 1; j--) {
                        if (document.getElementById(`dateSubmitted${j}`).value) {
                            newActiveStage = j;
                            break;
                        }
                    }

                    // Update the indicator for all stages based on the active stage
                    for (let j = 1; j <= 6; j++) {
                        updateIndicator(`dateSubmitted${j}`, `dateReturned${j}`, `indicator${j}`, j === newActiveStage);
                    }
                });

                // Event listener for changes on dateReturned
                dateReturned.addEventListener("change", function() {
                    updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);
                });
            }
        }
    }

    // Event listeners to enable/disable future stages based on the current stage's returned date
    function toggleStageFields(stageNumber) {
        const dateReturned = document.getElementById(`dateReturned${stageNumber}`);
        const dateSubmittedNext = document.getElementById(`dateSubmitted${stageNumber + 1}`);
        const dateReturnedNext = document.getElementById(`dateReturned${stageNumber + 1}`);
        
        if (dateReturned.value) {
            // Enable next stage if the current stage's dateReturned is filled
            dateSubmittedNext.removeAttribute('readonly');
            dateReturnedNext.removeAttribute('readonly');
        } else {
            // Make the next stage readonly if the current stage's dateReturned is empty
            dateSubmittedNext.setAttribute('readonly', 'true');
            dateReturnedNext.setAttribute('readonly', 'true');
        }
    }
    // Event listeners for each dateReturned field
    for (let i = 1; i <= 5; i++) {
        const dateReturnedField = document.getElementById(`dateReturned${i}`);
        dateReturnedField.addEventListener('change', function() {
            toggleStageFields(i);
        });

        // Initially disable dateSubmitted and dateReturned fields after stage 1
        if (!document.getElementById(`dateReturned${i}`).value) {
            document.getElementById(`dateSubmitted${i + 1}`).setAttribute('readonly', 'true');
            document.getElementById(`dateReturned${i + 1}`).setAttribute('readonly', 'true');
        }
    }

    // Ensure correct readonly status on page load
    if (!document.getElementById('dateReturned1').value) {
        document.getElementById('dateSubmitted2').setAttribute('readonly', 'true');
        document.getElementById('dateReturned2').setAttribute('readonly', 'true');
    }

    // Add any other initial readonly states
    // Example: Initially disable the last form fields until previous stages are completed
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
});
