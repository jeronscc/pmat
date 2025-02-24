<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <form action="/homepage-ilcdb">
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
                <form action="">
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
            <h5>Currently Viewing: <span id="currentViewingSaro"></span></h5>
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
                        <!-- Rows will be inserted dynamically by JavaScript -->
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
                    @csrf
                    <div class="mb-3">
                        <label for="saro_number" class="form-label">SARO NUMBER</label>
                        <input type="text" class="form-control" id="saro_number" name="saro_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="budget" class="form-label">BUDGET</label>
                        <input type="text" class="form-control" id="budget" name="budget" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">DESCRIPTION</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">YEAR</label>
                        <select class="form-select" name="saro_year" id="saro_year" required>
                            <option value="" disabled selected>Select Year</option>
                            <?php
                            $startYear = max(2026, date("Y")); // Start from 2026, or the current year if it's later
                            for ($year = $startYear; $year >= $startYear - 10; $year--) {
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
                        <label for="saro-number" class="form-label">SARO NUMBER</label>
                        <input type="text" class="form-control" id="saro-number" placeholder="Enter Activity">
                    </div>
                    <div class="mb-3">
                        <label for="pr-year" class="form-label">YEAR</label>
                        <select class="form-select" id="pr-year" placeholder="Enter Activity">
                        <option value="" disabled selected>Select Year</option>
                        <option>2026</option> 
                        <option>2025</option>
                        <option>2024</option> 
                        <option>2023</option> 
                        <option>2022</option> 
                        <option>2021</option>    
                        </select>
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
    document.getElementById('remainingBalance').textContent = '₱0';
    document.getElementById('currentViewingSaro').textContent = '';

    // Update SARO list based on year
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
                        fetchProcurementForSaro(saro.saro_no, year); // Fetch and display procurement for this SARO
                    });

                    // Append the list item to the SARO list
                    saroList.appendChild(listItem);
                });
            } else {
                // Show message if no SARO data is available
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found for the selected year.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));

    // Also fetch and display procurement data for the selected year
    fetchProcurementForYear(year);
}

function fetchProcurementForYear(year) {
    const url = year === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear any existing rows in the table

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; // Placeholder
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append the row to the table body
                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found for the selected year.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement data:', error));
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

document.addEventListener('DOMContentLoaded', function () {
    fetchProcurementForSaro(''); // Fetch all requirements by default
});
// Function to display the remaining balance for the clicked SARO
function displayCurrentBudget(saro) {
    // Set the current SARO name in the container
    document.getElementById('currentViewingSaro').textContent = `${saro.saro_no}`;
    document.getElementById('currentSaroName').textContent = `${saro.saro_no}`;
    
    // Check if current_budget exists and format it with comma separation
    const currentBudget = saro.current_budget
        ? `₱${formatNumberWithCommas(saro.current_budget)}`
        : '₱0';
    
    // Display the current budget in the "remainingBalance" container
    // Display the current budget in the "remainingBalance" containerBudget;
    document.getElementById('remainingBalance').textContent = currentBudget;
    // Fetch and display the requirements associated with the selected SARO
    // Fetch and display the requirements associated with the selected SARO
    fetchProcurementForSaro(saro.saro_no);
}
function fetchProcurementForSaro(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear any existing rows in the table

            if (data.length > 0) {
                // Loop through the fetched data and create table rows
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id; // Assuming "procurement_id" is returned
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity; // Assuming "activity" is returned
                    row.appendChild(activityCell);

                    // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending';  // Placeholder, since we don't have the status in the API response
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append the row to the table body
                    tableBody.appendChild(row);
                });
            } else {
                // Show message if no procurement data is available
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement requirements:', error));
}

