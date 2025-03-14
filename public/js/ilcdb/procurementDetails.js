document.addEventListener('DOMContentLoaded', function () {
    // Function to open the procurement details modal
    function openProcurementDetailsModal(procurement) {
        // Set the modal content
        document.getElementById('modalProcurementCategory').textContent = procurement.category;
        document.getElementById('modalProcurementNo').textContent = procurement.procurement_no;
        document.getElementById('modalSaroNo').textContent = procurement.saro_no;
        document.getElementById('modalYear').textContent = procurement.year;
        document.getElementById('modalDescription').textContent = procurement.description;
        document.getElementById('modalActivity').textContent = procurement.activity;

        // Change the label for "Activity" based on the procurement category
        const activityLabel = document.getElementById('modalActivityLabel');
        if (procurement.category.toLowerCase() === 'honoraria') {
            activityLabel.textContent = 'Speaker:';
        } else if (procurement.category.toLowerCase() === 'other expense') {
            activityLabel.textContent = 'Traveller:';
        } else {
            activityLabel.textContent = 'Activity:';
        }

        // Show the modal
        const procurementDetailsModal = new bootstrap.Modal(document.getElementById('procurementDetailsModal'));
        procurementDetailsModal.show();
    }

});