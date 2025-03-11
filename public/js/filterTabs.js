document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-link');

    // Handle Tab Click Event
    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            const status = tab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase();

            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Fetch data with both year & status filtering
            const yearFilter = document.getElementById('year')?.value || '';
            fetchProcurementData(yearFilter, status); // Call function from fetchProcurement.js
        });
    });

    // Handle Year Dropdown Change
    document.getElementById('year')?.addEventListener('change', function () {
        const yearFilter = this.value;
        const activeTab = document.querySelector('.nav-link.active');
        const status = activeTab ? activeTab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase() : 'all';

        fetchProcurementData(yearFilter, status); // Call function from fetchProcurement.js
    });

    // Fetch all data initially (default: all statuses, all years)
    fetchProcurementData();
});