function fetchSaroDataAndRequirements(year) {
    const url = year === '' ? '/api/fetch-saro-ilcdb' : `/api/fetch-saro-ilcdb?year=${year}`;
    
    fetch(url)
        .then(response => response.json())
        .then(saros => {
            const saroList = document.querySelector('.saro-list');
            saroList.innerHTML = ''; // Clear previous entries

            if (saros.length > 0) {
                saros.forEach(saro => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = `${saro.saro_no}`;

                    listItem.addEventListener('click', function() {
                        displayCurrentBudget(saro); 
                        fetchProcurementRequirements(saro.saro_no);
                    });

                    saroList.appendChild(listItem);
                });
            } else {
                const emptyMessage = document.createElement('li');
                emptyMessage.classList.add('list-group-item');
                emptyMessage.textContent = 'No SARO records found.';
                saroList.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching SARO data:', error));
}

function fetchProcurementRequirements(saroNo) {
    const url = saroNo === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?saro_no=${saroNo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear existing table rows

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS cell (this is just a placeholder as we don't have status in the data)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; 
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to table
                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement requirements:', error));
}
// Custom function to format numbers with commas
function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function filterProcurementByYear(year) {
    fetchProcurementData(year);
}

function fetchProcurementData(year) {
    const url = year === '' ? '/api/fetch-procurement-ilcdb' : `/api/fetch-procurement-ilcdb?year=${year}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear any existing rows in the table

            if (data.length > 0) {
                // Loop through the fetched data and create table rows
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS cell (this is just a placeholder as we don't have status in the data)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; 
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append row to table
                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}

document.getElementById('year').addEventListener('change', function() {
    // When the year changes, fetch the procurement data based on selected year
    fetchProcurementData(this.value);
});
const apiUrl = '/api/fetch-procurement-ilcdb'; // replace with your actual API endpoint

// Fetch data from the API and populate the table
window.addEventListener('DOMContentLoaded', (event) => {
    fetch(apiUrl)
    .then(response => response.json())  // Parse the response as JSON
    .then(data => {
        console.log(data); // Log the data to the console to check the response
        const tableBody = document.getElementById('procurementTable');
        
        // Clear any existing rows in the table
        tableBody.innerHTML = '';

        // Loop through the fetched data and create table rows
        data.forEach(item => {
            const row = document.createElement('tr');

            // PR NUMBER cell (procurement_id)
            const prNumberCell = document.createElement('td');
            prNumberCell.textContent = item.procurement_id; // Assuming "procurement_id" is returned
            row.appendChild(prNumberCell);

            // ACTIVITY cell
            const activityCell = document.createElement('td');
            activityCell.textContent = item.activity; // Assuming "activity" is returned
            row.appendChild(activityCell);

            // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
            const statusCell = document.createElement('td');
            const badge = document.createElement('span');
            badge.classList.add('badge', 'bg-warning', 'text-dark');
            badge.textContent = 'Pending';  // Placeholder, since we don't have the status in the API response
            statusCell.appendChild(badge);
            row.appendChild(statusCell);

            // Append the row to the table body
            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

});
</script>

<script>
document.getElementById('saveSaro').addEventListener('click', function() {
    const saroNumber = document.getElementById('saro_number').value;
    const budget = document.getElementById('budget').value;
    const year = document.getElementById('saro_year').value;
    
    console.log('saroNumber:', saroNumber, 'budget:', budget, 'year:', saro_year);

    if (!saroNumber || !budget || !saro_year) {
        alert('All fields must be filled out.');
        return;
    }

    fetch('/api/add-saro-ilcdb', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',  // Helps Laravel parse JSON properly
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            saro_number: saroNumber,
            budget: budget,
            saro_year: year
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errData => {
                console.error('Validation/Error:', errData);
                alert('Error: ' + JSON.stringify(errData));
                throw new Error('Request failed');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.message === 'SARO added successfully') {
            alert('SARO added successfully');
            fetchSaroData('');
            const addSaroModal = bootstrap.Modal.getInstance(document.getElementById("addSaroModal"));
            addSaroModal.hide();
            document.getElementById('saroForm').reset();
        } else {
            alert('Failed to add SARO');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding SARO');
    });
});

//Add eventlistener for the search button
document.getElementById('searchBar').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        searchProcurement();
    }
});

document.querySelector('.search-button').addEventListener('click', function() {
    searchProcurement();
});

// Function to search for procurement based on the search term
function searchProcurement() {
    const query = document.getElementById('searchBar').value;

    fetch(`/api/search-procurement-ilcdb?query=${query}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('procurementTable');
            tableBody.innerHTML = ''; // Clear any existing rows in the table

            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('tr');

                    // PR NUMBER cell (procurement_id)
                    const prNumberCell = document.createElement('td');
                    prNumberCell.textContent = item.procurement_id;
                    row.appendChild(prNumberCell);

                    // ACTIVITY cell
                    const activityCell = document.createElement('td');
                    activityCell.textContent = item.activity;
                    row.appendChild(activityCell);

                    // STATUS cell (we don't have status in the response, so we can leave it with a placeholder)
                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-warning', 'text-dark');
                    badge.textContent = 'Pending'; // Placeholder
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    // Append the row to the table body
                    tableBody.appendChild(row);
                });
            } else {
                const emptyMessage = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', '3');
                emptyCell.textContent = 'No procurement records found.';
                emptyMessage.appendChild(emptyCell);
                tableBody.appendChild(emptyMessage);
            }
        })
        .catch(error => console.error('Error fetching procurement data:', error));
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addProcurement').addEventListener('click', function() {
        const category = document.getElementById('category').value;
        const prNumber = document.getElementById('pr-number').value;
        const saroNumber = document.getElementById('saro-number').value;
        const prYear = document.getElementById('pr-year').value;
        const activity = document.getElementById('activity').value;
        const description = document.getElementById('description').value;

        console.log({ category, prNumber, saroNumber, prYear, activity, description });

        fetch('/api/add-procurement-ilcdb', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                category: category,
                pr_number: prNumber,
                saro_number: saroNumber,
                pr_year: prYear,
                activity: activity,
                description: description
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => {
                    console.error('Validation/Error:', errData);
                    alert('Error: ' + JSON.stringify(errData));
                    throw new Error('Request failed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.message === 'Procurement added successfully') {
                alert('Procurement added successfully');
                fetchProcurementForSaro(''); // Refresh the procurement table
                const procurementModal = bootstrap.Modal.getInstance(document.getElementById("procurementModal"));
                procurementModal.hide();
            } else {
                alert('Failed to add procurement');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding procurement');
        });
    });
});

</script>
</body>
</html>
</script>
</body>
</html>