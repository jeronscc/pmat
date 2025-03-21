function openAddSaroModal() {
    new bootstrap.Modal(document.getElementById("addSaroModal")).show();
}

function fetchSaroData(year) {
    // Reset the balance display to "₱0" before fetching SAROs
    document.getElementById("remainingBalance").textContent = "₱0";

    // If no year is selected, fetch all SAROs, otherwise filter by year
    const url =
        year === ""
            ? "/api/fetch-saro-ilcdb"
            : `/api/fetch-saro-ilcdb?year=${year}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            const saroList = document.querySelector(".saro-list");
            saroList.innerHTML = ""; // Clear previous entries

            if (data.length > 0) {
                data.forEach((saro) => {
                    // Create list item for each SARO number
                    const listItem = document.createElement("li");
                    listItem.classList.add("list-group-item");
                    listItem.textContent = `${saro.saro_no}`;

                    // Store budget_allocated as a tooltip
                    listItem.setAttribute("data-bs-toggle", "tooltip");
                    listItem.setAttribute("data-bs-placement", "right");
                    listItem.setAttribute(
                        "title",
                        `Budget Allocated: ₱${saro.budget_allocated.toLocaleString()}`
                    );

                    // Add click event to each SARO number
                    listItem.addEventListener("click", function () {
                        displayCurrentBudget(saro); // Show balance when SARO is clicked
                        highlightSelectedItem(this);
                    });

                    // Append the list item to the SARO list
                    saroList.appendChild(listItem);
                });

                // Initialize Bootstrap tooltips
                new bootstrap.Tooltip(document.body, {
                    selector: '[data-bs-toggle="tooltip"]',
                });
            } else {
                // Show message if no SARO data is available
                const emptyMessage = document.createElement("li");
                emptyMessage.classList.add("list-group-item");
                emptyMessage.textContent = "No SARO records found.";
                saroList.appendChild(emptyMessage);
            }
        })
        .catch((error) => console.error("Error fetching SARO data:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    fetchProcurementForSaro(""); // Fetch all requirements by default
});
// Function to display the remaining balance for the clicked SARO
function displayCurrentBudget(saro) {
    // Set the current SARO name in the container
    document.getElementById("currentViewingSaro").textContent = `${saro.saro_no}`;
    document.getElementById("currentSaroName").textContent = `${saro.saro_no}`;

    // Check if current_budget exists and format it with comma separation
    const currentBudget = saro.current_budget
        ? `₱${formatNumberWithCommas(saro.current_budget)}`
        : "₱0";

    // Display the current budget in the "remainingBalance" container
    document.getElementById("remainingBalance").textContent = currentBudget;

    // Fetch and display the requirements associated with the selected SARO
    fetchProcurementForSaro(saro.saro_no);

    // Fetch and display NTCA records for the selected SARO
    fetchNTCAForSaro(saro.saro_no);

    // Fetch NTCA balance and breakdown for the current quarter
    if (saro.ntca_no && saro.current_quarter) {
        fetchNTCABalance(saro.ntca_no, saro.current_quarter);
        fetchNTCABreakdown(saro.ntca_no);
    }
}

function fetchSaroDataAndRequirements(year) {
    const url =
        year === ""
            ? "/api/fetch-saro-ilcdb"
            : `/api/fetch-saro-ilcdb?year=${year}`;

    fetch(url)
        .then((response) => response.json())
        .then((saros) => {
            const saroList = document.querySelector(".saro-list");
            saroList.innerHTML = ""; // Clear previous entries

            if (saros.length > 0) {
                saros.forEach((saro) => {
                    const listItem = document.createElement("li");
                    listItem.classList.add("list-group-item");
                    listItem.textContent = `${saro.saro_no}`;

                    listItem.addEventListener("click", function () {
                        displayCurrentBudget(saro);
                        fetchProcurementRequirements(saro.saro_no);
                    });

                    saroList.appendChild(listItem);
                });
            } else {
                const emptyMessage = document.createElement("li");
                emptyMessage.classList.add("list-group-item", "empty-message");
                emptyMessage.textContent = "No SARO records found.";
                saroList.appendChild(emptyMessage);
            }
        })
        .catch((error) => console.error("Error fetching SARO data:", error));
}

window.fetchSaroDataAndRequirements = fetchSaroDataAndRequirements;

function fetchNTCAForSaro(saroNo) {
    fetch(`/api/fetch-ntca-by-saro/${saroNo}`)
        .then((response) => response.json())
        .then((data) => {
            const ntcaList = document.getElementById("ntcaBreakdownList");
            ntcaList.innerHTML = ""; // Clear existing NTCA records

            if (data.success) {
                data.ntca.forEach((ntca) => {
                    ntcaList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Unassigned Budget for ${
                            ntca.first_q
                        }<span class="fw-bold">${
                        ntca.current_budget
                            ? "₱" + ntca.current_budget.toLocaleString()
                            : "<em style='color:#777;'>Not yet allocated</em>"
                    }</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter: 
                        <span class="fw-bold">${
                            ntca.first_q
                                ? "₱" + ntca.first_q.toLocaleString()
                                : "<em style='color:#777;'>Not yet allocated</em>"
                        }</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter: 
                        <span class="fw-bold">${
                            ntca.second_q
                                ? "₱" + ntca.second_q.toLocaleString()
                                : "<em style='color:#777;'>Not yet allocated</em>"
                        }</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter: 
                        <span class="fw-bold">${
                            ntca.third_q
                                ? "₱" + ntca.third_q.toLocaleString()
                                : "<em style='color:#777;'>Not yet allocated</em>"
                        }</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter: 
                        <span class="fw-bold">${
                            ntca.fourth_q
                                ? "₱" + ntca.fourth_q.toLocaleString()
                                : "<em style='color:#777;'>Not yet allocated</em>"
                        }</span>
                    </li>
                `;
                });
            } else {
                ntcaList.innerHTML = `
                    <li class="list-group-item text-danger">${data.message}</li>
                `;
            }
        })
        .catch((error) => {
            console.error("Error fetching NTCA records:", error);
        });
}

