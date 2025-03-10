document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('procurementTable');
    const tabs = document.querySelectorAll('.nav-link');

    // Fetch procurement data from backend
    async function fetchData() {
        try {
            const response = await fetch('fetchProcurementData.php'); // Update with your actual API route
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching procurement data:', error);
            return [];
        }
    }

    // Render table based on filter
    async function renderTable(filter = 'all') {
        tableBody.innerHTML = ''; // Clear table
        const data = await fetchData();

        data.forEach(item => {
            const statusClass = getStatusClass(item.status);

            // Only insert rows that match the selected filter
            if (filter === 'all' || statusClass === filter) {
                const row = `
                    <tr class="${statusClass}">
                        <td>${item.prNumber}</td>
                        <td>${item.activity}</td>
                        <td><span class="status-label ${statusClass}">${item.status}</span></td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            }
        });
    }

    // Function to classify statuses into categories
    function getStatusClass(status) {
        if (status.toLowerCase().includes('supply') || status.toLowerCase().includes('budget')) {
            return 'ongoing';
        } else if (status.toLowerCase().includes('overdue')) {
            return 'overdue';
        } else if (status.toLowerCase() === 'done') {
            return 'done';
        }
        return 'ongoing'; // Default for unclassified statuses
    }

    // Listen for tab clicks to trigger filtering
    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default Bootstrap behavior
            const filter = tab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase();

            // Remove active class from all tabs and add to clicked tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Render table with new filter
            renderTable(filter);
        });
    });

    // Load all data on initial page load
    renderTable();
});
