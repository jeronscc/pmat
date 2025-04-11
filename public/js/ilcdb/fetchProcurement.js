function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case "done":
            return "badge bg-success text-white"; // Green for done
        case "overdue":
            return "badge bg-danger text-white"; // Red for overdue
        case "for dv creation":
            return "badge bg-primary text-white"; // Blue for DV creation
        case "for iar / par / ics / rfi creation":
            return "badge bg-info text-white"; // Light blue for documentation steps
        case "for ors creation":
            return "badge bg-warning text-dark"; // Yellow for ORS creation
        case "for obligation":
            return "badge bg-dark text-white"; // Dark for budget obligation
        case "for payment processing":
            return "badge bg-secondary text-white"; // Gray for payment processing
        case "waiting for budget":
            return "badge bg-light text-dark"; // Light for budget wait
        default:
            return "badge bg-light text-dark"; // Default for unknown or no status
    }
}

document.addEventListener("click", function (event) {
    const row = event.target.closest("tr"); // Get the clicked row
    if (row && row.dataset.procurementId) {
        openProcurementModal({ procurement_id: row.dataset.procurementId });
    }
});

function fetchProcurementForSaro(saroNo) {
    const url =
        saroNo === ""
            ? "/api/fetch-procurement-ilcdb"
            : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            const tableBodies = {
                all: document.getElementById("procurementTable"),
                overdue: document.getElementById("procurementTableOverdue"),
                done: document.getElementById("procurementTableDone"),
            };

            // Clear old rows in the table bodies
            Object.values(tableBodies).forEach(
                (tableBody) => (tableBody.innerHTML = "")
            );

            if (data.length > 0) {
                // Sort data by status order and maintain original order (FIFO)
                const statusOrder = {
                    overdue: 1,
                    "for dv creation": 2,
                    "for iar / par / ics / rfi creation": 3,
                    "for ors creation": 4,
                    "for obligation": 5,
                    "for payment processing": 6,
                    "waiting for budget": 7,
                    done: 8,
                };

                data.sort((a, b) => {
                    const statusA = (a.status || "unknown").toLowerCase();
                    const statusB = (b.status || "unknown").toLowerCase();

                    // Sort by status first
                    const statusComparison =
                        (statusOrder[statusA] || 9) -
                        (statusOrder[statusB] || 9);

                    // Maintain original FIFO order if statuses are the same
                    return statusComparison !== 0
                        ? statusComparison
                        : data.indexOf(a) - data.indexOf(b);
                });

                data.forEach((item) => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-procurement-id", item.procurement_id);

                    // PR NUMBER cell
                    const prNumberCell = document.createElement("td");
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // CATEGORY cell
                    const categoryCell = document.createElement("td");
                    categoryCell.textContent =
                        item.procurement_category || "N/A";
                    row.appendChild(categoryCell);

                    // ACTIVITY NAME cell
                    const activityCell = document.createElement("td");
                    activityCell.textContent = item.activity || "N/A";
                    row.appendChild(activityCell);

                    // STATUS cell
                    const statusCell = document.createElement("td");
                    const badge = document.createElement("span");

                    const statusMessage = (item.status || "").toLowerCase();
                    badge.className = getStatusClass(statusMessage);
                    badge.textContent = item.status || "Unknown Status";
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to the appropriate table body
                    if (statusMessage === "done") {
                        tableBodies.done.appendChild(row);
                    } else if (statusMessage === "overdue") {
                        tableBodies.overdue.appendChild(row);
                    } else {
                        tableBodies.all.appendChild(row);
                    }
                });
            } else {
                const emptyMessage = document.createElement("tr");
                const emptyCell = document.createElement("td");
                emptyCell.setAttribute("colspan", "4"); // Adjust column span based on the table structure
                emptyCell.textContent =
                    "No procurement records found for the selected SARO.";
                emptyMessage.appendChild(emptyCell);
                tableBodies.all.appendChild(emptyMessage);
            }
        })
        .catch((error) =>
            console.error("Error fetching procurement requirements:", error)
        );
}