function fetchNTCABreakdown(ntcaNo) {
    if (!ntcaNo) {
        console.error('NTCA No. is missing.');
        return; // Prevent further execution if NTCA No. is invalid
    }

    fetch(`/api/ntca-breakdown/${ntcaNo}`)
        .then(response => response.json())
        .then(data => {
            const breakdownList = document.getElementById('ntcaBreakdownList');
            breakdownList.innerHTML = ''; // Clear existing items

            if (data.success) {
                const { ntca_no, first_q, second_q, third_q, fourth_q, current_budget, total_quarters } = data.ntca;

                // Add NTCA No.
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>NTCA No:</strong> <span>${ntca_no}</span>
                    </li>
                `;

                // Add Unclaimed NTCA Budget
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Unclaimed NTCA Budget <span class="fw-bold text-success">₱${current_budget.toLocaleString()}</span>
                    </li>
                `;

                // Add balances for each quarter
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter <span class="fw-bold">₱${first_q.toLocaleString()}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter <span class="fw-bold">₱${second_q.toLocaleString()}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter <span class="fw-bold">₱${third_q.toLocaleString()}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter <span class="fw-bold">₱${fourth_q.toLocaleString()}</span>
                    </li>
                `;

                // Add total of all quarters
                breakdownList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        Total of All Quarters <span class="fw-bold text-primary">₱${total_quarters.toLocaleString()}</span>
                    </li>
                `;
            } else {
                breakdownList.innerHTML = `
                    <li class="list-group-item text-danger">${data.message}</li>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching NTCA breakdown:', error);
        });
}

function fetchNTCABalance(ntcaNo, quarter) {
    if (!ntcaNo) {
        console.error('NTCA No. is missing.');
        return; // Prevent further execution if NTCA No. is invalid
    }

    if (!quarter) {
        console.error('Quarter is missing.');
        return; // Prevent further execution if Quarter is invalid
    }

    fetch(`/api/ntca-balance/${ntcaNo}/${quarter}`)
        .then(response => response.json())
        .then(data => {
            const ntcaBalanceElement = document.getElementById('ntcaBalance');
            const ntcaLabelElement = document.getElementById('ntcaLabel'); // Add a label for NTCA

            if (data.success) {
                const { balance } = data;

                // Update NTCA label to include NTCA No. and Quarter
                ntcaLabelElement.textContent = `Unassigned Budget for NTCA (${ntcaNo})`;

                // Update NTCA balance for the current quarter
                ntcaBalanceElement.textContent = `₱${balance.toLocaleString()}`;
            } else {
                ntcaLabelElement.textContent = `Unassigned Budget for NTCA (${ntcaNo})`;
                ntcaBalanceElement.textContent = '₱0';
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching NTCA balance:', error);
            document.getElementById('ntcaBalance').textContent = '₱0';
        });
}

// Trigger NTCA breakdown fetch when the modal is opened
document.getElementById('ntcaBreakdownModal').addEventListener('shown.bs.modal', function () {
    const ntcaNo = document.getElementById('ntca_number').value; // Replace with the actual NTCA number
    if (!ntcaNo) {
        console.error('NTCA No. is missing.');
        return;
    }
    console.log(`Fetching NTCA breakdown for NTCA No: ${ntcaNo}`);
    fetchNTCABreakdown(ntcaNo);
});
