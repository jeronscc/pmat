<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Procurement Tracking and Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/forms.css">
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

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="container-fluid my-0">
                <div class="card shadow-lg p-4">
                    <div class="container mt-0">

                        <!-- Display PR number and Activity (retrieved from URL) -->
                        <div class="activity-info">
                            <h3>PR/Transaction Number: <span id="prtrNumber">{{ $prNumber }}</span></h3>
                            <h3>Activity Name: <span id="activityName">{{ $activity }}</span></h3>
                        </div>

                        <!-- Update Other Expense Form -->
                        <form id="otherExpenseForm">
                            @csrf
                            <!-- Hidden field for procurement_id (using pr_number from URL) -->
                            <input type="hidden" name="procurement_id" value="{{ $prNumber }}">

                            <hr class="my-4" style="border-top: 2px solid rgba(0, 0, 0, 0.6);">
                            <h2 class="fw-bold">Daily Travel Expenses Requirements</h2>
                            <h3>Budget Unit</h3>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Requirements</th>
                                            <th>Date Submitted</th>
                                            <th>Date Returned</th>
                                            <th>Indicator</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                                    data-bs-target="#requirementsModal1">
                                                    Upload Requirements
                                                </button>
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateSubmitted" name="dt_submitted"
                                                    value="{{ isset($record->dt_submitted) ? \Carbon\Carbon::parse($record->dt_submitted)->format('Y-m-d\TH:i') : '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned" name="dt_received"
                                                    value="{{ isset($record->dt_received) ? \Carbon\Carbon::parse($record->dt_received)->format('Y-m-d\TH:i') : '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Budget Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control" id="budgetSpent" name="budget_spent"
                                                        value="{{ old('budget_spent', $record->budget_spent ?? '') }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="table-buttons">
                                <button type="button" class="btn btn-danger" id="cancelChanges">Cancel</button>
                                <button type="button" class="btn btn-success" id="saveChanges">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="requirementsModal1" tabindex="-1" aria-labelledby="modalTitle1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle1">UPLOAD REQUIREMENTS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm1" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="procurement_id" name="procurement_id" value="{{ $prNumber }}">
                        <!-- ORS File Upload -->
                        <div class="mb-3">
                            <label for="orsFile" class="form-label">Upload ORS</label>
                            <input class="form-control" type="file" id="orsFile" name="orsFile">
                        </div>

                        <!-- DV File Upload -->
                        <div class="mb-3">
                            <label for="dvFile" class="form-label">Upload DV</label>
                            <input class="form-control" type="file" id="dvFile" name="dvFile">
                        </div>

                        <!-- Travel Order Upload -->
                        <div class="mb-3">
                            <label for="travelOrderFile" class="form-label">Upload Travel Order</label>
                            <input class="form-control" type="file" id="travelOrderFile" name="travelOrderFile">
                        </div>

                        <!-- Certificate of Appearance Upload -->
                        <div class="mb-3">
                            <label for="appearanceFile" class="form-label">Upload Certificate of Appearance</label>
                            <input class="form-control" type="file" id="appearanceFile" name="appearanceFile">
                        </div>

                        <!-- Official Travel Report Upload -->
                        <div class="mb-3">
                            <label for="reportFile" class="form-label">Upload Official Travel Report</label>
                            <input class="form-control" type="file" id="reportFile" name="reportFile">
                        </div>

                        <!-- Itinerary of Travel Upload -->
                        <div class="mb-3">
                            <label for="itineraryFile" class="form-label">Upload Itinerary of Travel</label>
                            <input class="form-control" type="file" id="itineraryFile" name="itineraryFile">
                        </div>

                        <!-- Certificate of Travel Completion Upload -->
                        <div class="mb-3">
                            <label for="certFile" class="form-label">Upload Certificate of Travel Completion</label>
                            <input class="form-control" type="file" id="certFile" name="certFile">
                        </div>

                        <!-- Container to display uploaded files -->
                        <div class="mb-3">
                            <label class="form-label">Uploaded Files</label>
                            <ul id="uploadedFilesListOtherExpense"></ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveBtn1">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="/js/menu.js"></script>
    <script src="/js/ilcdb/otherexpenseformIndicator.js"></script>
</body>

</html>
