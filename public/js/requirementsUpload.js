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
            return response.text(); // Read response as text
        })
        .then(text => {
            let data;
            try {
                data = JSON.parse(text); // Try to parse as JSON
            } catch (error) {
                throw new Error('Failed to parse JSON response');
            }
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