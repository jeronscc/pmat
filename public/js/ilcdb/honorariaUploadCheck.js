function uploadCompleteCheck() {
    const procurementIdElement = document.getElementById('procurement_id');
    if (!procurementIdElement) {
        console.error('Procurement ID element not found!');
        return;
    }

    const procurementId = procurementIdElement.value;
    console.log('Procurement ID:', procurementId);  // Check the ID value

    if (!procurementId) {
        console.error('Procurement ID is missing!');
        return;
    }

    const dateSubmitted = document.getElementById('dateSubmitted');
    const dateReturned = document.getElementById('dateReturned');

    fetch(`/api/uploadedHonorariaFilesCheck/${procurementId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const requirementsStatus = result.requirementsStatus;
                console.log('Requirements Status:', requirementsStatus);

                if (requirementsStatus === 1) {
                    dateSubmitted.disabled = false;
                    dateReturned.disabled = false;
                } 
                else if (requirementsStatus === 0) {
                    dateSubmitted.disabled = true;
                    dateReturned.disabled = true;
                } 
                else {
                    console.error('Error fetching missing files:', result.message);
                    alert('An error occurred while checking the upload status.');
                }
            } 
            else {
                console.error('Error fetching missing files:', result.message);
                alert('An error occurred while checking the upload status.');
            }
        })
        .catch(error => {
            console.error('Error fetching missing files:', error);
            alert('An error occurred while checking the upload status.');
        });
}

document.addEventListener('DOMContentLoaded', () => {
    uploadCompleteCheck();
});