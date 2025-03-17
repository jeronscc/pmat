document.addEventListener('DOMContentLoaded', function () {
    const procurementIdField = document.getElementById('procurement_id');
    const procurementId = procurementIdField?.value;

    // Fetch and display files for each section
    function fetchUploadedFiles(procurementId) {
        fetch(`/api/otherexpense/requirements/${procurementId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display files in respective lists based on requirement
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

    // Display files in their respective lists based on requirement type
    function displayUploadedFiles(requirement, files) {
        // Get the container for the specific file list (e.g., ORS, DV, etc.)
        const fileListContainer = document.getElementById(`uploadedFilesList${requirement}`);
        fileListContainer.innerHTML = ''; // Clear the existing list

        // Filter the files by requirement name and display them
        files.filter(file => file.requirement_name.includes(requirement)).forEach(file => {
            const fileLink = document.createElement('a');
            fileLink.href = `/${file.file_path}`;
            fileLink.textContent = file.requirement_name;
            fileLink.target = '_blank';

            const listItem = document.createElement('li');
            listItem.appendChild(fileLink);

            const inputField = document.getElementById(file.requirement_name);
            if (inputField) {
                inputField.disabled = true;
                inputField.style.display = 'none';
            }

            fileListContainer.appendChild(listItem);
        });
    }

    // Call fetchUploadedFiles when the page loads
    if (procurementId) {
        fetchUploadedFiles(procurementId);
    }

    // Handle the file upload
    document.getElementById('saveBtn1').addEventListener('click', function () {
        const form = document.getElementById('requirementsForm1');
        const formData = new FormData(form);

        if (!procurementId) {
            alert('Error: Procurement ID is missing.');
            return;
        }
        formData.append('procurement_id', procurementId);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/api/otherexpense/upload', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Update displayed files after saving
                displayUploadedFiles('ORS', data.files);
                displayUploadedFiles('DV', data.files);
                displayUploadedFiles('TravelOrder', data.files);
                displayUploadedFiles('Appearance', data.files);
                displayUploadedFiles('Report', data.files);
                displayUploadedFiles('Itinerary', data.files);
                displayUploadedFiles('Cert', data.files);
            } else {
                alert("Upload failed: " + (data.message || "Unknown error."));
            }
        })
        .catch(error => {
            console.error("Error during upload:", error);
            alert("Upload failed. Check console for details.");
        });
    });
});
