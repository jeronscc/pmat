function filterProcurementByCategory(category) {
    fetch(`/api/fetch-procurement-ilcdb?category=${category}`)
        .then(response => response.json())
        .then(data => {
            const procurementTable = document.getElementById('procurementTable');
            procurementTable.innerHTML = ''; // Clear any existing rows

            if (data.length > 0) {
                data.forEach(procurement => {
                    const row = document.createElement('tr');

                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = procurement.procurement_id;
                    prNumberCell.addEventListener('click', function() {
                        window.location.href = `/procurementform/${procurement.id}`;
                    });
                    row.appendChild(prNumberCell);

                    const activityCell = document.createElement('td');
                    activityCell.textContent = procurement.activity;
                    row.appendChild(activityCell);

                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; // Placeholder status
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    procurementTable.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                procurementTable.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}