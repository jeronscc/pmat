document.addEventListener('DOMContentLoaded', function () {
    const modals = [1, 2, 3, 4, 5, 6];

    modals.forEach(modalNumber => {
        const saveButton = document.getElementById(`saveBtn${modalNumber}`);
        if (saveButton) {
            saveButton.addEventListener('click', function () {
                const form = document.getElementById(`requirementsForm${modalNumber}`);
                const formData = new FormData(form);

                const procurementId = document.getElementById('procurementId')?.value;
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

                fetch(`/api/procurement/upload`, {
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
                            fetchUploadedFiles(procurementId, modalNumber); // Fetch and display uploaded files after saving
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
        }

        // Event listener to open the modal and fetch uploaded files
        const modalButton = document.querySelector(`[data-bs-target="#requirementsModal${modalNumber}"]`);
        if (modalButton) {
            modalButton.addEventListener('click', function () {
                const procurementId = document.getElementById('procurementId')?.value;
                if (procurementId) {
                    fetchUploadedFiles(procurementId, modalNumber);
                }
            });
        }
    });

    // Function to fetch and display uploaded files
    function fetchUploadedFiles(procurementId, modalNumber) {
        fetch(`/api/procurement/requirements/${procurementId}`)
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

    // Function to display uploaded files
    function displayUploadedFiles(files, modalNumber) {
        files.forEach(file => {
            const fileInput = document.getElementById(`${file.requirement_name}${modalNumber}`);
            const fileLinkContainer = document.getElementById(`${file.requirement_name}${modalNumber}Link`);
            const fileLabel = document.getElementById(`${file.requirement_name}${modalNumber}Label`); // Reference to the label
    
            if (fileInput && fileLinkContainer && fileLabel) {
                // Hide the file input once the file is uploaded
                fileInput.style.display = 'none'; 
    
                // Create the file link
                const fileLink = document.createElement('a');
                fileLink.href = `/${file.file_path}`; // Path to the uploaded file
                fileLink.textContent = `View ${file.requirement_name} File`;
                fileLink.target = '_blank';
    
                // Initially hide the file link container
                fileLinkContainer.style.display = 'none';
    
                // Add a click event to the label
                fileLabel.addEventListener('click', function () {
                    // Show the uploaded file link when the label is clicked
                    fileLinkContainer.style.display = 'block'; // Show the container with the link
                    fileLinkContainer.innerHTML = ''; // Clear any previous content
                    fileLinkContainer.appendChild(fileLink); // Append the link to the container
                });
    
                // Now, display the link in the other modals
                modals.forEach(otherModalNumber => {
                    if (otherModalNumber !== modalNumber) {
                        const otherFileLinkContainer = document.getElementById(`${file.requirement_name}${otherModalNumber}Link`);
                        if (otherFileLinkContainer) {
                            otherFileLinkContainer.style.display = 'block';
                            otherFileLinkContainer.innerHTML = ''; // Clear any existing content
                            otherFileLinkContainer.appendChild(fileLink.cloneNode(true));
                        }
                    }
                });
            }
        });
    }
    

    // Fetch uploaded files when the page loads
    const procurementId = document.getElementById('procurementId')?.value;
    if (procurementId) {
        modals.forEach(modalNumber => {
            fetchUploadedFiles(procurementId, modalNumber);
        });
    }
});