document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('saveBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('requirementsForm'));

        fetch('/requirements/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to upload requirements.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to upload requirements.');
        });
    });
});