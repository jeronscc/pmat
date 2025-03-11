document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-link');

    // Fetch procurement data from backend
    async function fetchData(year = '', status = 'all') {
        let url = '/api/fetch-combined-procurement-data';

        // Append year filter if provided
        if (year !== '') {
            url += `?year=${year}`;
        }

        // Append status filter if it's not 'all'
        if (status !== 'all') {
            url += year !== '' ? `&status=${status}` : `?status=${status}`;
        }

        try {
            const response = await fetch(url);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching procurement data:', error);
            return [];
        }
    }

    // Render table based on filter
    async function renderTable(filter = 'all') {
        const yearFilter = document.getElementById('year')?.value || '';
        const data = await fetchData(yearFilter, filter);

        const tableBodies = {
            all: document.getElementById('procurementTableAll'),
            ongoing: document.getElementById('procurementTableOngoing'),
            overdue: document.getElementById('procurementTableOverdue'),
            done: document.getElementById('procurementTableDone')
        };

        // Clear all table bodies
        Object.values(tableBodies).forEach(tableBody => tableBody.innerHTML = '');

        data.forEach(item => {
            const statusClass = getStatusClass(item.status);
            const row = `
                <tr class="${statusClass}">
                    <td>${item.procurement_id}</td>
                    <td>${item.activity}</td>
                    <td><span class="status-label ${statusClass}">${item.status}</span></td>
                </tr>
            `;

            if (filter === 'all' || statusClass === filter) {
                tableBodies[filter].insertAdjacentHTML('beforeend', row);
            }
        });
    }

    // Function to classify statuses into categories
    function getStatusClass(status) {
        if (status.toLowerCase().includes('ongoing')) {
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

    // Handle Year Dropdown Change
    document.getElementById('year')?.addEventListener('change', function () {
        const yearFilter = this.value;
        const activeTab = document.querySelector('.nav-link.active');
        const status = activeTab ? activeTab.getAttribute('id').replace('-tab', '').replace('tab', '').toLowerCase() : 'all';

        renderTable(status);
    });

    // Load all data on initial page load
    renderTable();
});
