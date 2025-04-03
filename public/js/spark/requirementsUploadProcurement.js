document.addEventListener('DOMContentLoaded', function () {
    const modals = [1, 2, 3, 4, 5, 6];

    modals.forEach(modalNumber => {
        const saveButton = document.getElementById(`saveBtn${modalNumber}`);
        if (saveButton) {
            saveButton.addEventListener('click', function () {
                if (areAllFilesSelected(modalNumber)) {
                    uploadFiles(modalNumber); // ✅ Proceed only if all files are selected
                } else {
                    alert(`Please upload all required files before saving Modal ${modalNumber}.`);
                }
            });
        }
    });

    function areAllFilesSelected(modalNumber) {
        const form = document.getElementById(`requirementsForm${modalNumber}`);
        if (!form) return false;

        const fileInputs = form.querySelectorAll('input[type="file"]');
        for (const fileInput of fileInputs) {
            if (fileInput.files.length === 0) {
                return false; // ❌ Missing a required file
            }
        }
        return true; // ✅ All files are selected
    }

    function uploadFiles(modalNumber) {
        const form = document.getElementById(`requirementsForm${modalNumber}`);
        if (!form) {
            console.error(`Error: Form requirementsForm${modalNumber} not found.`);
            return;
        }

        const formData = new FormData(form);
        const procurementId = document.getElementById('procurementId')?.value;
        if (!procurementId) {
            alert('Error: Procurement ID is missing.');
            return;
        }

        formData.append('procurement_id', procurementId);
        formData.append('modal', modalNumber); // ✅ Send modal number

        console.log(`Uploading files for modal ${modalNumber}:`, [...formData.entries()]);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error("Error: CSRF token not found.");
            return;
        }

        fetch(`/api/spark/procurement/upload`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log(`Server response for modal ${modalNumber}:`, data);
                if (data.success) {
                    //alert(data.message);

                    // Fetch uploaded files to update the UI dynamically
                    fetchUploadedFiles(procurementId, modalNumber);

                    // Refresh the entire page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000); // Delay to ensure fetch completes before reload
                } else {
                    alert("Upload failed: " + (data.message || "Unknown error."));
                }
            })
            .catch(error => {
                console.error("Error during upload:", error);
                alert("Upload failed. Check console for details.");
            });
    }

    function fetchUploadedFiles(procurementId, modalNumber) {
        fetch(`/api/spark/procurement/requirements/${procurementId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUploadedFiles(data.files, modalNumber);
                } else {
                    console.error("Failed to fetch uploaded files:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    function displayUploadedFiles(files, modalNumber) {
        files.forEach(file => {
            const fileInput = document.getElementById(`${file.requirement_name}${modalNumber}`);
            const fileLinkContainer = document.getElementById(`${file.requirement_name}${modalNumber}Link`);
            if (fileInput && fileLinkContainer) {
                fileInput.style.display = 'none'; // Hide the file input

                // Create the file link
                const fileLink = document.createElement('a');
                fileLink.href = `/${file.file_path}`;

                // Get the file size (assuming it's in bytes)
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

                // Set the link text content (including requirement name and file size)
                fileLink.textContent = `View ${file.requirement_name} (${formattedFileSize})`;
                fileLink.target = '_blank';
                fileLink.style.fontWeight = 'bold';

                // Clear existing content and append the new file link
                fileLinkContainer.innerHTML = '';
                fileLinkContainer.appendChild(fileLink);

                // Show the uploaded file link in other modals that need it
                modals.forEach(otherModalNumber => {
                    if (otherModalNumber !== modalNumber) {
                        const otherFileLinkContainer = document.getElementById(`${file.requirement_name}${otherModalNumber}Link`);
                        if (otherFileLinkContainer) {
                            otherFileLinkContainer.style.display = 'block';  // Ensure it's visible
                            otherFileLinkContainer.innerHTML = '';  // Clear existing content
                            otherFileLinkContainer.appendChild(fileLink.cloneNode(true)); // Append the link in other modal
                        }
                    }
                });
            }
        });
    }

    const procurementId = document.getElementById('procurementId')?.value;
    if (procurementId) {
        modals.forEach(modalNumber => {
            fetchUploadedFiles(procurementId, modalNumber);
        });
    }
});
