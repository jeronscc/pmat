document.addEventListener('DOMContentLoaded', function () {
    const procurementIdElement = document.getElementById('procurementId');

    if (!procurementIdElement) {
        console.error("Procurement ID element not found.");
        return;
    }

    const saroNo = document.getElementById('saro-no-value').value;
    let selectedNtcaValue = ''; // Placeholder for the pre-saved NTCA value

    selectedNtcaValue = document.getElementById('selected-ntca-value').value; // Hidden input for existing value

    const procurementId = procurementIdElement.value;
    const procurementUpdateUrl = "/api/honoraria/update"; // Use the API route for procurement update

    // Initialize the status tracking after checking file uploads
    initializeFileUploadStatuses(procurementId);

    // Handle the ntca_no field
    const ntcaNoField = document.getElementById('ntca-number');
    if (ntcaNoField) {
        // Disable the field if it already has a value from the hidden input (pre-saved value)
        if (selectedNtcaValue) {
            ntcaNoField.addEventListener('mousedown', function (e) {
                e.preventDefault(); // Prevent the dropdown from opening
            });
        }
        // Fetch NTCA options based on procurementId (or any other parameter you wish)
        fetch(`/api/ntca-by-saro?saro_no=${encodeURIComponent(saroNo)}`)
            .then(res => res.json())
            .then(data => {
                // Clear previous options and add the default one
                ntcaNoField.innerHTML = '<option value="" disabled>Select NTCA Number</option>';

                if (data.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No NTCA found';
                    ntcaNoField.appendChild(option);
                    return;
                }

                // Populate the dropdown with fetched NTCA numbers
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.ntca_no;
                    option.textContent = item.ntca_no;
                    ntcaNoField.appendChild(option);
                });

                // Set the dropdown to the pre-saved value (if any)
                if (selectedNtcaValue) {
                    ntcaNoField.value = selectedNtcaValue;
                } else {
                    // If there's no saved value, keep the placeholder as selected
                    ntcaNoField.value = '';
                }
            })
            .catch(err => console.error('Error fetching NTCA numbers:', err));
    }

    // Handle the quarter dropdown
    const quarterField = document.getElementById('quarter');
    if (quarterField) {
        // Check if the dropdown already has a saved value
        const savedQuarter = quarterField.getAttribute('data-saved-value'); // Get the saved value from the data attribute
        if (savedQuarter) {
            quarterField.value = savedQuarter; // Set the saved value as the selected option

            // Make the dropdown read-only by preventing interaction
            quarterField.addEventListener('mousedown', function (e) {
                e.preventDefault(); // Prevent the dropdown from opening
            });
        }

    }

    // Lock ntca_no and quarter if date fields are incomplete
    function lockNtcaAndQuarter() {
        let allDatesCompleted = true;

        // Check if all date fields are completed
        for (let i = 1; i <= 2; i++) {
            const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
            const dateReturned = document.getElementById(`dateReturned${i}`);

            if (!dateSubmitted || !dateReturned) continue;

            if (!(dateSubmitted.value && dateReturned.value)) {
                allDatesCompleted = false;
                break;
            }
        }

        // Lock ntca_no and quarter if dates are not completed
        if (!allDatesCompleted) {
            if (ntcaNoField) {
                ntcaNoField.addEventListener('mousedown', function (e) {
                    e.preventDefault(); // Prevent the dropdown from opening
                });
            }
            if (quarterField) {
                quarterField.addEventListener('mousedown', function (e) {
                    e.preventDefault(); // Prevent the dropdown from opening
                });
            }
        } else {
            // Unlock ntca_no and quarter if all dates are completed
            if (ntcaNoField) {
                ntcaNoField.removeEventListener('mousedown', function (e) {
                    e.preventDefault(); // Prevent the dropdown from opening
                });
            }
            if (quarterField) {
                quarterField.removeEventListener('mousedown', function (e) {
                    e.preventDefault();
                });
            }
        }
    }

    // Initial lock check on page load
    lockNtcaAndQuarter();

    // Add event listeners to date fields to recheck lock status on change
    for (let i = 1; i <= 2; i++) {
        const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
        const dateReturned = document.getElementById(`dateReturned${i}`);

        if (dateSubmitted) {
            dateSubmitted.addEventListener('change', lockNtcaAndQuarter);
        }
        if (dateReturned) {
            dateReturned.addEventListener('change', lockNtcaAndQuarter);
        }
    }

    document.getElementById('saveChanges').addEventListener('click', function (e) {
        e.preventDefault();

        // Temporarily remove the 'disabled' attribute from all fields before submitting
        const allDateFields = document.querySelectorAll('input[id^="dateSubmitted"], input[id^="dateReturned"]');
        allDateFields.forEach(field => field.removeAttribute('disabled'));

        // Remove 'disabled' from the quarter dropdown before submitting
        if (quarterField) {
            quarterField.removeEventListener('mousedown', function (e) {
                e.preventDefault();
            });
        }

        const form = document.getElementById('honorariaForm');
        const formData = new FormData(form);
    
        const ntcaNumber = ntcaNoField.value; // Get the selected NTCA number
    
        // Ensure NTCA number is added to the formData before submission
        if (ntcaNumber) {
            formData.append('ntca_no', ntcaNumber); // Append the selected NTCA number
        }

        const dateSubmitted1 = document.getElementById('dateSubmitted1').value;
        if (!dateSubmitted1) {
            alert('No data inputted');
            return;
        }

        let activeStage = 1;
        for (let i = 2; i >= 1; i--) {
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

                // After saving, reapply lock logic
                lockNtcaAndQuarter();

                window.location.href = '/homepage-ilcdb'; // Refresh the page after saving
            })
            .catch(error => {
                console.error('Error saving data:', error);
                alert('Error saving data. Check console for details.');
            });
    });

    document.getElementById('cancelChanges').addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = '/homepage-ilcdb';
    });

    function initializeFileUploadStatuses(procurementId) {
        // Check all modals initially
        for (let i = 1; i <= 2; i++) {
            checkUploadStatus(procurementId, i, function (allFilesUploaded, missingFiles) {
                const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
                const dateReturned = document.getElementById(`dateReturned${i}`);

                if (!dateSubmitted || !dateReturned) return;

                // Only enable date fields if all files are uploaded
                if (allFilesUploaded) {
                    // Still respect the stage sequence logic
                    if (i === 1 || (i > 1 && document.getElementById(`dateReturned${i - 1}`).value)) {
                        dateSubmitted.removeAttribute('disabled');
                    } else {
                        dateSubmitted.setAttribute('disabled', 'true');
                    }
                } else {
                    // Disable fields if files are missing
                    dateSubmitted.setAttribute('disabled', 'true');
                    dateReturned.setAttribute('disabled', 'true');

                    // Add tooltip or info about missing files
                    dateSubmitted.title = `Missing files: ${missingFiles.join(', ')}`;
                }
            });
        }

        // Then initialize the status tracking
        setTimeout(() => {
            initializeStatusTracking();
            toggleBudgetSpent();  // Initial check on page load to lock/unlock budget spent
        }, 500); // Give time for the file status checks to complete
    }

    function checkUploadStatus(procurementId, modalNumber, callback) {
        fetch(`/api/uploadedHonorariaFilesCheck/${procurementId}?modal=${modalNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const fileStatus = data.fileStatus;
                    const allFilesUploaded = Object.values(fileStatus).every(status => status === true);

                    if (callback) {
                        callback(allFilesUploaded, data.missingFiles || []);
                    }
                } else {
                    console.error('Error fetching upload status:', data.message);
                    callback(false, []);
                }
            })
            .catch(error => {
                console.error('Error fetching upload status:', error);
                callback(false, []);
            });
    }

    function initializeStatusTracking() {
        function updateIndicator(dateSubmittedId, dateReturnedId, indicatorId, isActive) {
            const dateSubmitted = document.getElementById(dateSubmittedId).value;
            const dateReturned = document.getElementById(dateReturnedId).value;
            const indicator = document.getElementById(indicatorId);

            if (!indicator) return;

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
        for (let i = 2; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }

        lockPreviousStages(activeStage);
        checkIfFormCompleted();

        for (let i = 1; i <= 2; i++) {
            const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
            const dateReturned = document.getElementById(`dateReturned${i}`);

            if (dateSubmitted && dateReturned) {
                updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);

                dateSubmitted.addEventListener("change", function () {
                    let newActiveStage = 1;
                    for (let j = 2; j >= 1; j--) {
                        if (document.getElementById(`dateSubmitted${j}`).value) {
                            newActiveStage = j;
                            break;
                        }
                    }

                    lockPreviousStages(newActiveStage);
                    checkIfFormCompleted();

                    for (let j = 1; j <= 2; j++) {
                        updateIndicator(`dateSubmitted${j}`, `dateReturned${j}`, `indicator${j}`, j === newActiveStage);
                    }
                    toggleBudgetSpent();

                    // Enable the return date field once submission date is entered
                    if (this.value) {
                        document.getElementById(`dateReturned${i}`).removeAttribute('disabled');
                        document.getElementById(`dateReturned${i}`).removeAttribute('readonly');
                    }
                });

                dateReturned.addEventListener("change", function () {
                    updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);
                    enableNextStage(i);
                    toggleBudgetSpent();
                });
            }
        }
    }

    function enableNextStage(currentStage) {
        if (currentStage < 2) {
            // Check if the current stage has both dates filled
            const currentSubmitted = document.getElementById(`dateSubmitted${currentStage}`).value;
            const currentReturned = document.getElementById(`dateReturned${currentStage}`).value;

            if (currentSubmitted && currentReturned) {
                // Check if the next stage's files are uploaded before enabling
                checkUploadStatus(procurementId, currentStage + 1, function (allFilesUploaded, missingFiles) {
                    const nextSubmitted = document.getElementById(`dateSubmitted${currentStage + 1}`);
                    const nextReturned = document.getElementById(`dateReturned${currentStage + 1}`);

                    if (!nextSubmitted || !nextReturned) return;

                    if (allFilesUploaded) {
                        nextSubmitted.removeAttribute('disabled');
                        nextSubmitted.removeAttribute('readonly');
                    } else {
                        nextSubmitted.setAttribute('disabled', 'true');
                        nextSubmitted.title = `Missing files: ${missingFiles.join(', ')}`;
                    }

                    // Return field starts as disabled, will be enabled after submission date is entered
                    nextReturned.setAttribute('readonly', 'true');
                });
            }
        }
    }

    function toggleStageFields(stageNumber) {
        const dateReturned = document.getElementById(`dateReturned${stageNumber}`);
        const dateSubmittedNext = document.getElementById(`dateSubmitted${stageNumber + 1}`);
        const dateReturnedNext = document.getElementById(`dateReturned${stageNumber + 1}`);

        if (!dateReturned || !dateSubmittedNext || !dateReturnedNext) return;

        if (dateReturned.value) {
            // Check file upload status for next stage before enabling
            checkUploadStatus(procurementId, stageNumber + 1, function (allFilesUploaded, missingFiles) {
                if (allFilesUploaded) {
                    dateSubmittedNext.removeAttribute('readonly');
                    dateSubmittedNext.removeAttribute('disabled');
                    dateReturnedNext.removeAttribute('readonly');
                } else {
                    dateSubmittedNext.setAttribute('disabled', 'true');
                    dateSubmittedNext.title = `Missing files: ${missingFiles.join(', ')}`;
                    dateReturnedNext.setAttribute('readonly', 'true');
                }
            });
        } else {
            dateSubmittedNext.setAttribute('readonly', 'true');
            dateReturnedNext.setAttribute('readonly', 'true');
        }
    }

    for (let i = 1; i <= 1; i++) {
        const dateReturnedField = document.getElementById(`dateReturned${i}`);
        if (dateReturnedField) {
            dateReturnedField.addEventListener('change', function () {
                toggleStageFields(i);
            });

            if (!document.getElementById(`dateReturned${i}`).value) {
                const nextSubmitted = document.getElementById(`dateSubmitted${i + 1}`);
                const nextReturned = document.getElementById(`dateReturned${i + 1}`);
                if (nextSubmitted && nextReturned) {
                    nextSubmitted.setAttribute('readonly', 'true');
                    nextReturned.setAttribute('readonly', 'true');
                }
            }
        }
    }

    // Initialize read-only state of all stages based on previous stage completion
    for (let i = 1; i <= 2; i++) {
        const prevDateReturned = i > 1 ? document.getElementById(`dateReturned${i - 1}`) : null;
        const currentSubmitted = document.getElementById(`dateSubmitted${i}`);
        const currentReturned = document.getElementById(`dateReturned${i}`);

        if (i === 1) {
            // For the first stage, no previous stage exists, so no need to check prevDateReturned
            if (currentSubmitted && currentReturned) {
                currentSubmitted.removeAttribute('readonly');
                currentReturned.removeAttribute('readonly');
            }
        } else if (prevDateReturned && currentSubmitted && currentReturned && !prevDateReturned.value) {
            currentSubmitted.setAttribute('readonly', 'true');
            currentReturned.setAttribute('readonly', 'true');
        }
    }

    function toggleBudgetSpent() {
        let allCompleted = true;

        for (let i = 1; i <= 2; i++) {
            const submitted = document.getElementById(`dateSubmitted${i}`);
            const received = document.getElementById(`dateReturned${i}`);

            if (!submitted || !received) continue;

            if (!(submitted.value && received.value)) {
                allCompleted = false;
                break;
            }
        }

        const budgetSpentField = document.getElementById('budgetSpent');
        if (budgetSpentField) {
            if (allCompleted) {
                if (!budgetSpentField.value) {
                    budgetSpentField.removeAttribute('readonly');
                    budgetSpentField.removeAttribute('disabled');
                } else {
                    budgetSpentField.setAttribute('readonly', 'true');
                }
            } else {
                budgetSpentField.setAttribute('readonly', 'true');
            }
        }
    }

    function lockPreviousStages(activeStage) {
        for (let i = 1; i < activeStage; i++) {
            const submittedField = document.getElementById(`dateSubmitted${i}`);
            const returnedField = document.getElementById(`dateReturned${i}`);

            if (submittedField && returnedField) {
                submittedField.setAttribute('readonly', 'true');
                returnedField.setAttribute('readonly', 'true');
            }
        }
    }

    function checkIfFormCompleted() {
        let allCompleted = true;
        for (let i = 1; i <= 2; i++) {
            const submitted = document.getElementById(`dateSubmitted${i}`);
            const returned = document.getElementById(`dateReturned${i}`);

            if (!submitted || !returned) continue;

            if (!(submitted.value && returned.value)) {
                allCompleted = false;
                break;
            }
        }

        if (allCompleted) {
            lockAllRows();
            const budgetSpentField = document.getElementById('budgetSpent');
            if (budgetSpentField) {
                budgetSpentField.setAttribute('readonly', 'true');
            }
        }
    }

    function lockAllRows() {
        for (let i = 1; i <= 2; i++) {
            const submittedField = document.getElementById(`dateSubmitted${i}`);
            const returnedField = document.getElementById(`dateReturned${i}`);

            if (submittedField && returnedField) {
                submittedField.setAttribute('readonly', 'true');
                returnedField.setAttribute('readonly', 'true');
            }
        }
    }
});