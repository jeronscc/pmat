<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="../src/css/mainheader.css">
    <?php include '../src/components/ilcdb_header.html'; ?>
</head>
<body>
    
    <div class="container-fluid mt-3">
        <div class="row">
    
    <!-- Left Panel (SARO & Balance) -->
    <div class="col-md-3 left-panel">
        <div class="balance-box p-3 mb-3">
            <h6>Remaining Balance (<span id="currentSaroName">SARO 1</span>):</h6>
            <h2 id="remainingBalance">₱4,600,000</h2>
        </div>

        <div class="d-flex justify-content-end align-items-center mb-2">
            <div class="dropdown me-2">
                <button class="icon-button dropdown-toggle" type="button" id="yearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../src/assets/filter.png" alt="Select Year" style="width: 20px; height: 20px; margin-right: 10px;">
                </button>
                <ul class="dropdown-menu" aria-labelledby="yearDropdown">
                    <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                        echo "<li><a class='dropdown-item' href='#' onclick='filterSaroByYear(\"$year\")'>$year</a></li>";
                    }
                    ?>
                </ul>
            </div>
            <button class="btn btn-dark" onclick="openAddSaroModal()">+ Add New SARO <i class="bi bi-filter"></i></button>
        </div>
        <ul class="list-group saro-list">
            <li class="list-group-item active" onclick="selectSaro('SARO 1')">SARO 1</li>
            <li class="list-group-item" onclick="selectSaro('SARO 2')">SARO 2</li>
            <li class="list-group-item" onclick="selectSaro('SARO 3')">SARO 3</li>
            <li class="list-group-item" onclick="selectSaro('SARO 4')">SARO 4</li>
            <li class="list-group-item" onclick="selectSaro('SARO 5')">SARO 5</li>
        </ul>
    </div>
 
    <!-- Right Panel (PR Table) -->
    <div class="col-md-9 right-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Currently Viewing: <span id="currentSaro">SARO 1</span></h5>
            <div class="d-flex">
                <button type="button" class="icon-button me-2" data-bs-toggle="modal" data-bs-target="#procurementModal">
                    <img src="../src/assets/add.png" alt="Add Procurement" style="width: 20px; height: 20px;">
                </button>
                <input type="text" class="form-control me-2" id="searchBar" placeholder="Search...">
                <button type="button" class="icon-button" onclick="searchProcurement()">
                    <img src="../src/assets/scan.png" alt="Search" style="width: 30px; height: 30px;">
                </button>
            </div>
        </div>
        <div class="table-container">
            <div class="record-box">
        <table>
            <thead>
                <tr>
                    <th>PR NUMBER</th>
                    <th>ACTIVITY</th>
                    <th>STATUS</th>
                </tr>
            </thead>      
            <tbody id="procurementTable"> 
                <tr>
                    <td>02-05647</td>
                    <td>ILCDB Orientation</td>
                    <td><span class="badge bg-warning text-dark">at Supply Unit</span></td>
                </tr>
                <tr>
                    <td>02-36421</td>
                    <td>Cybersecurity Workshop</td>
                    <td><span class="badge bg-success">Done</span></td>
                </tr>
                <tr>
                    <td>02-75482</td>
                    <td>Training Camp 2025</td>
                    <td><span class="badge bg-warning text-dark">at Budget Unit</span></td> 
                </tr>  
            </tbody> 
        </table> 
    </div>
    </div>
</div>
    
<!-- SARO Modal -->
<div class="modal fade" id="addSaroModal" tabindex="-1" aria-labelledby="saroTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="saroTitle">ADD SARO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="saroForm">
                    <div class="mb-3">
                        <label for="saro_number" class="form-label">SARO NUMBER</label>
                        <input type="text" class="form-control" id="saro_number" name="saro_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="budget" class="form-label">BUDGET</label>
                        <input type="text" class="form-control" id="budget" name="budget" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">YEAR</label>
                        <select class="form-select" id="year" name="year" required>
                            <option value="" disabled selected>Select Year</option>
                            <?php
                            $currentYear = date("Y");
                            for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveSaro">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Procurement Modal -->
