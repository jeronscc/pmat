document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('saveBtn').addEventListener('click', function () {
        const form = document.getElementById('requirementsForm');
        const formData = new FormData(form);

        // ✅ Manually append procurement_id if it's missing
        const procurementId = document.getElementById('procurement_id')?.value;
        if (procurementId) {
            formData.append('procurement_id', procurementId);
        } else {
            alert('Procurement ID is missing. Please check the form.');
            return;
        }

        // Log form data to check if procurement_id is included
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }

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
                location.reload();
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
