document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('saveBtn').addEventListener('click', function () {
        const form = document.getElementById('requirementsForm');
        const formData = new FormData(form);

        const procurementId = document.getElementById('procurement_id')?.value;
        if (!procurementId) {
            alert('Error: Procurement ID is missing.');
            return;
        }
        formData.append('procurement_id', procurementId);

        console.log("Sending form data:", [...formData.entries()]); // ✅ Debugging log

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
        .then(response => response.text()) // ✅ Read as text first
        .then(text => {
            console.log("Server response:", text); // ✅ Log raw response

            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert(data.message);
                    fetchUploadedFiles(procurementId); // Fetch and display uploaded files after saving
                } else {
                    alert("Upload failed: " + (data.message || "Unknown error."));
                }
            } catch (error) {
                console.error("Response is not valid JSON:", text);
                alert("Upload failed. Server returned an unexpected response.");
            }
        })
        .catch(error => {
            console.error("Error during upload:", error);
            alert("Upload failed. Check console for details.");
        });
    });

    // Function to fetch and display uploaded files
    function fetchUploadedFiles(procurementId) {
        fetch(`/api/requirements/${procurementId}`)
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
        const fileListContainer = document.getElementById('uploadedFilesList');
        fileListContainer.innerHTML = ''; // Clear existing files

        files.forEach(file => {
            const fileLink = document.createElement('a');
            fileLink.href = `/${file.file_path}`;
            fileLink.textContent = file.requirement_name;
            fileLink.target = '_blank';

            const listItem = document.createElement('li');
            listItem.appendChild(fileLink);

            // Disable the corresponding file input field
            const inputField = document.getElementById(file.requirement_name);
            if (inputField) {
                inputField.disabled = true;
                inputField.style.display = 'none';
            }

            fileListContainer.appendChild(listItem);
        });
    }

    // Event listener to open the modal and fetch uploaded files
    document.getElementById('openModalBtn').addEventListener('click', function () {
        const procurementId = document.getElementById('procurement_id')?.value;
        if (procurementId) {
            fetchUploadedFiles(procurementId);
        }
    });
});