// Fetch procurement data by year filter
function fetchProcurementForYear(year) {
    const url =
        year === ""
            ? "/api/fetch-procurement-ilcdb"
            : `/api/fetch-procurement-ilcdb?year=${year}`;

    console.log("Fetching procurement data from:", url); // Debugging log

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            const tableBodies = {
                all: document.getElementById("procurementTable"),
                overdue: document.getElementById("procurementTableOverdue"),
                done: document.getElementById("procurementTableDone"),
            };

            // Clear old rows in the table bodies
            Object.values(tableBodies).forEach(
                (tableBody) => (tableBody.innerHTML = "")
            );

            if (data.length > 0) {
                // Sort data by status order and maintain original order (FIFO)
                const statusOrder = {
                    overdue: 1,
                    "for dv creation": 2,
                    "for iar / par / ics / rfi creation": 3,
                    "for ors creation": 4,
                    "for obligation": 5,
                    "for payment processing": 6,
                    "waiting for budget": 7,
                    done: 8,
                };

                data.sort((a, b) => {
                    const statusA = (a.status || "unknown").toLowerCase();
                    const statusB = (b.status || "unknown").toLowerCase();

                    // Sort by status first
                    const statusComparison =
                        (statusOrder[statusA] || 9) -
                        (statusOrder[statusB] || 9);

                    // Maintain original FIFO order if statuses are the same
                    return statusComparison !== 0
                        ? statusComparison
                        : data.indexOf(a) - data.indexOf(b);
                });

                data.forEach((item) => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-procurement-id", item.procurement_id);

                    // PR NUMBER cell
                    const prNumberCell = document.createElement("td");
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // CATEGORY cell
                    const categoryCell = document.createElement("td");
                    categoryCell.textContent =
                        item.procurement_category || "N/A";
                    row.appendChild(categoryCell);

                    // ACTIVITY NAME cell
                    const activityCell = document.createElement("td");
                    activityCell.textContent = item.activity || "N/A";
                    row.appendChild(activityCell);

                    // STATUS cell
                    const statusCell = document.createElement("td");
                    const badge = document.createElement("span");

                    const statusMessage = (item.status || "").toLowerCase();
                    badge.className = getStatusClass(statusMessage);
                    badge.textContent = item.status || "Unknown Status";
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to the appropriate table body
                    if (statusMessage === "done") {
                        tableBodies.done.appendChild(row);
                    } else if (statusMessage === "overdue") {
                        tableBodies.overdue.appendChild(row);
                    } else {
                        tableBodies.all.appendChild(row);
                    }
                });
            } else {
                const emptyMessage = document.createElement("tr");
                const emptyCell = document.createElement("td");
                emptyCell.setAttribute("colspan", "4"); // Adjust column span based on the table structure
                emptyCell.textContent =
                    "No procurement records found for the selected SARO.";
                emptyMessage.appendChild(emptyCell);
                tableBodies.all.appendChild(emptyMessage);
            }
        })
        .catch((error) =>
            console.error("Error fetching procurement requirements:", error)
        );
}

