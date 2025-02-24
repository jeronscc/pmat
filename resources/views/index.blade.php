<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <!-- Bootstrap CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/landingpage.css">
    <link rel="stylesheet" href="/css/landingheader.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <header class="d-flex align-items-center justify-content-between bg-black text-white p-3 shadow">
        <div class="logo">
            <img src="/assets/dict-logo.png" alt="DICT Logo" class="img-fluid" id="dictLogo">
        </div>
        <h1 class="text-center flex-grow-1 fs-4 m-0">Procurement Tracking and Monitoring System</h1> 
        <a href="/login">
            <button class="btn custom-btn">Log In</button>
        </a>
    </header>

    <main>
        <aside>
            <div class="balance-box">
                <span>Remaining Balance:</span>
                <p>₱0</p> <!-- Default value -->
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
    </div>
            <button class="accordion">ILCDB <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel ilcdb">
                <!-- SARO data will be populated here -->
            </div>

            <button class="accordion">DTC <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel dtc">
                <!-- Placeholder content -->
                <p>SARO 1</p>
            </div>

            <button class="accordion">SPARK <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel spark">
                <!-- Placeholder content -->
                <p>SARO 1</p>
            </div>

            <button class="accordion">PROJECT CLICK <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel project-click">
                <!-- Placeholder content -->
                <p>SARO 1</p>
            </div>
        </aside>
        <section class="records">
            <div class="header">
                <h5><span>Currently Viewing:</span></h5>
                <div class="search">
                    <form id="searchForm"> 
                        <div class="search-box">
                            <input type="text" class="form-control me-2" id="searchBar" placeholder="Search...">   
                            <button type="submit" class="search-button">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Scrollable table -->
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
                            <!-- Procurement data will be populated here -->
                        </tbody> 
                    </table> 
                </div>
            </div>
        </section>
    </main>
    <script src="/js/landingpage.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/fetch-saro-ilcdb')
                .then(response => response.json())
                .then(data => {
                    const ilcdbPanel = document.querySelector('.panel.ilcdb');
                    const remainingBalance = document.querySelector('.balance-box p');
                    ilcdbPanel.innerHTML = ''; // Clear any existing SARO entries

                    if (data.length > 0) {
                        data.forEach(saro => {
                            const saroElement = document.createElement('p');
                            saroElement.textContent = saro.saro_no;
                            saroElement.addEventListener('click', function() {
                                remainingBalance.textContent = `₱${Number(saro.current_budget).toLocaleString()}`;
                                fetchProcurementData(saro.saro_no);
                            });
                            ilcdbPanel.appendChild(saroElement);
                        });
                    } else {
                        const emptyMessage = document.createElement('p');
                        emptyMessage.textContent = 'No SARO records found.';
                        ilcdbPanel.appendChild(emptyMessage);
                    }
                })
                .catch(error => console.error('Error fetching SARO data:', error));
        });

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
    </script>
</body>
</html>