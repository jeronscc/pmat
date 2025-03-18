document.addEventListener('DOMContentLoaded', function () {
    // Get procurementId and check if it's present
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

            console.log("Sending form data:", [...formData.entries()]); // ✅ Debugging log

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
            .then(response => response.json()) // Read as JSON
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
                if (data.success) {
                    // Display uploaded files in their respective sections (DV, ORS, etc.)
                    displayUploadedFiles('ORS', data.files);
                    displayUploadedFiles('DV', data.files);
                    displayUploadedFiles('TravelOrder', data.files);
                    displayUploadedFiles('Appearance', data.files);
                    displayUploadedFiles('Report', data.files);
                    displayUploadedFiles('Itinerary', data.files);
                    displayUploadedFiles('Cert', data.files);
                } else {
                    console.error("Failed to fetch uploaded files:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    // Function to display uploaded files in their respective sections
    function displayUploadedFiles(requirement, files) {
        const fileListContainer = document.getElementById(`uploadedFilesList${requirement}`);
        if (!fileListContainer) {
            console.error(`Container for ${requirement} not found`);
            return;
        }

        fileListContainer.innerHTML = ''; // Clear existing files

        // Ensure files is an array (in case it's an object)
        if (!Array.isArray(files)) {
            console.error("Files is not an array, converting to array:", files);
            files = Object.values(files);  // Convert object to array
        }

        // Filter the files by requirement name and display them
        files.filter(file => file.requirement_name.includes(requirement)).forEach(file => {
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
    const requirementsModal1 = document.getElementById('requirementsModal1');
    if (requirementsModal1) {
        requirementsModal1.addEventListener('shown.bs.modal', function () {
            fetchUploadedFiles(procurementId);
        });
    }
});
