document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('saveBtn').addEventListener('click', function () {
        const form = document.getElementById('requirementsForm');
        const formData = new FormData(form);

        const procurementId = document.getElementById('procurement_id')?.value;
        if (procurementId) {
            formData.append('procurement_id', procurementId);
        } else {
            alert('Procurement ID is missing. Please check the form.');
            return;
        }

        console.log("Sending form data:", [...formData.entries()]); // ✅ Log form data before sending

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const uploadUrl = window.location.origin + "/requirements/upload";

        fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.text()) // ✅ Read as text first
        .then(text => {
            console.log("Server response:", text); // ✅ Log raw response

            try {
                const data = JSON.parse(text); // ✅ Try parsing JSON
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Failed to upload: " + (data.message || "Unknown error."));
                }
            } catch (error) {
                console.error("Response is not valid JSON:", text); // ✅ Log invalid response
                alert("Upload failed. Server returned an unexpected response.");
            }
        })
        .catch(error => {
            console.error("Error during upload:", error);
            alert("Upload failed. Check console for details.");
        });
    });
});
