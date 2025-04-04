function getCurrentQuarter(ntca) {
    if (ntca.fourth_q > 0) return 'fourth_q';
    if (ntca.third_q > 0) return 'third_q';
    if (ntca.second_q > 0) return 'second_q';
    if (ntca.first_q > 0) return 'first_q';
    return null; // No quarter has a value
}

function fetchAndRenderSaroData(apiUrl, panelSelector, balanceSelector, procurementApiUrl, ntcaApiUrl, procurementDetailsApiUrl, year = '') {
    // Append the year as a query parameter if provided
    const urlWithYear = year ? `${apiUrl}?year=${year}` : apiUrl;

    fetch(urlWithYear)
        .then(response => response.json())
        .then(data => {
            const panel = document.querySelector(panelSelector);
            const remainingBalance = document.querySelector(balanceSelector);
            panel.innerHTML = ''; // Clear any existing SARO entries

            if (data.length > 0) {
                data.forEach(saro => {
                    const saroElement = document.createElement('p');
                    saroElement.textContent = saro.saro_no;
                    saroElement.style.margin = '5px 0';
                    saroElement.style.padding = '5px';
                    saroElement.setAttribute('data-saro-no', saro.saro_no); // Attach SARO number
                    saroElement.setAttribute('data-bs-toggle', 'tooltip');
                    saroElement.setAttribute('data-bs-placement', 'right');
                    saroElement.setAttribute('title', `Description: ${saro.description}, Budget Allocated: ₱${Number(saro.current_budget).toLocaleString()}`);
                    saroElement.addEventListener('click', function () {
                        document.getElementById("currentSaroNo").textContent = `${saro.saro_no} Remaining Balance: `;
                        document.getElementById("viewingSaroNo").textContent = `Currently Viewing: ${saro.saro_no}`;
                        remainingBalance.textContent = `₱${Number(saro.current_budget).toLocaleString()}`;

                        // Store the procurement details API URL for later use
                        window.currentProcurementDetailsApiUrl = procurementDetailsApiUrl;
                        
                        fetchProcurementData(saro.saro_no, procurementApiUrl, 'all'); // Fetch procurement data for the selected SARO
                        fetchNTCAForSaro(saro.saro_no, ntcaApiUrl); // Fetch NTCA data for the selected SARO
                    });
                    panel.appendChild(saroElement);
                });

                // Initialize Bootstrap tooltips
                initializeTooltips();
            } else {
                const emptyMessage = document.createElement('p');
                emptyMessage.textContent = 'No SARO records found.';
                emptyMessage.style.margin = '5px 0';
                emptyMessage.style.padding = '5px';
                panel.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
}

function filterSaroByYear(year) {
    // Call fetchAndRenderSaroData for each module with the selected year
    fetchAndRenderSaroData(
        '/api/fetch-saro-ilcdb', // SARO API URL for ILCDB
        '.panel.ilcdb',          // Panel selector for ILCDB
        '.balance-box p',        // Balance selector
        '/api/fetch-procurement-ilcdb', // Procurement API URL for ILCDB
        '/api/fetch-ntca-by-saro', // NTCA API URL
        year                     // Selected year
    );

    fetchAndRenderSaroData(
        '/api/dtc/fetch-saro-dtc', // SARO API URL for DTC
        '.panel.dtc',          // Panel selector for DTC
        '.balance-box p',      // Balance selector
        '/api/dtc/fetch-procurement-dtc', // Procurement API URL for DTC
        '/api/dtc/fetch-ntca-by-saro', // NTCA API URL
        year                   // Selected year
    );

    fetchAndRenderSaroData(
        '/api/click/fetch-saro-click', // SARO API URL for CLICK
        '.panel.project-click',  // Panel selector for CLICK
        '.balance-box p',        // Balance selector
        '/api/click/fetch-procurement-click', // Procurement API URL for CLICK
        '/api/click/fetch-ntca-by-saro', // NTCA API URL
        year                     // Selected year
    );

    fetchAndRenderSaroData(
        '/api/spark/fetch-saro-spark', // SARO API URL for SPARK
        '.panel.spark',          // Panel selector for SPARK
        '.balance-box p',        // Balance selector
        '/api/spark/fetch-procurement-spark', // Procurement API URL for SPARK
        '/api/spark/fetch-ntca-by-saro', // NTCA API URL
        year                     // Selected year
    );
}

function fetchProcurementData(saroNo, baseApiUrl) {
    fetch(`${baseApiUrl}?saro_no=${saroNo}`)
        .then(response => response.json())
        .then(data => {
            // Store the data globally so we can use it for filtering
            window.procurementData = data;
            
            // Initialize tabs
            initializeTabs();
            
            // Initially display all data
            displayProcurementData('all');
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}

function initializeTabs() {
    // Add event listeners to tabs
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Get the target tab ID
            const targetId = this.getAttribute('href').substring(1);
            
            // Determine filter based on tab ID
            let filter = 'all';
            switch(targetId) {
                case 'tabPending':
                    filter = 'pending';
                    break;
                case 'tabOngoing':
                    filter = 'ongoing';
                    break;
                case 'tabOverdue':
                    filter = 'overdue';
                    break;
                case 'tabDone':
                    filter = 'done';
                    break;
                default:
                    filter = 'all';
            }
            
            // Display filtered data
            displayProcurementData(filter);
        });
    });
}

function displayProcurementData(filter) {
    const procurementTable = document.getElementById('procurementTable');
    procurementTable.innerHTML = ''; // Clear any existing rows
    
    if (!window.procurementData || window.procurementData.length === 0) {
        const emptyMessage = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.setAttribute('colspan', '4');
        emptyCell.textContent = 'No procurement records found.';
        emptyMessage.appendChild(emptyCell);
        procurementTable.appendChild(emptyMessage);
        return;
    }
    
    // Filter the data based on selected tab
    const filteredData = window.procurementData.filter(item => {
        const status = (item.status || '').toLowerCase();
        const honorariaStatus = (item.honoraria_status || '').toLowerCase();
        
        // Use honoraria status if available and not "no status"
        const effectiveStatus = (honorariaStatus !== 'no status' && honorariaStatus !== '') 
            ? honorariaStatus.toLowerCase() 
            : status.toLowerCase();
        
        if (filter === 'all') {
            return true;
        } else if (filter === 'pending') {
            return effectiveStatus.includes('pending');
        } else if (filter === 'ongoing') {
            return effectiveStatus.includes('ongoing');
        } else if (filter === 'overdue') {
            return effectiveStatus.includes('overdue');
        } else if (filter === 'done') {
            return effectiveStatus === 'done';
        }
        return false;
    });
    
    if (filteredData.length > 0) {
        filteredData.forEach(item => {
            const row = document.createElement('tr');
            row.setAttribute('data-procurement-id', item.procurement_id);

            // PR NUMBER cell
            const prNumberCell = document.createElement('td');
            prNumberCell.textContent = item.procurement_id;
            row.appendChild(prNumberCell);

            // CATEGORY cell
            const categoryCell = document.createElement('td');
            categoryCell.textContent = item.procurement_category;
            row.appendChild(categoryCell);

            // ACTIVITY cell
            const activityCell = document.createElement('td');
            activityCell.textContent = item.activity;
            row.appendChild(activityCell);

            // STATUS & UNIT cell
            const statusCell = document.createElement('td');
            const badge = document.createElement('span');

            let statusMessage = item.status || '';
            let unitMessage = item.unit ? ` at ${item.unit}` : '';

            // Use honoraria status if available
            if (item.honoraria_status && item.honoraria_status.toLowerCase() !== 'no status') {
                statusMessage = item.honoraria_status;
            }

            // If status is "done", remove the unit part
            if (statusMessage.toLowerCase() === 'done') {
                unitMessage = '';
            }

            badge.className = getStatusClass(statusMessage || '');
            badge.textContent = statusMessage + unitMessage;

            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            procurementTable.appendChild(row);
        });
    } else {
        const emptyMessage = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.setAttribute('colspan', '4');
        emptyCell.textContent = 'No records found.';
        emptyMessage.appendChild(emptyCell);
        procurementTable.appendChild(emptyMessage);
    }
}

function fetchNTCAForSaro(saroNo, ntcaApiUrl) {
    console.log(`Fetching NTCA for SARO: ${saroNo}`); // Debugging
    fetch(`${ntcaApiUrl}/${saroNo}`)
        .then(response => response.json())
        .then(data => {
            console.log('NTCA API Response:', data); // Debugging
            const ntcaList = document.getElementById("ntcaBreakdownList");
            const ntcaLabelElement = document.getElementById("ntcaLabel");
            const ntcaBalanceElement = document.getElementById("ntcaBalance");
            ntcaList.innerHTML = ''; // Clear existing NTCA records

            if (data.success) {
                data.ntca.forEach(ntca => {
                    // Determine the current quarter dynamically
                    const currentQuarter = getCurrentQuarter(ntca);

                    // Update NTCA container label and balance for the current quarter
                    ntcaLabelElement.textContent = `NTCA (${ntca.ntca_no} - ${currentQuarter ? currentQuarter : 'No Quarter'}): `;
                    const currentQuarterBalance = currentQuarter ? ntca[currentQuarter] : 0;
                    ntcaBalanceElement.textContent = currentQuarterBalance
                        ? `₱${Number(currentQuarterBalance).toLocaleString()}`
                        : "₱0";
                    console.log(`Updated NTCA Balance: ${ntcaBalanceElement.textContent}`); // Debugging

                    // Add NTCA breakdown to the list
                    ntcaList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>SARO Budget Allocated:</strong>
                        <span class="fw-bold">
                            ${ntca.saro_budget ? "₱" + ntca.saro_budget.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>NTCA Budget Allocated:</strong>
                        <span class="fw-bold">
                            ${ntca.ntca_budget ? "₱" + ntca.ntca_budget.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Unassigned Budget for NTCA (${ntca.ntca_no}):</strong>
                        <span class="fw-bold">
                            ${ntca.current_budget ? "₱" + ntca.current_budget.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter: 
                        <span class="fw-bold">
                            ${ntca.first_q ? "₱" + Number(ntca.first_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter: 
                        <span class="fw-bold">
                            ${ntca.second_q ? "₱" + Number(ntca.second_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter: 
                        <span class="fw-bold">
                            ${ntca.third_q ? "₱" + Number(ntca.third_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter: 
                        <span class="fw-bold">
                            ${ntca.fourth_q ? "₱" + Number(ntca.fourth_q).toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                `;
                });
            } else {
                ntcaList.innerHTML = `
                    <li class="list-group-item text-danger">${data.message}</li>
                `;
                ntcaLabelElement.textContent = "NTCA:";
                ntcaBalanceElement.textContent = "₱0";
                console.log('No NTCA data found'); // Debugging
            }
        })
        .catch(error => {
            console.error("Error fetching NTCA records:", error);
            const ntcaBalanceElement = document.getElementById("ntcaBalance");
            ntcaBalanceElement.textContent = "₱0"; // Default in case of an error
        });
}

function getStatusClass(status) {
    const baseClass = "custom-font-size"
    switch (status.toLowerCase()) {
        case 'pending':
            return `badge bg-secondary text-white p-2 ${baseClass}`; // Gray for pending
        case 'ongoing':
            return `badge bg-warning text-dark p-2  ${baseClass}`; // Orangeish yellow for ongoing
        case 'done':
            return `badge bg-success text-white p-2  ${baseClass}`; // Green for done
        case 'overdue':
            return `badge bg-danger text-white p-2 ${baseClass}`; // Red for overdue
        default:
            return `badge bg-light text-dark p-2 ${baseClass}`; // Default for unknown status
    }
}

document.addEventListener('click', function (event) {
    const row = event.target.closest('tr'); // Get the clicked row
    if (row && row.dataset.procurementId) {
        openProcurementModal({ procurement_id: row.dataset.procurementId });
    }
});

let bootstrapModalInstance = null;

// Function to open modal and display procurement details
function openProcurementModal(item) {
    const procurementId = item.procurement_id;
    const modal = document.getElementById('procurementDetailsModal');

    if (!modal) {
        console.error("Modal element not found.");
        return;
    }

    // Use the stored API URL
    const detailsApiUrl = window.currentProcurementDetailsApiUrl;
    const url = `${detailsApiUrl}?procurement_id=${procurementId}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message); // Show error if procurement is not found
            } else {
                // Populate modal fields
                document.getElementById('modalProcurementCategory').textContent = data.procurement_category || 'N/A';
                document.getElementById('modalProcurementNo').textContent = data.procurement_id || 'N/A';
                document.getElementById('modalSaroNo').textContent = data.saro_no || 'N/A';
                document.getElementById('modalNTCANo').textContent = data.ntca_no || 'N/A';
                document.getElementById('modalQuarter').textContent = data.quarter || 'N/A';
                document.getElementById('modalPurchaseRequest').textContent = data.pr_amount || 'N/A';
                document.getElementById('modalYear').textContent = data.year || 'N/A';
                document.getElementById('modalDescription').textContent = data.description || 'N/A';
                document.getElementById('modalActivity').textContent = data.activity || 'N/A';

                // Change the label for "Activity" based on the procurement category
                const activityLabel = document.getElementById('modalActivityLabel');
                const category = data.procurement_category.toLowerCase();
                console.log("Procurement category:", category); // Debugging log

                if (category === 'honoraria') {
                    activityLabel.textContent = 'Speaker:';
                } else if (category === 'daily travel expense') {
                    activityLabel.textContent = 'Traveller:';
                } else {
                    activityLabel.textContent = 'Activity:';
                }

                // Initialize Bootstrap modal only once
                if (!bootstrapModalInstance) {
                    bootstrapModalInstance = new bootstrap.Modal(modal);
                }

                // Show the modal
                bootstrapModalInstance.show();
            }
        })
        .catch(error => {
            console.error('Error fetching procurement details:', error);
            alert('Failed to load procurement details.');
        });
}

// Ensure modal closes when the close button is clicked
document.getElementById('closeModalBtn').addEventListener('click', function () {
    if (bootstrapModalInstance) {
        bootstrapModalInstance.hide();
    }
});

function initializeTooltips() {
    const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    existingTooltips.forEach(tooltipTriggerEl => {
        const tooltipInstance = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (tooltipInstance) {
            tooltipInstance.dispose();
        }
    });

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}