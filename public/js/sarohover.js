// SARO hover functionality
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/fetch-saro-ilcdb')
        .then(response => response.json())
        .then(data => {
            const saroContainer = document.querySelector('.saro-container');
            const remainingBalance = document.querySelector('.balance-container p');

            saroContainer.innerHTML = ''; // Clear any existing SARO entries

            if (data.length > 0) {
                data.forEach(saro => {
                    const saroElement = document.createElement('p');
                    saroElement.textContent = saro.saro_no;
                    saroElement.style.margin = '5px 0';
                    saroElement.style.padding = '5px';
                    saroElement.style.cursor = 'pointer';
                    saroElement.setAttribute('data-bs-toggle', 'tooltip');
                    saroElement.setAttribute('data-bs-placement', 'right');
                    saroElement.setAttribute('title', `Description: ${saro.description}`);

                    saroElement.addEventListener('click', function() {
                        remainingBalance.textContent = `₱${Number(saro.current_budget).toLocaleString()}`;
                        fetchProcurementData(saro.saro_no); // Fetch and display procurement data for selected SARO
                    });

                    saroContainer.appendChild(saroElement);
                });

                // Initialize Bootstrap tooltips with a short delay
                setTimeout(reinitializeTooltips, 100);
            } else {
                const emptyMessage = document.createElement('p');
                emptyMessage.textContent = 'No SARO records found.';
                emptyMessage.style.margin = "5px 0";
                emptyMessage.style.padding = "5px";
                saroContainer.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
});

// Function to initialize Bootstrap tooltips
function reinitializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Reinitialize tooltips when hovering over SARO items (Delegated event)
document.addEventListener('mouseover', function(event) {
    if (event.target.matches('.saro-item')) {
        const tooltip = bootstrap.Tooltip.getInstance(event.target) || new bootstrap.Tooltip(event.target);
        tooltip.show();
    }
});
