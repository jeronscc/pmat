document.addEventListener('DOMContentLoaded', function () {
    const procurementIdField = document.getElementById('procurement_id');
    const procurementId = procurementIdField?.value;

    // This is the container for the list of uploaded files
    const fileListContainer = document.getElementById('uploadedFilesListOtherExpense');

    // Fetch uploaded files based on procurement ID
    function fetchUploadedFiles(procurementId) {
        fetch(`/api/otherexpense/requirements/${procurementId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUploadedFiles(data.files);  // Display files if successful
                } else {
                    console.error("Failed to fetch uploaded files:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    // Function to display uploaded files next to their respective file names
    function displayUploadedFiles(files) {
        // Loop over each file in the list of uploaded files
        files.forEach(file => {
            const fileLink = document.createElement('a');
            fileLink.href = `/${file.file_path}`; // File link path
            fileLink.textContent = `View ${file.requirement_name} File (${(file.size / 1024).toFixed(2)} KB)`; // File name and size as text
            fileLink.target = '_blank'; // Open in new tab

            const listItem = document.createElement('li');
            listItem.appendChild(fileLink);

            // For each file input field in the modal, disable and hide it
            const inputField = document.getElementById(file.requirement_name);
            if (inputField) {
                inputField.disabled = true;
                inputField.style.display = 'none';
            }

            // Insert the link directly below the file name in the modal
            const fileSection = document.getElementById(`${file.requirement_name}Section`);
            if (fileSection) {
                const uploadedFileList = fileSection.querySelector('.uploaded-files-list');
                if (!uploadedFileList) {
                    const newFileList = document.createElement('ul');
                    newFileList.classList.add('uploaded-files-list');
                    fileSection.appendChild(newFileList);
                }

                const uploadedFileList = fileSection.querySelector('.uploaded-files-list');
                uploadedFileList.appendChild(listItem);  // Append the uploaded file link
            }
        });
    }

    // Call fetchUploadedFiles when the page loads to display already uploaded files
    if (procurementId) {
        fetchUploadedFiles(procurementId);
    }

    // Handling the form save button click event
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
                displayUploadedFiles(data.files); // Update displayed files after saving
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
