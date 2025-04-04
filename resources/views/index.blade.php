<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <!-- Bootstrap CSS -->
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
            <div class="balance-box position-relative">
                <span id="currentSaroNo">Remaining Balance:</span>
                <p>₱0</p> <!-- Default value -->

                <!-- NTCA Label and Button Inline -->
                <div class="d-flex justify-content-between align-items-center">
                    <span id="ntcaLabel">NTCA:</span>
                    <button class="btn btn-primary btn-sm py-1 ntca-view-button" type="button" data-bs-toggle="modal" data-bs-target="#ntcaBreakdownModal">
                        View
                    </button>
                </div>

                <p id="ntcaBalance">₱0</p> <!-- Default value -->
            </div>
            <div class="modal fade" id="ntcaBreakdownModal" tabindex="-1" aria-labelledby="ntcaBreakdownModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:rgb(36, 36, 36); color: white;">
                            <h5 class="modal-title" id="ntcaBreakdownModalLabel">NTCA Balance Breakdown</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group" id="ntcaBreakdownList">
                                <!-- NTCA records will be dynamically populated here -->
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
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
            </div>
            <button class="accordion">ILCDB <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel ilcdb">
                <!-- SARO data will be populated here -->
            </div>

            <button class="accordion">DTC <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel dtc">
                <!-- Placeholder content -->
            </div>

            <button class="accordion">SPARK <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel spark">
                <!-- Placeholder content -->
            </div>

            <button class="accordion">PROJECT CLICK <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel project-click">
                <!-- Placeholder content -->

            </div>
        </aside>
        <section class="records">
            <div class="header">
                <h5><span id="viewingSaroNo">Currently Viewing:</span></h5>
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
            <!-- Scrollable table -->
            <div class="table-container">
                <div class="record-box">
                    <table>
                        <thead>
                            <tr>
                                <th>PR NUMBER</th>
                                <th>CATEGORY</th>
                                <th>ACTIVITY/PERSON-IN-CHARGE</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="procurementTable">
                            <!-- Procurement data will be populated here -->
                        </tbody>
                    </table>
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
                            <p><strong>NTCA No:</strong> <span id="modalNTCANo"></span></p>
                            <p><strong>Current Quarter:</strong> <span id="modalQuarter"></span></p>
                            <p><strong>Purchase Request:</strong> <span id="modalPurchaseRequest"></span></p>
                            <p><strong>Year:</strong> <span id="modalYear"></span></p>
                            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                            <p><strong id="modalActivityLabel">Activity:</strong> <span id="modalActivity"></span></p>
                        </div>
                        <div class="modal-footer">
                            <!-- Cancel Button -->
                            <button type="button" class="btn btn-secondary bg bg-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/ilcdb/landingpage.js"></script>
    <script src="/js/indexLoadIlcdb.js"></script>
    <script src="/js/indexLoadDtc.js"></script>
    <script src="/js/indexLoadClick.js"></script>
    <script src="/js/indexLoadSpark.js"></script>
    <script src="/js/shared.js"></script>
</body>

</html>