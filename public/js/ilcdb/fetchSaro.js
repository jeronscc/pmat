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

// Helper function to determine the current quarter
function getCurrentQuarter() {
    const month = new Date().getMonth() + 1; // Months are 0-based, so add 1
    if (month <= 3) return 'first_q';
    if (month <= 6) return 'second_q';
    if (month <= 9) return 'third_q';
    return 'fourth_q';
}

function fetchNTCAForSaro(saroNo) {
    fetch(`/api/fetch-ntca-by-saro/${saroNo}`)
        .then((response) => response.json())
        .then((data) => {
            const ntcaList = document.getElementById("ntcaBreakdownList");
            const ntcaLabelElement = document.getElementById("ntcaLabel");
            const ntcaBalanceElement = document.getElementById("ntcaBalance");
            ntcaList.innerHTML = ""; // Clear existing NTCA records

            if (data.success) {
                const currentMonth = new Date().getMonth() + 1;
                const currentQuarter = currentMonth <= 3 ? 'First Quarter' :
                                       currentMonth <= 6 ? 'Second Quarter' :
                                       currentMonth <= 9 ? 'Third Quarter' : 'Fourth Quarter';

                data.ntca.forEach((ntca) => {
                    // Update NTCA container label and balance for the current quarter
                    ntcaLabelElement.textContent = `NTCA (${ntca.ntca_no} - ${currentQuarter})`;
                    const currentQuarterKey = currentQuarter.toLowerCase().replace(' ', '_');
                    const currentQuarterBalance = ntca[currentQuarterKey];
                    ntcaBalanceElement.textContent = currentQuarterBalance
                        ? `₱${currentQuarterBalance.toLocaleString()}`
                        : "₱0";

                    // Add NTCA breakdown to the list
                    ntcaList.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Unassigned Budget for (${ntca.ntca_no}):</strong>
                        <span class="fw-bold">
                            ${ntca.current_budget ? "₱" + ntca.current_budget.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        First Quarter: 
                        <span class="fw-bold">
                            ${ntca.first_q ? "₱" + ntca.first_q.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Second Quarter: 
                        <span class="fw-bold">
                            ${ntca.second_q ? "₱" + ntca.second_q.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Third Quarter: 
                        <span class="fw-bold">
                            ${ntca.third_q ? "₱" + ntca.third_q.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Fourth Quarter: 
                        <span class="fw-bold">
                            ${ntca.fourth_q ? "₱" + ntca.fourth_q.toLocaleString() : "<em style='color:#777;'>Not yet allocated</em>"}
                        </span>
                    </li>
                `;

                    // Add total of all quarters
                    const totalQuarters = (ntca.first_q + ntca.second_q + ntca.third_q + ntca.fourth_q).toFixed(2);
                    ntcaList.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between">
                            Total of All Quarters <span class="fw-bold text-primary">₱${parseFloat(totalQuarters).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </li>
                    `;

                    // Add budget surplus if applicable
                    if (currentQuarterKey && ntca[currentQuarterKey] > 0) {
                        ntcaList.innerHTML += `
                            <li class="list-group-item d-flex justify-content-between">
                                Budget Surplus <span class="fw-bold text-success">₱${ntca[currentQuarterKey].toLocaleString()}</span>
                            </li>
                        `;
                    }

                    // Add budget deficit (default to 0 unless there is already a deficit)
                    const budgetDeficit = ntca.budget_deficit ?? 0;
                    ntcaList.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between">
                            Budget Deficit <span class="fw-bold text-danger">₱${budgetDeficit.toLocaleString()}</span>
                        </li>
                    `;
                });
            } else {
                ntcaList.innerHTML = `
                    <li class="list-group-item text-danger">${data.message}</li>
                `;
                ntcaLabelElement.textContent = "NTCA:";
                ntcaBalanceElement.textContent = "₱0";
            }
        })
        .catch((error) => {
            console.error("Error fetching NTCA records:", error);
        });
}

