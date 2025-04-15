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
            <img src="/assets/spark-logo.png" alt="DTC Logo" class="img-fluid ms-2">
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
                <form action="/homepage-spark">
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
                <form action="{{ route('accounts') }}">
                    <button type="submit">
                        <i class="fas fa-users"></i><img src="/assets/account_icon.png" alt=""> Accounts
                    </button>
                </form>
            </li>
            <li>
                <form action="{{ url('/reports') }}">
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
                            <h3><b>PR Number: </b><span id="prtrNumber">{{ $prNumber }}</span></h3>
                            <h3><b>Name of Speaker: </b><span id="activityName">{{ $activity }}</span></h3>
                            <h3><b>Description: </b><span id="procurement-description">{{ $description }}</span></h3>
                            <h3><b>Actual Amount: </b><span id="procurement-pr-amount">{{ number_format($pr_amount, 2) }}</span></h3>
                        </div>

                        <hr class="my-4" style="border-top: 2px solid rgba(0, 0, 0, 0.6);">
                        <!-- Hidden fields to pass along the procurement id and activity -->
                        <input type="hidden" id="procurementId" name="procurement_id" value="{{ $prNumber }}">

                        <!-- Update Honoraria Form -->
                        <form id="honorariaForm">
                            @csrf
                            <!-- Hidden field for procurement_id (using pr_number from URL) -->
                            <input type="hidden" id="procurementId" name="procurement_id" value="{{ $prNumber }}">

                            <hr class="my-4" style="border-top: 2px solid rgba(0, 0, 0, 0.6);">
                            <h2 class="fw-bold">Honoraria for Speakers Requirements</h2>
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
                                                <input type="datetime-local" class="form-control" id="dateSubmitted1" name="dt_submitted1" value="{{ $record->dt_submitted1 ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned1" name="dt_received1" value="{{ $record->dt_received1 ?? '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator1" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h3>Accounting Unit</h3>
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
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal2">
                                                    Upload Requirements
                                                </button>
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateSubmitted2" name="dt_submitted2" value="{{ $record->dt_submitted2 ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned2" name="dt_received2" value="{{ $record->dt_received2 ?? '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator2" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h3>NTCA</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>NTCA Number</th>
                                            <th>Quarter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" id="ntca_no" name="ntca_no" placeholder="NTCA Number"
                                                    value="{{ old('ntca_no', $record->ntca_no ?? '') }}">
                                            </td>
                                            <td>
                                                <select class="form-select" id="quarter" name="quarter" data-saved-value="{{ $record->quarter ?? '' }}">
                                                    <option value="" disabled {{ empty($record->quarter) ? 'selected' : '' }}>Select Current Quarter</option>
                                                    <option value="First Quarter" {{ $record->quarter == 'First Quarter' ? 'selected' : '' }}>First Quarter</option>
                                                    <option value="Second Quarter" {{ $record->quarter == 'Second Quarter' ? 'selected' : '' }}>Second Quarter</option>
                                                    <option value="Third Quarter" {{ $record->quarter == 'Third Quarter' ? 'selected' : '' }}>Third Quarter</option>
                                                    <option value="Fourth Quarter" {{ $record->quarter == 'Fourth Quarter' ? 'selected' : '' }}>Fourth Quarter</option>
                                                </select>
                                            </td>
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
                                <button type="button" class="btn btn-secondary" id="cancelChanges">Cancel</button>
                                <button type="button" class="btn btn-primary" id="saveChanges">Save</button>
                            </div>
                        </form>
                    </div>

                    <!-- Modals -->
                    <div class="modal fade" id="requirementsModal1" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title" id="modalTitle">UPLOAD REQUIREMENTS</h5>
                                </div>
                                <div class="modal-body">
                                    <form id="requirementsForm1" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="procurement_id" name="procurement_id" value="{{ $prNumber }}">
                                        <!-- Reminder about file requirements -->
                                        <div class="alert alert-info">
                                            Only PDF files are accepted. The file size must not exceed 5 MB.
                                        </div>
                                        <!-- ORS File Upload -->
                                        <div class="mb-3">
                                            <label for="orsFile1" class="form-label">Upload ORS</label>
                                            <input class="form-control" type="file" id="orsFile1" name="orsFile">
                                            <div id="orsFile1Link"></div>
                                        </div>
                                        <!-- Service Contract Upload -->
                                        <div class="mb-3">
                                            <label for="contractFile1" class="form-label">Upload Service Contract</label>
                                            <input class="form-control" type="file" id="contractFile1" name="contractFile">
                                            <div id="contractFile1Link"></div>
                                        </div>

                                        <!-- Certificate Honoraria Classification Upload -->
                                        <div class="mb-3">
                                            <label for="classificationFile1" class="form-label">Upload Certificate Honoraria Classification</label>
                                            <input class="form-control" type="file" id="classificationFile1" name="classificationFile">
                                            <div id="classificationFile1Link"></div>
                                        </div>

                                        <!-- Resume/CV Upload -->
                                        <div class="mb-3">
                                            <label for="resumeFile1" class="form-label">Upload Resume/CV</label>
                                            <input class="form-control" type="file" id="resumeFile1" name="resumeFile">
                                            <div id="resumeFile1Link"></div>
                                        </div>

                                        <!-- Government ID Upload -->
                                        <div class="mb-3">
                                            <label for="govidFile1" class="form-label">Upload Government ID</label>
                                            <input class="form-control" type="file" id="govidFile1" name="govidFile">
                                            <div id="govidFile1Link"></div>
                                        </div>

                                        <!-- Payslip/Certificate of Gross Income Upload -->
                                        <div class="mb-3">
                                            <label for="payslipFile1" class="form-label">Upload Payslip/Certificate of Gross Income</label>
                                            <input class="form-control" type="file" id="payslipFile1" name="payslipFile">
                                            <div id="payslipFile1Link"></div>
                                        </div>

                                        <!-- TIN and Bank Account Details Upload -->
                                        <div class="mb-3">
                                            <label for="bankFile1" class="form-label">Upload TIN and Bank Account Details</label>
                                            <input class="form-control" type="file" id="bankFile1" name="bankFile">
                                            <div id="bankFile1Link"></div>
                                        </div>

                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveBtn1">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Modal 2-->
                    <div class="modal fade" id="requirementsModal2" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title" id="modalTitle">UPLOAD REQUIREMENTS</h5>
                                </div>
                                <div class="modal-body">
                                    <form id="requirementsForm2" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="procurement_id" name="procurement_id" value="{{ $prNumber }}">
                                        <!-- Reminder about file requirements -->
                                        <div class="alert alert-info">
                                            Only PDF files are accepted. The file size must not exceed 5 MB.
                                        </div>

                                        <!-- DV File Upload -->
                                        <div class="mb-3">
                                            <label for="dvFile2" class="form-label">Upload DV</label>
                                            <input class="form-control" type="file" id="dvFile2" name="dvFile">
                                            <div id="dvFile2Link"></div>
                                        </div>

                                        <!-- Terminal Report Upload -->
                                        <div class="mb-3">
                                            <label for="reportFile2" class="form-label">Upload Terminal Report</label>
                                            <input class="form-control" type="file" id="reportFile2" name="reportFile">
                                            <div id="reportFile2Link"></div>
                                        </div>

                                        <!-- Attendance Upload -->
                                        <div class="mb-3">
                                            <label for="attendanceFile2" class="form-label">Upload Attendance</label>
                                            <input class="form-control" type="file" id="attendanceFile2" name="attendanceFile">
                                            <div id="attendanceFile2Link"></div>
                                        </div>

                                        <!-- Certificate of Services Rendered Upload -->
                                        <div class="mb-3">
                                            <label for="certFile2" class="form-label">Upload Certificate of Services Rendered</label>
                                            <input class="form-control" type="file" id="certFile2" name="certFile">
                                            <div id="certFile2Link"></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveBtn2">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Optionally include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/menu.js"></script>
    <!--<script src="/js/ilcdb/honorariaUploadCheck.js"></script>-->
    <script src="/js/spark/honorariaformIndicator.js"></script>
    <script src="/js/spark/requirementsUpload.js"></script>
</body>

</html>