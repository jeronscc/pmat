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
                        <form action="/select-project">
                            <button type="submit">
                                <i class="fas fa-project"></i><img src="/assets/project-icon.png" alt=""> Project
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
                            $startYear = max(2026, date("Y")); // Start from 2026, or the current year if it's later
                            for ($year = $startYear; $year >= $startYear - 10; $year--) {
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
                        <img src="/assets/add.png" alt="Add Procurement" style="width: 20px; height: 20px; margin-bottom:5px;">
                    </button>
                    <div class="search-box">
                        <input type="text" class="form-control me-2" id="searchBar" placeholder="Search...">
                        <button type="submit" class="search-button">Search</button>
                    </div>
                </div>
            </div>

            <!-- Tabs Above Table -->
            <ul class="nav nav-tabs" id="procurementTabs">
                <li class="nav-item">
                    <a class="nav-link active" style="color: black;" id="tabAll-tab" data-bs-toggle="tab" href="#tabAll">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: black;" id="tabOngoing-tab" data-bs-toggle="tab" href="#tabPending">Pending</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: black;" id="tabOngoing-tab" data-bs-toggle="tab" href="#tabOngoing">Ongoing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: black;" id="tabOverdue-tab" data-bs-toggle="tab" href="#tabOverdue">Overdue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: black;" id="tabDone-tab" data-bs-toggle="tab" href="#tabDone">Done</a>
                </li>
            </ul>

            <!-- Table Container -->
            <div class="table-container tab-content">
                <!-- All Tab (Default) -->
                <div class="tab-pane fade show active" id="tabAll">
                    <div class="record-box">
                        <table>
                            <thead>
                                <tr>
                                    <th>PR NUMBER</th>
                                    <th>CATEGORY</th>
                                    <th>ACTIVITY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="procurementTable">
                                <!-- Default content (All) -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pending Tab -->
                <div class="tab-pane fade " id="tabPending">
                    <div class="record-box">
                        <table>
                            <thead>
                                <tr>
                                    <th>PR NUMBER</th>
                                    <th>CATEGORY</th>
                                    <th>ACTIVITY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="procurementTablePending">
                            <!-- Pending content (All) -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Ongoing Tab -->
                <div class="tab-pane fade" id="tabOngoing">
                    <div class="record-box">
                        <table>
                            <thead>
                                <tr>
                                    <th>PR NUMBER</th>
                                    <th>CATEGORY</th>
                                    <th>ACTIVITY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="procurementTableOngoing">
                                <!-- Ongoing data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Overdue Tab -->
                <div class="tab-pane fade" id="tabOverdue">
                    <div class="record-box">
                        <table>
                            <thead>
                                <tr>
                                    <th>PR NUMBER</th>
                                    <th>CATEGORY</th>
                                    <th>ACTIVITY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="procurementTableOverdue">
                                <!-- Overdue data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Done Tab -->
                <div class="tab-pane fade" id="tabDone">
                    <div class="record-box">
                        <table>
                            <thead>
                                <tr>
                                    <th>PR NUMBER</th>
                                    <th>CATEGORY</th>
                                    <th>ACTIVITY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="procurementTableDone">
                                <!-- Done data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </section>
    </div>
</div>
<div class="modal fade" id="procurementDetailsModal" tabindex="-1" aria-labelledby="procurementDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="procurementDetailsModalLabel">Procurement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Procurement Category:</strong> <span id="modalProcurementCategory"></span></p>
                <p><strong>Procurement No:</strong> <span id="modalProcurementNo"></span></p>
                <p><strong>SARO No:</strong> <span id="modalSaroNo"></span></p>
                <p><strong>Year:</strong> <span id="modalYear"></span></p>
                <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                <p><strong>Activity:</strong> <span id="modalActivity"></span></p>
            </div>
            <div class="modal-footer">
                <!-- Cancel Button -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <!-- Edit Button (still needs implementation) -->
                <button type="button" class="btn btn-primary">Edit</button>
            </div>
        </div>
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
                                <textarea class="form-control"  rows="3" id="saroDesc" placeholder="Enter Description"></textarea>
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
        <!-- Overdue Modal -->
        <div class="modal fade" id="overdueModal" tabindex="-1" aria-labelledby="overdueModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Added 'modal-lg' for better space -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="overdueModalLabel">Overdue Procurements</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;"> 
                            <!-- Scrollable Table -->
                            <table class="table table-bordered">

                                <tbody id="overdueProcurementList">
                                    <!-- Overdue procurements will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                <form id="procurementForm">
                    <div class="mb-3">
                        <label for="category" class="form-label">Select Procurement Category</label>
                        <select class="form-select" id="category">
                            <option value="" disabled selected>Select Category</option>
                            <option value="SVP">SVP</option>
                            <option value="Honoraria">Honoraria</option>
                            <option value="Other expense">Other Expenses</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pr-number" class="form-label">PR/TRANSACTION NUMBER</label>
                        <input type="text" class="form-control" id="pr-number" placeholder="Enter PR Number">
                    </div>
                    <div class="mb-3">
                        <label for="pr-year" class="form-label">YEAR</label>
                        <select class="form-select" id="pr-year" placeholder="Enter Activity">
                            <option value="" disabled selected>Select Year</option>
                            <?php
                            $startYear = max(2026, date("Y")); // Start from 2026, or the current year if it's later
                            for ($year = $startYear; $year >= $startYear - 5; $year--) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="saro-number" class="form-label">SARO NUMBER</label>
                        <select class="form-select" id="saro-number">
                            <option value="" disabled selected>Select SARO Number</option>
                            <!-- SARO options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="activity" class="form-label" id="activityLabel">ACTIVITY</label>
                        <input type="text" class="form-control" id="activity" placeholder="Enter Activity">
                    </div>
                    <div class="mb-3">
                        <label for="pr-amount" class="form-label">PR AMOUNT</label>
                        <input type="number" class="form-control" id="pr-amount" placeholder="Enter PR Amount">
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Custom JS -->
        <script src="/js/menu.js"></script>
        <script src="/js/ilcdb/fetchSaro.js"></script>
        <script src="/js/ilcdb/fetchProcurement.js"></script>
        <script src="/js/ilcdb/searchProcurement.js"></script>
        <script src="/js/ilcdb/addProcurement.js"></script>
        <script src="/js/ilcdb/addSaro.js"></script>
        <script src="/js/ilcdb/filterSaroByYear.js"></script>
        <script src="/sarohover.js"></script>
        <script src="/js/ilcdb/overdueNotification.js"></script>
        <!-- Bootstrap JS (Optional, only needed for dropdowns, modals, etc.) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
    // Listen for category selection change
    document.getElementById('category').addEventListener('change', function() {
        const category = this.value;
        
        // Reset form fields before changing
        resetForm();

        // Get current year
        const currentYear = new Date().getFullYear();
        // Generate PR number with random 5 digits
        const generatePRNumber = () => `PROC-${currentYear}-${Math.floor(10000 + Math.random() * 90000)}`;

        if (category === 'SVP') {
            // Retain original form for SVP
            document.getElementById('activityLabel').innerText = 'ACTIVITY';
            document.getElementById('pr-number').setAttribute('placeholder', 'Enter PR Number');
            document.getElementById('pr-number').removeAttribute('readonly');
            document.getElementById('activity').setAttribute('placeholder', 'Enter Activity');
            document.getElementById('description').setAttribute('placeholder', 'Enter Description');
        } else if (category === 'Honoraria') {
            // Modify form for Honoraria
            document.getElementById('activityLabel').innerText = 'NAME OF SPEAKER';
            document.getElementById('pr-number').value = generatePRNumber();  // Auto-generate PR number
            document.getElementById('pr-number').setAttribute('readonly', 'true'); // Make PR Number non-editable
            document.getElementById('activity').setAttribute('placeholder', 'Enter name of the resource speaker');
            document.getElementById('description').setAttribute('placeholder', 'Enter title of the training');
        } else if (category === 'Other expense') {
            // Modify form for Other expense
            document.getElementById('activityLabel').innerText = 'NAME OF TRAVELLER';
            document.getElementById('pr-number').value = generatePRNumber();  // Auto-generate PR number
            document.getElementById('pr-number').setAttribute('readonly', 'true'); // Make PR Number non-editable
            document.getElementById('activity').setAttribute('placeholder', 'Enter name of traveller');
            document.getElementById('description').setAttribute('placeholder', 'Enter the purpose of travel');
        }
    });

    // Reset the form fields
    function resetForm() {
        document.getElementById('activity').value = '';
        document.getElementById('description').value = '';
        document.getElementById('pr-number').value = '';  // Clear PR Number
        document.getElementById('pr-year').value = '';
        document.getElementById('saro-number').value = '';
        document.getElementById('pr-amount').value = '';  // Reset PR Amount
    }
</script>
    </body>
</html>