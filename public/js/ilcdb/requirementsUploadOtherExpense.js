document.addEventListener('DOMContentLoaded', function () {
    const procurementId = document.getElementById('procurement_id')?.value;
    if (!procurementId) {
        alert('Error: Procurement ID is missing.');
        return;
    }

    // Fetch uploaded files when the page loads
    fetchUploadedFiles(procurementId);

    // Save button click event listener for form 1
    const saveBtn1 = document.getElementById('saveBtn1');
    if (saveBtn1) {
        saveBtn1.addEventListener('click', function () {
            const form = document.getElementById('requirementsForm1');
            if (!form) {
                console.error("Form not found.");
                return;
            }

            const formData = new FormData(form);
            formData.append('procurement_id', procurementId);

            console.log("Sending form data:", [...formData.entries()]); // Debugging log

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error("Error: CSRF token not found.");
                return;
            }

            fetch('/api/otherexpense/upload', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Server response:", data); // Log response

                    if (data.success) {
                        //alert(data.message);
                        // Important: Fetch files again after successful upload
                        fetchUploadedFiles(procurementId);
                    } else {
                        alert("Upload failed: " + (data.message || "Unknown error."));
                    }
                })
                .catch(error => {
                    console.error("Error during upload:", error);
                    alert("Upload failed. Check console for details.");
                });
        });
    }

    // Function to fetch and display uploaded files
    function fetchUploadedFiles(procurementId) {
        fetch(`/api/otherexpense/requirements/${procurementId}`)
            .then(response => response.json())
            .then(data => {
                console.log("Fetched files data:", data); // Log the fetched data for debugging

                if (data.success && data.files) {
                    // Ensure files is an array
                    const files = Array.isArray(data.files) ? data.files : Object.values(data.files);
                    console.log("Processing files:", files); // Log processed files array

                    // Display each file type
                    displayFilesForRequirement('ORS', files);
                    displayFilesForRequirement('DV', files);
                    displayFilesForRequirement('TravelOrder', files);
                    displayFilesForRequirement('Appearance', files);
                    displayFilesForRequirement('Report', files);
                    displayFilesForRequirement('Itinerary', files);
                    displayFilesForRequirement('Cert', files);
                } else {
                    console.error("Failed to fetch uploaded files or no files found:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    // Function to display files for a specific requirement
    function displayFilesForRequirement(requirementType, files) {
        // Get the file list container for this requirement
        const fileListContainer = document.getElementById(`uploadedFilesList${requirementType}`);
        if (!fileListContainer) {
            console.error(`Container for ${requirementType} not found: uploadedFilesList${requirementType}`);
            return;
        }

        // Get the file input associated with this requirement
        // Fixed: Handle TravelOrder properly with lowercase first letter in ID (travelOrderFile)
        const fileInputId = requirementType === 'TravelOrder' ? 'travelOrderFile' : `${requirementType.toLowerCase()}File`;
        const fileInput = document.getElementById(fileInputId);

        // Clear existing content
        fileListContainer.innerHTML = '';

        // Find files for this requirement type - consider multiple possible formats
        const matchingFiles = files.filter(file => {
            if (!file.requirement_name) return false;

            // Check for various formats of the requirement name
            const reqName = file.requirement_name.toLowerCase();
            const searchType = requirementType.toLowerCase();

            return reqName.includes(searchType) ||
                reqName.includes(searchType.replace(/([A-Z])/g, ' $1').trim().toLowerCase()) ||  // Check with spaces
                reqName.includes(searchType.replace(/([A-Z])/g, '_$1').trim().toLowerCase());    // Check with underscores
        });

        console.log(`Files matching ${requirementType}:`, matchingFiles);

        // If we have matching files, show them and hide the file input
        if (matchingFiles.length > 0) {
            matchingFiles.forEach(file => {
                // Create the file link
                const fileLink = document.createElement('a');

                const fileSize = file.size || 0; // Default to 0 if size is missing or invalid
                // Format the file size to KB or MB
                let formattedFileSize = '';
                if (fileSize < 1024) {
                    formattedFileSize = `${fileSize} bytes`;
                } else if (fileSize < 1048576) {
                    formattedFileSize = `${(fileSize / 1024).toFixed(2)} KB`;
                } else {
                    formattedFileSize = `${(fileSize / 1048576).toFixed(2)} MB`;
                }

                fileLink.href = `/${file.file_path}`;
                fileLink.textContent = file.original_filename || `View ${requirementType} (${formattedFileSize})`;
                fileLink.target = '_blank';
                fileLink.classList.add('text-primary', 'fw-bold');

                // Create a container for the link
                const linkContainer = document.createElement('div');
                linkContainer.classList.add('mt-2', 'mb-2');
                linkContainer.appendChild(fileLink);

                // Add the link to the file list container
                fileListContainer.appendChild(linkContainer);

                if (fileInput) {
                    // Hide the file input since we already have a file
                    fileInput.style.display = 'none';
                    fileInput.disabled = true;

                    // Explicitly log when we're hiding an input
                    console.log(`Hiding input for ${requirementType} (ID: ${fileInputId})`);
                }
            });
        } else {
            // Make sure the file input is visible if no files exist
            if (fileInput) {
                fileInput.style.display = '';
                fileInput.disabled = false;

                // Explicitly log when we're showing an input
                console.log(`Showing input for ${requirementType} (ID: ${fileInputId})`);
            }
        }
    }

    // Event listener to open the modal and fetch uploaded files
    const requirementsModal1 = document.getElementById('requirementsModal1');
    if (requirementsModal1) {
        requirementsModal1.addEventListener('shown.bs.modal', function () {
            fetchUploadedFiles(procurementId);
        });
    }
});