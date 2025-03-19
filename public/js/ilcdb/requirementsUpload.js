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

            fetch('/api/upload', {
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
        fetch(`/api/requirements/${procurementId}`)
            .then(response => response.json())
            .then(data => {
                console.log("Fetched files data:", data); // Log the fetched data for debugging

                if (data.success && data.files) {
                    // Ensure files is an array
                    if (!Array.isArray(data.files)) {
                        console.error("Files is not an array, converting to array:", data.files);
                        data.files = Object.values(data.files);
                    }

                    // Display uploaded files in their respective sections
                    displayUploadedFiles('ORS', data.files);
                    displayUploadedFiles('DV', data.files);
                    displayUploadedFiles('Contract', data.files);
                    displayUploadedFiles('Classification', data.files);
                    displayUploadedFiles('Report', data.files);
                    displayUploadedFiles('Attendance', data.files);
                    displayUploadedFiles('Resume', data.files);
                    displayUploadedFiles('Govid', data.files);
                    displayUploadedFiles('Payslip', data.files);
                    displayUploadedFiles('Bank', data.files);
                    displayUploadedFiles('Cert', data.files);
                } else {
                    console.error("Failed to fetch uploaded files or no files found:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    // Function to display uploaded files in their respective sections
    function displayUploadedFiles(requirement, files) {
        const fileListContainer = document.getElementById(`${requirement.toLowerCase()}FileUploaded`);
        if (!fileListContainer) {
            console.error(`Container for ${requirement} not found`);
            return;
        }

        fileListContainer.innerHTML = ''; // Clear existing files

        // Log the files array for each section for debugging purposes
        console.log(`Files for ${requirement}:`, files);

        // Filter the files by requirement name and display them
        files.filter(file => file.requirement_name && file.requirement_name.includes(requirement)).forEach(file => {
            const fileLink = document.createElement('a');
            fileLink.href = `/${file.file_path}`;
            fileLink.textContent = file.requirement_name;
            fileLink.target = '_blank';

            const listItem = document.createElement('li');
            listItem.appendChild(fileLink);

            // Disable and hide the corresponding file input field
            const inputField = document.getElementById(`${requirement.toLowerCase()}File`);
            if (inputField) {
                inputField.disabled = true;
                inputField.style.display = 'none';
            }

            fileListContainer.appendChild(listItem);
        });
    }

    // Event listener to open the modal and fetch uploaded files
    const requirementsModal = document.getElementById('requirementsModal');
    if (requirementsModal) {
        requirementsModal.addEventListener('shown.bs.modal', function () {
            fetchUploadedFiles(procurementId);
        });
    }
});