<div class="modal fade" id="procurementModal" tabindex="-1" aria-labelledby="procurementTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="procurementTitle">ADD PROCUREMENT</h5>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="category" class="form-label">Select Procurement Category</label>
                        <select class="form-select" id="category">
                            <option value="" disabled selected>Select Category</option>
                            <option value="SVA">SVP</option>
                            <option value="Honoraria">Honoraria</option>
                            <option value="Other expense">Other Expenses</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pr-number" class="form-label">PR/TRANSACTION NUMBER</label>
                        <input type="text" class="form-control" id="pr-number" placeholder="Enter PR Number">
                    </div>
                    <div class="mb-3">
                        <label for="activity" class="form-label">ACTIVITY</label>
                        <input type="text" class="form-control" id="activity" placeholder="Enter Activity">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">DESCRIPTION</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="addProcurement">Add</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openAddSaroModal() {
    new bootstrap.Modal(document.getElementById("addSaroModal")).show();
}

function selectSaro(saroName) {
    document.getElementById("currentSaro").textContent = saroName;
    document.getElementById("currentSaroName").textContent = saroName;
    // Dummy data for different SAROs
    const saroData = {
        "SARO 1": {
            balance: "₱4,600,000",
            dummyData: [
                { pr: "02-05647", activity: "ILCDB Orientation", status: "at Supply Unit", badge: "warning" },
                { pr: "02-36421", activity: "Cybersecurity Workshop", status: "Done", badge: "success" },
                { pr: "02-75482", activity: "Training Camp 2025", status: "at Budget Unit", badge: "warning" }
            ]
        },
        "SARO 2": {
            balance: "₱3,200,000",
            dummyData: [
                { pr: "03-12345", activity: "IT Conference", status: "at Supply Unit", badge: "warning" },
                { pr: "03-67890", activity: "Hackathon", status: "Done", badge: "success" }
            ]
        },
        "SARO 3": {
            balance: "₱2,800,000",
            dummyData: [
                { pr: "04-11223", activity: "Software Training", status: "at Budget Unit", badge: "warning" }
            ]
        },
        "SARO 4": {
            balance: "₱1,500,000",
            dummyData: []
        },
        "SARO 5": {
            balance: "₱5,000,000",
            dummyData: []
        }
    };

    let tableContent = dummyData[saroName] ? dummyData[saroName].map(item =>
        `<tr>
            <td>${item.pr}</td>
            <td>${item.activity}</td>
            <td><span class="badge bg-${item.badge}">${item.status}</span></td>
        </tr>`).join("") : "<tr><td colspan='3' class='text-center'>No data available</td></tr>";

    document.getElementById("procurementTable").innerHTML = tableContent;
}
    // Update remaining balance
    const remainingBalanceElement = document.getElementById("remainingBalance");
        if (saroData[saroName]) {
            remainingBalanceElement.textContent = saroData[saroName].balance;
        } else {
            remainingBalanceElement.textContent = "₱0";
        }
        // Update active class
    document.querySelectorAll('.saro-list .list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`.saro-list .list-group-item:contains(${saroName})`).classList.add('active');

function filterSaroByYear(year) {
    const saroList = document.querySelector(".saro-list");
    saroList.innerHTML = "";

    if (year === "2025") {
        saroList.innerHTML = `
            <li class="list-group-item active" onclick="selectSaro('SARO 1')">SARO 1</li>
            <li class="list-group-item" onclick="selectSaro('SARO 2')">SARO 2</li>
            <li class="list-group-item" onclick="selectSaro('SARO 3')">SARO 3</li>
            <li class="list-group-item" onclick="selectSaro('SARO 4')">SARO 4</li>
            <li class="list-group-item" onclick="selectSaro('SARO 5')">SARO 5</li>
        `;
    } else {
        saroList.innerHTML = "<li class='list-group-item text-center'>No data available</li>";
    }
}

function searchProcurement() {
    const searchTerm = document.getElementById("searchBar").value.toLowerCase();
    const rows = document.querySelectorAll("#procurementTable tr");

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchTerm));
        row.style.display = match ? "" : "none";
    });
}
</script>
</body>
</html>