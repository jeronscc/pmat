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
                indicator.style.backgroundColor = "grey";
                indicator.textContent = "Pending";
            } else if (dateSubmitted && dateReturned) {
                indicator.style.backgroundColor = "yellow";
                indicator.textContent = "Ongoing";
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

                    for (let j = 1; j <= 6; j++) {
                        updateIndicator(`dateSubmitted${j}`, `dateReturned${j}`, `indicator${j}`, j === newActiveStage);
                    }
                });

                dateReturned.addEventListener("change", function() {
                    updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);
                });
            }
        }
    }
});
