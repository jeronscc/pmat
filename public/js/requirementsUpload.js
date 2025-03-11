document.addEventListener('DOMContentLoaded', function () {
    const uploadBtn = document.getElementById('saveBtn');
    const saveChangesBtn = document.getElementById('saveChanges'); // ✅ Save button to be enabled only when all files are uploaded
    const requiredFiles = [
        'orsFile', 'dvFile', 'contractFile', 'classificationFile', 'reportFile',
        'attendanceFile', 'resumeFile', 'govidFile', 'payslipFile', 'bankFile', 'certFile'
    ];

    uploadBtn.addEventListener('click', function () {
        const form = document.getElementById('requirementsForm');
        const formData = new FormData(form);

        const procurementId = document.getElementById('procurement_id')?.value;
        if (procurementId) {
            formData.append('procurement_id', procurementId);
        } else {
            alert('Procurement ID is missing. Please check the form.');
            return;
        }

        // ✅ Check if all required files are provided
        let missingFiles = [];
        requiredFiles.forEach(file => {
            if (!formData.get(file) || formData.get(file).size === 0) {
                missingFiles.push(file);
            }
        });

        if (missingFiles.length > 0) {
            alert("Please upload all required files before saving:\n" + missingFiles.join(', '));
            return;
        }

        console.log("Sending form data:", [...formData.entries()]); // ✅ Log form data

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const uploadUrl = window.location.origin + "/requirements/upload";

        fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);

                // ✅ Enable Save button once all files are uploaded successfully
                saveChangesBtn.removeAttribute('disabled');
            } else {
                alert("Failed to upload: " + (data.message || "Unknown error."));
            }
        })
        .catch(error => {
            console.error("Error during upload:", error);
            alert("Upload failed. Check console for details.");
        });
    });
});
