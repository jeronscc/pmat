document.addEventListener('DOMContentLoaded', function () {
    const procurementId = document.getElementById('procurement_id')?.value;
    if (!procurementId) {
        alert('Error: Procurement ID is missing.');
        return;
    }

    // Fetch uploaded files when the page loads
    fetchUploadedFiles(procurementId);

    // Save button click event listener for the form
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            const form = document.getElementById('requirementsForm');
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

            fetch('/api/requirements/upload', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Server response:", data); // Log response

                if (data.success) {
                    alert(data.message);
                    fetchUploadedFiles(procurementId); // Fetch and display uploaded files after saving
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
        fetch(`/api/requirements/${procurementId}/files`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUploadedFiles(data.files);
                } else {
                    console.error("Failed to fetch uploaded files:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    // Function to display uploaded files
    function displayUploadedFiles(files) {
        for (const [requirementName, filePath] of Object.entries(files)) {
            const fileInputContainer = document.getElementById(`${requirementName}Container`);
            const fileListContainer = document.getElementById(`${requirementName}Uploaded`);

            if (!fileInputContainer || !fileListContainer) {
                console.error(`Containers for ${requirementName} not found.`);
                continue;
            }

            // Clear existing files in the list container
            fileListContainer.innerHTML = '';

            // Create and append the file link
            const fileLink = document.createElement('a');
            fileLink.href = `/${filePath}`;
            fileLink.textContent = `View ${requirementName}`;
            fileLink.target = '_blank';

            const listItem = document.createElement('li');
            listItem.appendChild(fileLink);
            fileListContainer.appendChild(listItem);

            // Hide the file input container
            fileInputContainer.style.display = 'none';
        }
    }

    // Event listener to open the modal and fetch uploaded files
    const openModalBtn = document.getElementById('openModalBtn');
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            if (procurementId) {
                fetchUploadedFiles(procurementId);
            }
        });
    }
});