// FETCH PROCUREMENT DATA FOR MODAL
function fetchProcurementRequirements(saroNo) {
    const url = saroNo === ""
        ? "/api/fetch-procurement-ilcdb"
        : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

        fetch(url)
        .then((response) => response.json())
        .then((data) => {
            const tableBodies = {
                all: document.getElementById("procurementTable"),
                overdue: document.getElementById("procurementTableOverdue"),
                done: document.getElementById("procurementTableDone"),
            };

            // Clear old rows in the table bodies
            Object.values(tableBodies).forEach(
                (tableBody) => (tableBody.innerHTML = "")
            );

            if (data.length > 0) {
                // Sort data by status order and maintain original order (FIFO)
                const statusOrder = {
                    overdue: 1,
                    "for dv creation": 2,
                    "for iar / par / ics / rfi creation": 3,
                    "for ors creation": 4,
                    "for obligation": 5,
                    "for payment processing": 6,
                    "waiting for budget": 7,
                    done: 8,
                };

                data.sort((a, b) => {
                    const statusA = (a.status || "unknown").toLowerCase();
                    const statusB = (b.status || "unknown").toLowerCase();

                    // Sort by status first
                    const statusComparison =
                        (statusOrder[statusA] || 9) -
                        (statusOrder[statusB] || 9);

                    // Maintain original FIFO order if statuses are the same
                    return statusComparison !== 0
                        ? statusComparison
                        : data.indexOf(a) - data.indexOf(b);
                });

                data.forEach((item) => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-procurement-id", item.procurement_id);

                    // PR NUMBER cell
                    const prNumberCell = document.createElement("td");
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // CATEGORY cell
                    const categoryCell = document.createElement("td");
                    categoryCell.textContent =
                        item.procurement_category || "N/A";
                    row.appendChild(categoryCell);

                    // ACTIVITY NAME cell
                    const activityCell = document.createElement("td");
                    activityCell.textContent = item.activity || "N/A";
                    row.appendChild(activityCell);

                    // STATUS cell
                    const statusCell = document.createElement("td");
                    const badge = document.createElement("span");

                    const statusMessage = (item.status || "").toLowerCase();
                    badge.className = getStatusClass(statusMessage);
                    badge.textContent = item.status || "Unknown Status";
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to the appropriate table body
                    if (statusMessage === "done") {
                        tableBodies.done.appendChild(row);
                    } else if (statusMessage === "overdue") {
                        tableBodies.overdue.appendChild(row);
                    } else {
                        tableBodies.all.appendChild(row);
                    }
                });
            } else {
                const emptyMessage = document.createElement("tr");
                const emptyCell = document.createElement("td");
                emptyCell.setAttribute("colspan", "4"); // Adjust column span based on the table structure
                emptyCell.textContent =
                    "No procurement records found for the selected SARO.";
                emptyMessage.appendChild(emptyCell);
                tableBodies.all.appendChild(emptyMessage);
            }
        })
        .catch((error) =>
            console.error("Error fetching procurement requirements:", error)
        );
}

// Store Bootstrap modal instance globally
let bootstrapModalInstance = null;

// Function to open modal and display procurement details
function openProcurementModal(item) {
    const procurementId = item.procurement_id; // Get procurement ID from clicked item
    const modal = document.getElementById("procurementDetailsModal");

    if (!modal) {
        console.error("Modal element not found.");
        return;
    }

    // Fetch detailed data from the API using the procurement_id
    const url = `/api/fetch-procurement-details?procurement_id=${procurementId}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            if (data.message) {
                alert(data.message); // Show error if procurement is not found
            } else {
                // Populate modal fields
                console.log("Full API Response:", data);
                document.getElementById(
                    "modalProcurementCategory"
                ).textContent = data.procurement_category || "N/A";
                document.getElementById("modalProcurementNo").textContent =
                    data.procurement_id || "N/A";
                document.getElementById("modalSaroNo").textContent =
                    data.saro_no || "N/A";
                document.getElementById("modalNTCANo").textContent =
                    data.ntca_no || "N/A";
                document.getElementById("modalQuarter").textContent =
                    data.quarter || "N/A";
                document.getElementById("modalPurchaseRequest").textContent =
                    data.pr_amount || "N/A";
                document.getElementById("modalApprovedBudget").textContent =
                    data.approved_budget || "Not yet approved";
                document.getElementById("modalYear").textContent =
                    data.year || "N/A";
                document.getElementById("modalDescription").textContent =
                    data.description || "N/A";
                document.getElementById("modalActivity").textContent =
                    data.activity || "N/A";

                // Change the label for "Activity" based on the procurement category
                const activityLabel =
                    document.getElementById("modalActivityLabel");
                const category = data.procurement_category.toLowerCase();
                console.log("Procurement category:", category); // Debugging log

                if (category === "honoraria") {
                    activityLabel.textContent = "Speaker:";
                } else if (category === "daily travel expense") {
                    activityLabel.textContent = "Traveller:";
                } else {
                    activityLabel.textContent = "Activity:";
                }

                // Initialize Bootstrap modal only once
                if (!bootstrapModalInstance) {
                    bootstrapModalInstance = new bootstrap.Modal(modal);
                }

                // Show the modal
                bootstrapModalInstance.show();
            }
        })
        .catch((error) => {
            console.error("Error fetching procurement details:", error);
            alert("Failed to load procurement details.");
        });
}

// Ensure modal closes when the close button is clicked
document.getElementById("closeModalBtn").addEventListener("click", function () {
    if (bootstrapModalInstance) {
        bootstrapModalInstance.hide();
    }
});

// Event listener for table row click
const tableBodies = {
    all: document.getElementById("procurementTable"),
    pending: document.getElementById("procurementTablePending"),
    ongoing: document.getElementById("procurementTableOngoing"),
    overdue: document.getElementById("procurementTableOverdue"),
    done: document.getElementById("procurementTableDone"),
};

document.addEventListener("click", function (event) {
    const row = event.target.closest("tr"); // Find closest row
    if (row && row.dataset.procurementId) {
        openProcurementModal({ procurement_id: row.dataset.procurementId });
    }
});

function fetchProcurementData(year = "", status = "all") {
    let url = "/api/fetch-combined-procurement-data";

    if (year !== "") url += `?year=${year}`;
    if (status !== "all")
        url += year !== "" ? `&status=${status}` : `?status=${status}`;
    if (currentSaroNo !== "")
        url +=
            year !== "" || status !== "all"
                ? `&saro_no=${currentSaroNo}`
                : `?saro_no=${currentSaroNo}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            const tableBodies = {
                all: document.getElementById("procurementTable"),
                overdue: document.getElementById("procurementTableOverdue"),
                done: document.getElementById("procurementTableDone"),
            };

            Object.values(tableBodies).forEach(
                (tableBody) => (tableBody.innerHTML = "")
            ); // Clear old rows

            data.forEach((item) => {
                const row = document.createElement("tr");
                row.setAttribute("data-procurement-id", item.procurement_id); // Unique row identifier

                // PR NUMBER cell
                const prNumberCell = document.createElement("td");
                prNumberCell.textContent = item.procurement_id;
                row.appendChild(prNumberCell);

                // CATEGORY cell
                const categoryCell = document.createElement("td");
                categoryCell.textContent = item.procurement_category || "N/A";
                row.appendChild(categoryCell);

                // ACTIVITY NAME cell
                const activityCell = document.createElement("td");
                activityCell.textContent = item.activity || "N/A";
                row.appendChild(activityCell);

                // STATUS cell
                const statusCell = document.createElement("td");
                const badge = document.createElement("span");

                const statusMessage = (item.status || "").toLowerCase();
                badge.className = getStatusClass(statusMessage);
                badge.textContent = item.status || "Unknown Status";

                statusCell.appendChild(badge);
                row.appendChild(statusCell);

                // Append row to the appropriate table body
                if (statusMessage === "done") {
                    tableBodies.done.appendChild(row);
                } else if (statusMessage === "overdue") {
                    tableBodies.overdue.appendChild(row);
                } else {
                    tableBodies.all.appendChild(row);
                }
            });
        })
        .catch((error) =>
            console.error("Error fetching procurement data:", error)
        );
}

