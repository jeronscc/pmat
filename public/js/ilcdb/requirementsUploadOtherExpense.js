document.addEventListener('DOMContentLoaded', function () {
    const procurementIdField = document.getElementById('procurement_id');
    const procurementId = procurementIdField?.value;

    // Fetch and display files for each section
    function fetchUploadedFiles(procurementId) {
        fetch(`/api/otherexpense/requirements/${procurementId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display files in respective sections based on the requirement type
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

    // Display files in their respective sections based on requirement type
    function displayUploadedFiles(requirement, files) {
        const fileListContainer = document.getElementById(`uploadedFilesList${requirement}`);
        const fileInput = document.getElementById(`${requirement.toLowerCase()}File`);
        fileListContainer.innerHTML = ''; // Clear the existing list

        // Filter the files by requirement name and display them
        files.filter(file => file.requirement_name.includes(requirement)).forEach(file => {
            const fileLink = document.createElement('a');
            fileLink.href = `/${file.file_path}`;
            fileLink.textContent = file.requirement_name;
            fileLink.target = '_blank';

            const listItem = document.createElement('li');
            listItem.appendChild(fileLink);

            fileListContainer.appendChild(listItem);

            // Hide the input field once the file is uploaded
            if (fileInput) {
                fileInput.style.display = 'none';  // Hide input field
            }
        });
    }

    // Call fetchUploadedFiles when the page loads
    if (procurementId) {
        fetchUploadedFiles(procurementId);
    }

    // Handle the file upload for the modal form
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
                // After saving, update the displayed files
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

    // Re-fetch files when the modal is opened
    document.getElementById('requirementsModal1').addEventListener('shown.bs.modal', function () {
        if (procurementId) {
            fetchUploadedFiles(procurementId);
        }
    });
});
