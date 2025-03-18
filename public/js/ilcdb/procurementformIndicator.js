document.addEventListener('DOMContentLoaded', function() {
    const procurementIdElement = document.getElementById('procurementId');
    
    if (!procurementIdElement) {
        console.error("Procurement ID element not found.");
        return;
    }
    
    const procurementId = procurementIdElement.value;
    const procurementUpdateUrl = "/api/procurement/update";  // Use the API route for procurement update

    // Initialize the status tracking after checking file uploads
    initializeFileUploadStatuses(procurementId);
    
    // Set up a polling mechanism to check for file uploads regularly
    setUpFileUploadPolling(procurementId);
    
    document.getElementById('saveChanges').addEventListener('click', function(e) {
        e.preventDefault();

        // Temporarily remove the 'disabled' attribute from all fields before submitting
        // This ensures all field values are included in the form submission
        const allDateFields = document.querySelectorAll('input[id^="dateSubmitted"], input[id^="dateReturned"]');
        allDateFields.forEach(field => field.removeAttribute('disabled'));

        let formData = new FormData(document.getElementById('procurementForm'));

        const dateSubmitted1 = document.getElementById('dateSubmitted1').value;
        if (!dateSubmitted1) {
            alert('No data inputted');
            return;
        }

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
            // Instead of reloading, update the UI state
            updateUIAfterSave();
        })
        .catch(error => {
            console.error('Error saving data:', error);
            alert('Error saving data. Check console for details.');
        });
    });

    function updateUIAfterSave() {
        // Re-lock all appropriate fields
        let activeStage = 1;
        for (let i = 6; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }
        
        lockPreviousStages(activeStage);
        checkIfFormCompleted();
        
        // Then refresh file upload statuses
        refreshFileUploadStatuses(procurementId);
    }
    
    document.getElementById('cancelChanges').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/homepage-ilcdb';
    });

    function setUpFileUploadPolling(procurementId) {
        // Poll for file upload changes every 5 seconds
        const pollingInterval = 5000; // 5 seconds
        
        setInterval(function() {
            refreshFileUploadStatuses(procurementId);
        }, pollingInterval);
        
        // Also set up listeners for file upload events if possible
        // This depends on how your file upload system is implemented
        const fileUploadForms = document.querySelectorAll('form[id^="fileUploadForm"]');
        if (fileUploadForms.length > 0) {
            fileUploadForms.forEach(form => {
                form.addEventListener('submit', function() {
                    // After a short delay to allow for server processing
                    setTimeout(() => {
                        refreshFileUploadStatuses(procurementId);
                    }, 2000);
                });
            });
        }
    }

    function refreshFileUploadStatuses(procurementId) {
        console.log("Refreshing file upload statuses...");
        
        // Check all modals for updated file status
        for (let i = 1; i <= 6; i++) {
            checkUploadStatus(procurementId, i, function(allFilesUploaded, missingFiles) {
                updateStageEnabledState(i, allFilesUploaded, missingFiles);
            });
        }
    }

    function updateStageEnabledState(stageNumber, allFilesUploaded, missingFiles) {
        const dateSubmitted = document.getElementById(`dateSubmitted${stageNumber}`);
        const dateReturned = document.getElementById(`dateReturned${stageNumber}`);
        
        if (!dateSubmitted || !dateReturned) return;
        
        // First check: If field is already marked as readonly, keep it locked
        if (dateSubmitted.hasAttribute('readonly')) {
            return; // Don't change the state of fields that are deliberately locked
        }
        
        // Get current active stage
        let activeStage = 1;
        for (let i = 6; i >= 1; i--) {
            if (document.getElementById(`dateSubmitted${i}`).value) {
                activeStage = i;
                break;
            }
        }
    
        // Handle stage 1 or stages where the previous stage is completed
        const prevStageCompleted = stageNumber === 1 || 
            (document.getElementById(`dateReturned${stageNumber-1}`) && 
             document.getElementById(`dateReturned${stageNumber-1}`).value);
             
        // Enable fields if:
        // 1. All files are uploaded for this stage
        // 2. Either it's stage 1 OR the previous stage is completed
        // 3. The stage is not locked (not a previous completed stage)
        if (allFilesUploaded && prevStageCompleted && stageNumber >= activeStage) {
            console.log(`Stage ${stageNumber} files complete and previous stage completed. Enabling fields.`);
            dateSubmitted.removeAttribute('disabled');
            dateSubmitted.removeAttribute('readonly');
            
            // Only enable return date if submission date exists
            if (dateSubmitted.value) {
                dateReturned.removeAttribute('disabled');
                dateReturned.removeAttribute('readonly');
            }
            
            // Update tooltip
            dateSubmitted.title = "All files uploaded. Fields enabled.";
            dateReturned.title = dateSubmitted.value ? "All files uploaded. Fields enabled." : "Enter submission date first";
            
            // Add a visual indicator that this field was just enabled
            dateSubmitted.classList.add('field-just-enabled');
            setTimeout(() => {
                dateSubmitted.classList.remove('field-just-enabled');
            }, 3000);
        } else if (!allFilesUploaded) {
            // Disable fields if files are missing
            dateSubmitted.setAttribute('disabled', 'true');
            dateReturned.setAttribute('disabled', 'true');
            
            // Update tooltip with missing files info
            const missingFilesText = missingFiles && missingFiles.length > 0 
                ? `Missing files: ${missingFiles.join(', ')}` 
                : 'Missing required files';
            dateSubmitted.title = missingFilesText;
            dateReturned.title = missingFilesText;
        }
    }

    function initializeFileUploadStatuses(procurementId) {
        // Check all modals initially
        for (let i = 1; i <= 6; i++) {
            checkUploadStatus(procurementId, i, function(allFilesUploaded, missingFiles) {
                const dateSubmitted = document.getElementById(`dateSubmitted${i}`);
                const dateReturned = document.getElementById(`dateReturned${i}`);
                
                if (!dateSubmitted || !dateReturned) return;

                // Only enable date fields if all files are uploaded
                if (allFilesUploaded) {
                    // Still respect the stage sequence logic
                    if (i === 1 || (i > 1 && document.getElementById(`dateReturned${i-1}`).value)) {
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
        fetch(`/api/uploadedProcurementFilesCheck/${procurementId}?modal=${modalNumber}`)
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

                    lockPreviousStages(newActiveStage);
                    checkIfFormCompleted();

                    for (let j = 1; j <= 6; j++) {
                        updateIndicator(`dateSubmitted${j}`, `dateReturned${j}`, `indicator${j}`, j === newActiveStage);
                    }
                    toggleBudgetSpent();
                    
                    // Enable the return date field once submission date is entered
                    if (this.value) {
                        document.getElementById(`dateReturned${i}`).removeAttribute('disabled');
                        document.getElementById(`dateReturned${i}`).removeAttribute('readonly');
                    }
                });

                dateReturned.addEventListener("change", function() {
                    updateIndicator(`dateSubmitted${i}`, `dateReturned${i}`, `indicator${i}`, i === activeStage);
                    enableNextStage(i);
                    toggleBudgetSpent();
                });
            }
        }
    }

    function enableNextStage(currentStage) {
        if (currentStage < 6) {
            // Check if the current stage has both dates filled
            const currentSubmitted = document.getElementById(`dateSubmitted${currentStage}`).value;
            const currentReturned = document.getElementById(`dateReturned${currentStage}`).value;
            
            if (currentSubmitted && currentReturned) {
                // Check if the next stage's files are uploaded before enabling
                checkUploadStatus(procurementId, currentStage + 1, function(allFilesUploaded, missingFiles) {
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
            checkUploadStatus(procurementId, stageNumber + 1, function(allFilesUploaded, missingFiles) {
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

    for (let i = 1; i <= 5; i++) {
        const dateReturnedField = document.getElementById(`dateReturned${i}`);
        if (dateReturnedField) {
            dateReturnedField.addEventListener('change', function() {
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
    for (let i = 2; i <= 6; i++) {
        const prevDateReturned = document.getElementById(`dateReturned${i-1}`);
        const currentSubmitted = document.getElementById(`dateSubmitted${i}`);
        const currentReturned = document.getElementById(`dateReturned${i}`);
        
        if (prevDateReturned && currentSubmitted && currentReturned && !prevDateReturned.value) {
            currentSubmitted.setAttribute('readonly', 'true');
            currentReturned.setAttribute('readonly', 'true');
        }
    }

    function toggleBudgetSpent() {
        let allCompleted = true;

        for (let i = 1; i <= 6; i++) {
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
                budgetSpentField.removeAttribute('readonly');
                budgetSpentField.removeAttribute('disabled');
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
        for (let i = 1; i <= 6; i++) {
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
        for (let i = 1; i <= 6; i++) {
            const submittedField = document.getElementById(`dateSubmitted${i}`);
            const returnedField = document.getElementById(`dateReturned${i}`);
            
            if (submittedField && returnedField) {
                submittedField.setAttribute('readonly', 'true');
                returnedField.setAttribute('readonly', 'true');
            }
        }
    }
    

});