// Event listener for the year filter
document.getElementById("year")?.addEventListener("change", function () {
    const yearFilter = this.value;
    const activeTab = document.querySelector(".nav-link.active");
    const status = activeTab
        ? activeTab
              .getAttribute("id")
              .replace("-tab", "")
              .replace("tab", "")
              .toLowerCase()
        : "all";

    fetchProcurementData(yearFilter, status);
});

// Fetch procurement data when the page loads
document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById("procurementTable")) {
        fetchProcurementData("");
    }
});

// Event listener for tab clicks to switch between tabs
document.querySelectorAll(".nav-link").forEach((tab) => {
    tab.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default Bootstrap behavior
        const filter = tab
            .getAttribute("id")
            .replace("-tab", "")
            .replace("tab", "")
            .toLowerCase();

        // Remove active class from all tabs and add to clicked tab
        document
            .querySelectorAll(".nav-link")
            .forEach((t) => t.classList.remove("active"));
        tab.classList.add("active");

        // Fetch and render table with new filter
        const yearFilter = document.getElementById("year")?.value || "";
        fetchProcurementData(yearFilter, filter);
    });
});

function checkOverdue() {
    fetch("/check-overdue")
        .then((response) => response.json())
        .then((data) => {
            if (data.updated) {
                console.log("Overdue status updated.");
                updateOverdueUI(data.overdue_items); // Update table without refreshing
            }
        })
        .catch((error) => console.error("Error:", error));
}

function updateOverdueUI(overdueItems) {
    overdueItems.forEach((item) => {
        const row = document.querySelector(
            `[data-procurement-id="${item.procurement_id}"]`
        );
        if (row) {
            const statusBadge = row.querySelector(".status-badge");
            if (statusBadge) {
                statusBadge.className = "badge bg-danger text-white"; // Set to red
                statusBadge.textContent = "Overdue";
            }
        }
    });
}

