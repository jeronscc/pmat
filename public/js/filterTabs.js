document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-link');

    // Fetch procurement data from backend
    async function fetchData(filter = 'all') {
        try {
            const response = await fetch(`/api/fetch-procurement-by-status?status=${filter}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching procurement data:', error);
            return [];
        }
    }    

    // Render table based on filter
    async function renderTable(filter = 'all') {
        const tableBodies = {
            all: document.getElementById('procurementTableAll'),
            ongoing: document.getElementById('procurementTableOngoing'),
            overdue: document.getElementById('procurementTableOverdue'),
            done: document.getElementById('procurementTableDone')
        };
    
        // Clear all table bodies
        Object.values(tableBodies).forEach(body => (body.innerHTML = ''));
    
        const data = await fetchData(filter); // Fetch filtered data
    
        data.forEach(item => {
            const statusClass = getStatusClass(item.status);
            const row = `
                <tr class="${statusClass}">
                    <td>${item.procurement_id}</td>
                    <td>${item.activity}</td>
                    <td><span class="status-label ${statusClass}">${item.status}</span></td>
                    <td>
                        <button class="btn ${item.status.toLowerCase() === 'done' ? 'btn-secondary' : 'btn-success'}" 
                            ${item.status.toLowerCase() === 'done' ? 'disabled' : ''} 
                            onclick="editProcurement('${item.procurement_id}')">
                            ${item.status.toLowerCase() === 'done' ? 'Completed' : 'Edit'}
                        </button>
                    </td>
                </tr>
            `;
    
            // Insert row into the "All" tab and respective status tab
            tableBodies.all.insertAdjacentHTML('beforeend', row);
            if (tableBodies[statusClass]) {
                tableBodies[statusClass].insertAdjacentHTML('beforeend', row);
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

    // Load all data on initial page load
    renderTable();
});

// Function to handle editing procurement
function editProcurement(procurementId) {
    window.location.href = `/procurementform/${procurementId}`;
}