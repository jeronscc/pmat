<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/homepage.css">
    <link rel="stylesheet" href="/css/mainheader.css">
    <link rel="stylesheet" href="/css/sidenav.css">
</head>
<body>
    
<header class="d-flex align-items-center justify-content-between bg-black text-white p-3 shadow" id="stickyHeader">
        <div class="logo d-flex align-items-center">
            <img src="/assets/dict-logo.png" alt="DICT Logo" class="img-fluid" id="dictLogo">
            <img src="/assets/ilcdb-logo-2.png" alt="DTC Logo" class="img-fluid ms-2"> 
        </div>
        <h1 class="text-center flex-grow-1 fs-4 m-0">Procurement Tracking and Monitoring System</h1> 
        
        <button class="btn custom-btn" id="menu-icon">
            <i class="bi bi-list"></i> <!-- Hamburger icon -->
        </button>
    </header>

    <!-- Side Nav (Initially hidden) -->
    <div id="side-nav" class="side-nav">
        <ul>
            <li>
                <form action="" method="POST">
                    <button type="submit">
                        <i class="fas fa-home"></i><img src="/assets/home_icon.png" alt=""> Home
                    </button>
                </form>
            </li>
            <li>
                <form action="">
                    <button type="submit">
                        <i class="fas fa-users"></i><img src="/assets/account_icon.png" alt=""> Accounts
                    </button>
                </form>
            </li>
            <li>
                <form action="" method="POST">
                    <button type="submit">
                        <i class="fas fa-clock"></i><img src="/assets/report_icon.png" alt=""> Reports
                    </button>
                </form>
            </li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt"></i><img src="/assets/logout_icon.png" alt=""> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
    <div class="container-fluid mt-3">
        <div class="row">
    
    <!-- Left Panel (SARO & Balance) -->
    <div class="col-md-3 left-panel">
    <div class="balance-container p-3 mb-3">
        <h6>Remaining Balance (<span id="currentSaroName"></span>):</h6>
        <h2 id="remainingBalance"></h2>
    </div>

    <div class="d-flex justify-content-end align-items-center mb-2">
        <div class="dropdown me-2">
            <button class="dropdown-toggle" type="button" id="yearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="/assets/filter.png" alt="Select Year" style="width: 20px; height: 20px; margin-right: 10px;">
            </button>
            <ul class="dropdown-menu" aria-labelledby="yearDropdown">
                <li><a class="dropdown-item" href="#" onclick="filterSaroByYear('')">Show All</a></li> <!-- Show All option -->
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

    <!-- Scrollable SARO List Container -->
    <div class="saro-container">
        <ul class="list-group saro-list">

        </ul>
    </div>
</div>

 
    <!-- Right Panel (PR Table) -->
    <div class="col-md-9 right-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <section class="records">
        <div class="header">
            <h5>Currently Viewing: <span id="currentSaro">SARO 1</span></h5>
            <div class="d-flex">
                <button type="button" class="icon-button me-2" data-bs-toggle="modal" data-bs-target="#procurementModal">
                    <img src="/assets/add.png" alt="Add Procurement" style="width: 20px; height: 20px;">
                </button>
                <div class="search-box">
                <input type="text" class="form-control me-2" id="searchBar" placeholder="Search...">
                <button type="submit" class="search-button" onclick="">
                    Search
                </button>
                <div>
            </div>
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
                <tr>
                    <td>02-75482</td>
                    <td>Training Camp 2025</td>
                    <td><span class="badge bg-warning text-dark">at Budget Unit</span></td> 
                </tr>  
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
                <tr>
                    <td>02-75482</td>
                    <td>Training Camp 2025</td>
                    <td><span class="badge bg-warning text-dark">at Budget Unit</span></td> 
                </tr>  
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
                <tr>
                    <td>02-75482</td>
                    <td>Training Camp 2025</td>
                    <td><span class="badge bg-warning text-dark">at Budget Unit</span></td> 
                </tr> 
                <tr>
                    <td>02-75482</td>
                    <td>Training Camp 2025</td>
                    <td><span class="badge bg-warning text-dark">at Budget Unit</span></td> 
                </tr>  
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
    </section>
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
                            <option value="all">Show All</option> <!-- Show All option -->
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

    <!-- Bootstrap JS (Optional, only needed for dropdowns, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/menu.js"></script>
<script>
    function openAddSaroModal() {
    new bootstrap.Modal(document.getElementById("addSaroModal")).show();
}

</script>
<script>
// Fetch SARO data from the API
// Wait for the window to load
window.onload = function() {
    // Initially load all SAROs when the page loads (with no balance)
    fetchSaroData('');
};

// Function to fetch and display SARO list based on the selected year or "all"
function filterSaroByYear(year) {
    // Reset the balance display to "₱0" when the year filter changes
    

    // If the user selects "Show All", pass an empty string to fetch all SAROs
    const url = year === '' ? '/api/fetch-saro-ilcdb' : `/api/fetch-saro-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            saroList.innerHTML = ''; // Clear previous entries

            if (data.length > 0) {
                data.forEach(saro => {
                    // Create list item for each SARO number
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `${saro.saro_no}`;

                    // Add click event to each SARO number
                    listItem.addEventListener('click', function() {
                        displayCurrentBudget(saro); // Show balance when SARO is clicked
                    });

                    // Append the list item to the SARO list
                    saroList.appendChild(listItem);
                });
            } else {
                // Show message if no SARO data is available
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
}

function fetchSaroData(year) {
    // Reset the balance display to "₱0" before fetching SAROs
    document.getElementById('remainingBalance').textContent = '₱0';

    // If no year is selected, fetch all SAROs, otherwise filter by year
    const url = year === '' ? '/api/fetch-saro-ilcdb' : `/api/fetch-saro-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const saroList = document.querySelector('.saro-list');
            saroList.innerHTML = ''; // Clear previous entries

            if (data.length > 0) {
                data.forEach(saro => {
                    // Create list item for each SARO number
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `${saro.saro_no}`;

                    // Add click event to each SARO number
                    listItem.addEventListener('click', function() {
                        displayCurrentBudget(saro); // Show balance when SARO is clicked
                    });

                    // Append the list item to the SARO list
                    saroList.appendChild(listItem);
                });
            } else {
                // Show message if no SARO data is available
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
}

// Function to display the remaining balance for the clicked SARO
function displayCurrentBudget(saro) {
    // Set the current SARO name in the container
    document.getElementById('currentSaroName').textContent = `${saro.saro_no}`;
    
    // Check if current_budget exists and format it with comma separation
    const currentBudget = saro.current_budget
        ? `₱${formatNumberWithCommas(saro.current_budget)}`
        : '₱0';
    
    // Display the current budget in the "remainingBalance" container
    document.getElementById('remainingBalance').textContent = currentBudget;
}

// Custom function to format numbers with commas
function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}




// Add an event listener to the year filter
document.getElementById('year').addEventListener('change', function() {
    // When the year changes, fetch the SARO data and reset the balance display
    fetchSaroData(this.value);
});


</script>
</body>
</html>