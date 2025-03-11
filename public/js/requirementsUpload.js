document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('saveBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('requirementsForm'));

        // Ensure the base URL is correct
        const baseUrl = window.location.origin;
        const uploadUrl = `${baseUrl}/requirements/upload`; // Ensure no extra slashes

        fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to upload requirements.');
            }
        })
        .catch(error => {
            console.error('Error during upload:', error);
            alert('Failed to upload requirements.');
        });
    });
});