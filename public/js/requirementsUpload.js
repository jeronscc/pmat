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

        fetch('/requirements/upload', {
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
                    location.reload();
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
