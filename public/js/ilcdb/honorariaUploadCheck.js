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

function incompFilesDisable() {
    const orsFiles = document.getElementById('orsFile');
    const dvFiles = document.getElementById('dvFile');
    const contractFiles = document.getElementById('contractFile');
    const classificationFiles = document.getElementById('classificationFile');
    const report = document.getElementById('reportFile');
    const attendance = document.getElementById('attendanceFile');
    const resume = document.getElementById('resumeFile');
    const govId = document.getElementById('govidFile');
    const paySlip = document.getElementById('payslipFile');
    const bank = document.getElementById('bankFile');
    const cert = document.getElementById('certFile');
    const save = document.getElementById('saveBtn');

    // Check if all file inputs have files selected
    if (
        !orsFiles?.files.length ||
        !dvFiles?.files.length ||
        !contractFiles?.files.length ||
        !classificationFiles?.files.length ||
        !report?.files.length ||
        !attendance?.files.length ||
        !resume?.files.length ||
        !govId?.files.length ||
        !paySlip?.files.length ||
        !bank?.files.length ||
        !cert?.files.length
    ) {
        save.disabled = true; // Disable the save button if any file input is empty
    } else {
        save.disabled = false; // Enable the save button if all file inputs have files
    }
}

// Add event listeners to file inputs to trigger the check when files are selected
document.addEventListener('DOMContentLoaded', () => {
    const fileInputs = document.querySelectorAll(
        '#orsFile, #dvFile, #contractFile, #classificationFile, #reportFile, #attendanceFile, #resumeFile, #govidFile, #payslipFile, #bankFile, #certFile'
    );

    fileInputs.forEach(input => {
        input.addEventListener('change', incompFilesDisable); // Re-check when a file is selected
    });

    incompFilesDisable(); // Initial check on page load
});