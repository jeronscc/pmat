async function requirementsUpload(formData) {
    try {
        const response = await fetch('/requirements/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
        } else {
            alert('Upload failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error uploading files:', error);
        alert('An error occurred while uploading files.');
    }
}
