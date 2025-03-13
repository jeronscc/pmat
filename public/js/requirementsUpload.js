document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.saveBtn').forEach(button => {
        button.addEventListener('click', function () {
            const formType = this.getAttribute('data-form-type');
            const form = document.querySelector(`#requirementsForm${capitalizeFirstLetter(formType)}`);
            const formData = new FormData(form);

            const procurementId = form.querySelector('input[name="procurement_id"]').value;
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

            const uploadUrl = formType === 'honoraria' ? '/api/requirements/upload' : '/api/otherexpense/upload';

            fetch(uploadUrl, {
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
                        fetchUploadedFiles(procurementId, formType); // Fetch and display uploaded files after saving
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
    });

    // Function to fetch and display uploaded files
    function fetchUploadedFiles(procurementId, formType) {
        const fetchUrl = formType === 'honoraria' ? `/api/requirements/${procurementId}` : `/api/otherexpense/requirements/${procurementId}`;
        fetch(fetchUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUploadedFiles(data.files, formType);
                } else {
                    console.error("Failed to fetch uploaded files:", data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching uploaded files:", error);
            });
    }

    // Function to display uploaded files
    function displayUploadedFiles(files, formType) {
        const fileListContainer = document.getElementById(`uploadedFilesList${capitalizeFirstLetter(formType)}`);
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
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function () {
            const formType = this.getAttribute('data-bs-target').replace('#requirementsModal', '').toLowerCase();
            const procurementId = document.getElementById(`procurement_id_${formType}`).value;
            if (procurementId) {
                fetchUploadedFiles(procurementId, formType);
            }
        });
    });

    // Fetch uploaded files when the page loads
    document.querySelectorAll('input[name="procurement_id"]').forEach(input => {
        const formType = input.id.replace('procurement_id_', '');
        const procurementId = input.value;
        if (procurementId) {
            fetchUploadedFiles(procurementId, formType);
        }
    });

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
});
