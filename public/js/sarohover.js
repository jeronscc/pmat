document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/fetch-saro-ilcdb')
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            const remainingBalance = document.getElementById('remainingBalance');
            saroList.innerHTML = ''; // Clear any existing SARO entries

            if (data.length > 0) {
                data.forEach(saro => {
                    const saroElement = document.createElement('li');
                    saroElement.classList.add('list-group-item');
                    saroElement.textContent = saro.saro_no;
                    saroElement.style.margin = '5px 0'; // 10px space above & below
                    saroElement.style.padding = '5px';  
                    saroElement.setAttribute('data-bs-toggle', 'tooltip');
                    saroElement.setAttribute('data-bs-placement', 'right');
                    saroElement.setAttribute('title', `Description: ${saro.description}`);
                    saroElement.addEventListener('click', function() {
                        remainingBalance.textContent = `₱${Number(saro.current_budget).toLocaleString()}`;
                        fetchProcurementData(saro.saro_no);
                    });
                    saroList.appendChild(saroElement);
                });

                // Initialize Bootstrap tooltips
                initializeTooltips();
            } else {
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found.';
                emptyMessage.style.margin = "5px 0";
                emptyMessage.style.padding = "5px"
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
});

function filterSaroByYear(year) {
    fetch(`/api/fetch-saro-ilcdb?year=${year}`)
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            const remainingBalance = document.getElementById('remainingBalance');
            saroList.innerHTML = ''; // Clear previous records

            if (data.length > 0) {
                data.forEach(saro => {
                    const saroElement = document.createElement('li');
                    saroElement.classList.add('list-group-item');
                    saroElement.textContent = saro.saro_no;
                    saroElement.style.margin = '5px 0'; 
                    saroElement.style.padding = '5px';
                    saroElement.setAttribute('data-bs-toggle', 'tooltip');
                    saroElement.setAttribute('data-bs-placement', 'right');
                    saroElement.setAttribute('title', `Description: ${saro.description}`);
                    saroElement.addEventListener('click', function() {
                        remainingBalance.textContent = `₱${Number(saro.current_budget).toLocaleString()}`;
                        fetchProcurementData(saro.saro_no);
                    });
                    saroList.appendChild(saroElement);
                });

                // Initialize Bootstrap tooltips
                initializeTooltips();
            } else {
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
}

function initializeTooltips() {
    // Dispose of existing tooltips
    const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    existingTooltips.forEach(tooltipTriggerEl => {
        const tooltipInstance = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (tooltipInstance) {
            tooltipInstance.dispose();
        }
    });

    // Initialize new tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function fetchProcurementData(saroNo) {
    fetch(`/api/fetch-procurement-ilcdb?saro_no=${saroNo}`)
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

document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const query = document.getElementById('searchBar').value;
    searchProcurement(query);
});

function searchProcurement(query) {
    fetch(`/api/search-procurement-ilcdb?query=${query}`)
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
        .catch(error => console.error('Error searching procurement data:', error));
}