setInterval(checkOverdue, 5000); // Check every 5 seconds

// Function to update procurement table dynamically
function updateProcurementTable(data) {
    const tableBodies = {
        all: document.getElementById("procurementTable"),
        overdue: document.getElementById("procurementTableOverdue"),
        done: document.getElementById("procurementTableDone"),
    };

    // Clear all table bodies
    Object.values(tableBodies).forEach(
        (tableBody) => (tableBody.innerHTML = "")
    );

    if (data.length > 0) {
        // Sort data by status order and maintain original order for FIFO
        const statusOrder = {
            overdue: 1,
            "for dv creation": 2,
            "for iar / par / ics / rfi creation": 3,
            "for ors creation": 4,
            "for obligation": 5,
            "for payment processing": 6,
            "waiting for budget": 7,
            done: 8,
        };

        data.sort((a, b) => {
            const statusA = (a.status || "unknown").toLowerCase();
            const statusB = (b.status || "unknown").toLowerCase();

            // Sort by status first
            const statusComparison =
                (statusOrder[statusA] || 9) - (statusOrder[statusB] || 9);

            // If statuses are the same, maintain original FIFO order
            return statusComparison !== 0
                ? statusComparison
                : data.indexOf(a) - data.indexOf(b);
        });

        data.forEach((item) => {
            const row = document.createElement("tr");

            // PR NUMBER cell
            const prNumberCell = document.createElement("td");
            prNumberCell.textContent = item.procurement_id;
            row.appendChild(prNumberCell);

            // CATEGORY cell
            const categoryCell = document.createElement("td");
            categoryCell.textContent = item.procurement_category || "N/A";
            row.appendChild(categoryCell);

            // ACTIVITY NAME cell
            const activityCell = document.createElement("td");
            activityCell.textContent = item.activity || "N/A";
            row.appendChild(activityCell);

            // STATUS & UNIT cell
            const statusCell = document.createElement("td");
            const badge = document.createElement("span");

            const statusMessage = (item.status || "").toLowerCase();
            badge.className = getStatusClass(statusMessage);
            badge.textContent = item.status || "Unknown Status";

            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            // Append row to the appropriate table body
            if (statusMessage === "done") {
                tableBodies.done.appendChild(row);
            } else if (statusMessage === "overdue") {
                tableBodies.overdue.appendChild(row);
            } else {
                tableBodies.all.appendChild(row);
            }
        });

        // Add event listener to each table body for delegation
        Object.values(tableBodies).forEach((tableBody) => {
            tableBody.addEventListener("click", function (event) {
                const prNumberCell = event.target.closest("td");
                if (prNumberCell && prNumberCell.parentElement) {
                    const procurementId = prNumberCell.textContent;
                    const row = prNumberCell.parentElement;
                    openProcurementModal({ procurement_id: procurementId });
                }
            });
        });
    } else {
        // Display message if no data is available
        const emptyMessage = document.createElement("tr");
        const emptyCell = document.createElement("td");
        emptyCell.setAttribute("colspan", "4"); // Match table column count
        emptyCell.textContent = "No procurement records found.";
        emptyMessage.appendChild(emptyCell);
        tableBodies.all.appendChild(emptyMessage);
    }
}


// Event listener for the year filter
document.getElementById("year")?.addEventListener("change", function () {
    const yearFilter = this.value;
    const activeTab = document.querySelector(".nav-link.active");
    const status = activeTab
        ? activeTab
              .getAttribute("id")
              .replace("-tab", "")
              .replace("tab", "")
              .toLowerCase()
        : "all";

    fetchProcurementData(yearFilter, status);
});

// Fetch procurement data when the page loads
document.addEventListener("DOMContentLoaded", () => {
    // Check if procurementTable exists before trying to populate it
    if (document.getElementById("procurementTable")) {
        fetchProcurementData("");
    }
});

// Utility functions
function highlightSelectedItem(selectedItem) {
    const items = document.querySelectorAll(".saro-list .list-group-item");
    items.forEach((item) => item.classList.remove("active"));
    selectedItem.classList.add("active");
}

function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function filterProcurementByYear(year) {
    fetchProcurementData(year);
}
