<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Procurement Tracking and Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/forms.css">
    <link rel="stylesheet" href="/css/mainheader.css">
    <link rel="stylesheet" href="/css/sidenav.css">
</head>

<body>
    <header class="d-flex align-items-center justify-content-between bg-black text-white p-3 shadow" id="stickyHeader">
        <div class="logo d-flex align-items-center">
            <img src="/assets/dict-logo.png" alt="DICT Logo" class="img-fluid" id="dictLogo">
            <img src="/assets/project_click_logo-2.png" alt="DTC Logo" class="img-fluid ms-2">
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
                <form action="/homepage-projectClick">
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
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="container-fluid my-0">
                <div class="card shadow-lg p-4">
                    <div class="container mt-5">
                        <div class="activity-info">
                            <h3><b>PR Number: </b><span id="prNumber">{{ $prNumber }}</span></h3>
                            <h3><b>Activity Name: </b><span id="activityName">{{ $activityName }}</span></h3>
                            <h3><b>Description: </b><span id="procurement-description">{{ $description }}</span></h3>

                        </div>
                        <hr class="my-4" style="border-top: 2px solid rgba(0, 0, 0, 0.6);">
                        <!-- Hidden fields to pass along the procurement id and activity -->
                        <input type="hidden" id="procurementId" name="procurement_id" value="{{ $prNumber }}">

                        <!-- The form (no <form> tag is required if we use AJAX, but wrapping it helps) -->
                        <form id="procurementForm">
                            @csrf
                            <!-- Hidden fields to pass along the procurement id and activity -->
                            <input type="hidden" id="procurementId" name="procurement_id" value="{{ $prNumber }}">
                            <h2 class="fw-bold">Pre-Procurement Requirements</h2>
                            <h3>Supply Unit</h3>
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
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal1">
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
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal3">
                                                    Upload Requirements
                                                </button>
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateSubmitted3" name="dt_submitted3" value="{{ $record->dt_submitted3 ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned3" name="dt_received3" value="{{ $record->dt_received3 ?? '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator3" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h2 class="fw-bold">Post-Procurement Requirements</h2>
                            <h3>Supply Unit</h3>
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
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal4">
                                                    Upload Requirements
                                                </button>
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateSubmitted4" name="dt_submitted4" value="{{ $record->dt_submitted4 ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned4" name="dt_received4" value="{{ $record->dt_received4 ?? '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator4" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal5">
                                                    Upload Requirements
                                                </button>
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateSubmitted5" name="dt_submitted5" value="{{ $record->dt_submitted5 ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned5" name="dt_received5" value="{{ $record->dt_received5 ?? '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator5" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
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
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal6">
                                                    Upload Requirements
                                                </button>
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateSubmitted6" name="dt_submitted6" value="{{ $record->dt_submitted6 ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="datetime-local" class="form-control" id="dateReturned6" name="dt_received6" value="{{ $record->dt_received6 ?? '' }}">
                                            </td>
                                            <td><span class="indicator" id="indicator6" style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Budget Spent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control" id="budgetSpent" name="budget_spent"
                                                            value="{{ $record->budget_spent ?? '' }}" readonly>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="table-buttons">
                                <button type="button" class="btn btn-secondary" id="cancelChanges">Cancel</button>
                                <button type="button" class="btn btn-primary" id="saveChanges">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Modal 1 -->
    <div class="modal fade" id="requirementsModal1" tabindex="-1" aria-labelledby="modalTitle1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle1">UPLOAD REQUIREMENTS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm1" enctype="multipart/form-data">
                        @csrf
                        <!-- Reminder about file requirements -->
                        <div class="alert alert-info">
                            Only PDF files are accepted. The file size must not exceed 5 MB.
                        </div>
                        <div class="mb-3">
                            <label for="appFile1" class="form-label">APP / PPMP</label>
                            <input class="form-control" type="file" id="appFile1" name="appFile">
                            <div id="appFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="saroFile1" class="form-label">SARO</label>
                            <input class="form-control" type="file" id="saroFile1" name="saroFile">
                            <div id="saroFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="budgetFile1" class="form-label">Budget Breakdown</label>
                            <input class="form-control" type="file" id="budgetFile1" name="budgetFile">
                            <div id="budgetFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="distributionFile1" class="form-label">Distribution List</label>
                            <input class="form-control" type="file" id="distributionFile1" name="distributionFile">
                            <div id="distributionFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="poiFile1" class="form-label">POI / Activity Design</label>
                            <input class="form-control" type="file" id="poiFile1" name="poiFile">
                            <div id="poiFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="researchFile1" class="form-label">Market Research</label>
                            <input class="form-control" type="file" id="researchFile1" name="researchFile">
                            <div id="researchFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="purchaseFile1" class="form-label">Purchase Request</label>
                            <input class="form-control" type="file" id="purchaseFile1" name="purchaseFile">
                            <div id="purchaseFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="quotationsFile1" class="form-label">Quotations</label>
                            <input class="form-control" type="file" id="quotationsFile1" name="quotationsFile">
                            <div id="quotationsFile1Link"></div>
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

    <!-- Modal 2 -->
    <div class="modal fade" id="requirementsModal2" tabindex="-1" aria-labelledby="modalTitle2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle2">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm2" enctype="multipart/form-data">
                        @csrf
                        <!-- Reminder about file requirements -->
                        <div class="alert alert-info">
                            Only PDF files are accepted. The file size must not exceed 5 MB.
                        </div>
                        <div class="mb-3">
                            <label for="poFile2" class="form-label">Purchase Order</label>
                            <input class="form-control" type="file" id="poFile2" name="poFile">
                            <div id="poFile2Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="absFile2" class="form-label">Abstract / Philgeps Posting*</label>
                            <input class="form-control" type="file" id="absFile2" name="absFile">
                            <div id="absFile2Link"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Previously Submitted Requirements</label>
                            <div class="mb-3">
                                <label for="purchaseFile2" class="form-label" id="purchaseFileLabel2">Purchase Request</label>
                                <div id="purchaseFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="quotationsFile2" class="form-label" id="quotationsFileLabel2">Quotations</label>
                                <div id="quotationsFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="appFile2" class="form-label" id="appFileLabel2">APP/PPMP</label>
                                <div id="appFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="saroFile2" class="form-label" id="saroFileLabel2">SARO</label>
                                <div id="saroFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="budgetFile2" class="form-label" id="budgetFileLabel2">Budget Breakdown</label>
                                <div id="budgetFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="distributionFile2" class="form-label" id="distributionFileLabel2">Distribution List</label>
                                <div id="distributionFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="poiFile2" class="form-label" id="poiFileLabel2">POI / Activity Design</label>
                                <div id="poiFile2Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="researchFile2" class="form-label" id="researchFileLabel2">Market Research</label>
                                <div id="researchFile2Link"></div>
                            </div>
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

    <!-- Modal 3 -->
    <div class="modal fade" id="requirementsModal3" tabindex="-1" aria-labelledby="modalTitle3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle3">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm3" enctype="multipart/form-data">
                        @csrf
                        <!-- Reminder about file requirements -->
                        <div class="alert alert-info">
                            Only PDF files are accepted. The file size must not exceed 5 MB.
                        </div>
                        <div class="mb-3">
                            <label for="orsFile3" class="form-label">ORS (Obligation Request and Status)</label>
                            <input class="form-control" type="file" id="orsFile3" name="orsFile">
                            <div id="orsFile3Link"></div>
                        </div>
                        <!--
                    <div class="mb-3">
                        <label for="poFile3" class="form-label">Purchase Order</label>
                        <input class="form-control" type="file" id="poFile3" name="poFile">
                        <div id="poFile3Link"></div>
                    </div>
                    <div class="mb-3">
                        <label for="absFile3" class="form-label">Abstract / Philgeps Posting*</label>
                        <input class="form-control" type="file" id="absFile3" name="absFile">
                        <div id="absFile3Link"></div>
                    </div>
                -->
                        <div class="mb-3">
                            <label class="form-label">Previously Submitted Requirements (For Reference)</label>
                            <div class="mb-3">
                                <label for="poFile3" class="form-label" id="poFile3">Purchase Order</label>
                                <div id="poFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="absFile3" class="form-label">Abstract / Philgeps Posting*</label>
                                <div id="absFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="purchaseFile3" class="form-label" id="purchaseFileLabel3">Purchase Request</label>
                                <div id="purchaseFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="quotationsFile3" class="form-label" id="quotationsFileLabel3">Quotations</label>
                                <div id="quotationsFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="appFile3" class="form-label" id="appFileLabel3">APP/PPMP</label>
                                <div id="appFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="saroFile3" class="form-label" id="saroFileLabel3">SARO</label>
                                <div id="saroFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="budgetFile3" class="form-label" id="budgetFileLabel3">Budget Breakdown</label>
                                <div id="budgetFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="distributionFile3" class="form-label" id="distributionFileLabel3">Distribution List</label>
                                <div id="distributionFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="poiFile3" class="form-label" id="poiFileLabel3">POI / Activity Design</label>
                                <div id="poiFile3Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="researchFile3" class="form-label" id="researchFileLabel3">Market Research</label>
                                <div id="researchFile3Link"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBtn3">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 4 -->
    <div class="modal fade" id="requirementsModal4" tabindex="-1" aria-labelledby="modalTitle4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle4">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm4" enctype="multipart/form-data">
                        @csrf
                        <!-- Reminder about file requirements -->
                        <div class="alert alert-info">
                            Only PDF files are accepted. The file size must not exceed 5 MB.
                        </div>
                        <div class="mb-3">
                            <label for="attendanceFile4" class="form-label">Attendance Sheet</label>
                            <input class="form-control" type="file" id="attendanceFile4" name="attendanceFile">
                            <div id="attendanceFile4Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="cocFile4" class="form-label">Certificate of Completion/Satisfaction for Supplier</label>
                            <input class="form-control" type="file" id="cocFile4" name="cocFile">
                            <div id="cocFile4Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="photoFile4" class="form-label">Photo</label>
                            <input class="form-control" type="file" id="photoFile4" name="photoFile">
                            <div id="photoFile4Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="soaFile4" class="form-label">SOA / Billing Statement</label>
                            <input class="form-control" type="file" id="soaFile4" name="soaFile">
                            <div id="soaFile4Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="drFile4" class="form-label">Delivery Receipt</label>
                            <input class="form-control" type="file" id="drFile4" name="drFile">
                            <div id="drFile4Link"></div>
                        </div>
                        <div class="mb-3">
                            <label for="dlFile4" class="form-label">Distribution List (Receiving Copy)</label>
                            <input class="form-control" type="file" id="dlFile4" name="dlFile">
                            <div id="dlFile4Link"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBtn4">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 5 -->
    <div class="modal fade" id="requirementsModal5" tabindex="-1" aria-labelledby="modalTitle5" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle5">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm5" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Previously Uploaded Documents (ORS, PO, Abstract, Philgeps Posting, IAR, ICS/PAR, Request for Inspection)</label>
                            <div class="mb-3">
                                <label for="orsFile5" class="form-label">ORS (Obligation Request and Status)</label>
                                <div id="orsFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="poFile5" class="form-label">Purchase Order</label>
                                <div id="poFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="absFile5" class="form-label">Abstract / Philgeps Posting*</label>
                                <div id="absFile5Link"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Previously Uploaded Documents (Attendance, Certificate, Photos, SOA, DR, Distribution List)</label>
                            <div class="mb-3">
                                <label for="attendanceFile5" class="form-label">Attendance Sheet</label>
                                <div id="attendanceFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="cocFile5" class="form-label">Certificate of Completion/Satisfaction for Supplier</label>
                                <div id="cocFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="photoFile5" class="form-label">Photo</label>
                                <div id="photoFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="soaFile5" class="form-label">SOA / Billing Statement</label>
                                <div id="soaFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="drFile5" class="form-label">Delivery Receipt</label>
                                <div id="drFile5Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="dlFile5" class="form-label">Distribution List (Receiving Copy)</label>
                                <div id="dlFile5Link"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Previously Uploaded Documents (PR, Quotations, APP/PPMP, SARO, Budget Breakdown, Distribution List, POI/Activity Design, Market Research)</label>
                            <div id="allReqsFilePreview5">
                                <div class="mb-3">
                                    <label for="purchaseFile5" class="form-label" id="purchaseFileLabel5">Purchase Request</label>
                                    <div id="purchaseFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="quotationsFile5" class="form-label" id="quotationsFileLabel5">Quotations</label>
                                    <div id="quotationsFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="appFile5" class="form-label" id="appFileLabel5">APP/PPMP</label>
                                    <div id="appFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="saroFile5" class="form-label" id="saroFileLabel5">SARO</label>
                                    <div id="saroFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="budgetFile5" class="form-label" id="budgetFileLabel5">Budget Breakdown</label>
                                    <div id="budgetFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="distributionFile5" class="form-label" id="distributionFileLabel5">Distribution List</label>
                                    <div id="distributionFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="poiFile5" class="form-label" id="poiFileLabel5">POI / Activity Design</label>
                                    <div id="poiFile5Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="researchFile5" class="form-label" id="researchFileLabel5">Market Research</label>
                                    <div id="researchFile5Link"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 6 -->
    <div class="modal fade" id="requirementsModal6" tabindex="-1" aria-labelledby="modalTitle6" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle6">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form id="requirementsForm6" enctype="multipart/form-data">
                        @csrf
                        <div class="alert alert-info">
                            Only PDF files are accepted. The file size must not exceed 5 MB.
                        </div>
                        <div class="mb-3">
                            <label for="dvFile1" class="form-label">Disbursement Voucher</label>
                            <input class="form-control" type="file" id="dvFile1" name="dvFile">
                            <div id="dvFile1Link"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uploaded Documents (ORS, PO, Abstract, Philgeps Posting, IAR, ICS/PAR, Request for Inspection)</label>
                            <div class="mb-3">
                                <label for="orsFile6" class="form-label">ORS (Obligation Request and Status)</label>
                                <div id="orsFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="poFile6" class="form-label">Purchase Order</label>
                                <div id="poFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="absFile6" class="form-label">Abstract / Philgeps Posting</label>
                                <div id="absFile6Link"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uploaded Documents (Attendance, Certificate, Photos, SOA, DR, Distribution List)</label>
                            <div class="mb-3">
                                <label for="attendanceFile6" class="form-label">Attendance Sheet</label>
                                <div id="attendanceFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="cocFile6" class="form-label">Certificate of Completion/Satisfaction for Supplier</label>
                                <div id="cocFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="photoFile6" class="form-label">Photo</label>
                                <div id="photoFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="soaFile6" class="form-label">SOA / Billing Statement</label>
                                <div id="soaFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="drFile6" class="form-label">Delivery Receipt</label>
                                <div id="drFile6Link"></div>
                            </div>
                            <div class="mb-3">
                                <label for="dlFile6" class="form-label">Distribution List (Receiving Copy)</label>
                                <div id="dlFile6Link"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uploaded Documents (PR, Quotations, APP/PPMP, SARO, Budget Breakdown, Distribution List, POI/Activity Design, Market Research)</label>
                            <div id="allReqsFilePreview6">
                                <div class="mb-3">
                                    <label for="purchaseFile6" class="form-label" id="purchaseFileLabel6">Purchase Request</label>
                                    <div id="purchaseFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="quotationsFile6" class="form-label" id="quotationsFileLabel6">Quotations</label>
                                    <div id="quotationsFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="appFile6" class="form-label" id="appFileLabel6">APP/PPMP</label>
                                    <div id="appFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="saroFile6" class="form-label" id="saroFileLabel6">SARO</label>
                                    <div id="saroFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="budgetFile6" class="form-label" id="budgetFileLabel2">Budget Breakdown</label>
                                    <div id="budgetFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="distributionFile6" class="form-label" id="distributionFileLabel6">Distribution List</label>
                                    <div id="distributionFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="poiFile6" class="form-label" id="poiFileLabel6">POI / Activity Design</label>
                                    <div id="poiFile6Link"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="researchFile6" class="form-label" id="researchFileLabel6">Market Research</label>
                                    <div id="researchFile6Link"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBtn6">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/menu.js"></script>
    <script src="/js/click/procurementformIndicator.js"></script>
    <script src="/js/click/addProcurementForm.js"></script>
    <script src="/js/click/requirementsUploadProcurement.js"></script>
    <script>
        /*
// Get references to the date fields
const dateReturned1 = document.getElementById('dateReturned1');
const dateSubmitted2 = document.getElementById('dateSubmitted2');
const dateReturned2 = document.getElementById('dateReturned2');
const dateSubmitted3 = document.getElementById('dateSubmitted3');
const dateReturned3 = document.getElementById('dateReturned3');
const dateSubmitted4 = document.getElementById('dateSubmitted4');
const dateReturned4 = document.getElementById('dateReturned4');
const dateSubmitted5 = document.getElementById('dateSubmitted5');
const dateReturned5 = document.getElementById('dateReturned5');
const dateSubmitted6 = document.getElementById('dateSubmitted6');

// Add event listener to dateReturned1 to check if it has a value
dateReturned1.addEventListener('change', function() {
    if (dateReturned1.value) {
        // Enable dateSubmitted2 if dateReturned1 has a value
        dateSubmitted2.removeAttribute('readonly');
        dateReturned2.removeAttribute('readonly');
    } else {
        // Make dateSubmitted2 readonly if dateReturned1 is empty
        dateSubmitted2.setAttribute('readonly', 'true');
        dateReturned2.setAttribute('readonly', 'true');
    }
});

dateReturned2.addEventListener('change', function() {
    if (dateReturned2.value) {
        // Enable dateSubmitted3 if dateReturned2 has a value
        dateSubmitted3.removeAttribute('readonly');
        dateReturned3.removeAttribute('readonly');
    } else {
        // Make dateSubmitted3 readonly if dateReturned2 is empty
        dateSubmitted3.setAttribute('readonly', 'true');
        dateReturned3.setAttribute('readonly', 'true');
    }
});

dateReturned3.addEventListener('change', function() {
    if (dateReturned3.value) {
        // Enable dateSubmitted4 if dateReturned3 has a value
        dateSubmitted4.removeAttribute('readonly');
        dateReturned4.removeAttribute('readonly');
    } else {
        // Make dateSubmitted4 readonly if dateReturned3 is empty
        dateSubmitted4.setAttribute('readonly', 'true');
        dateReturned4.setAttribute('readonly', 'true');
    }
});

dateReturned4.addEventListener('change', function() {
    if (dateReturned4.value) {
        // Enable dateSubmitted5 if dateReturned4 has a value
        dateSubmitted5.removeAttribute('readonly');
        dateReturned5.removeAttribute('readonly');
    } else {
        // Make dateSubmitted5 readonly if dateReturned4 is empty
        dateSubmitted5.setAttribute('readonly', 'true');
        dateReturned5.setAttribute('readonly', 'true');
    }
});

dateReturned5.addEventListener('change', function() {
    if (dateReturned5.value) {
        // Enable dateSubmitted6 if dateReturned5 has a value
        dateSubmitted6.removeAttribute('readonly');
        dateReturned6.removeAttribute('readonly');
    } else {
        // Make dateSubmitted6 readonly if dateReturned5 is empty
        dateSubmitted6.setAttribute('readonly', 'true');
        dateReturned6.setAttribute('readonly', 'true');
    }
});

// Initially set dateSubmitted2 to readonly if dateReturned1 is empty
if (!dateReturned1.value) {
    dateSubmitted2.setAttribute('readonly', 'true');
    dateReturned2.setAttribute('readonly', 'true');
}

if (!dateReturned2.value) {
    dateSubmitted3.setAttribute('readonly', 'true');
    dateReturned3.setAttribute('readonly', 'true');
}

if (!dateReturned3.value) {
    dateSubmitted4.setAttribute('readonly', 'true');
    dateReturned4.setAttribute('readonly', 'true');
}

if (!dateReturned4.value) {
    dateSubmitted5.setAttribute('readonly', 'true');
    dateReturned5.setAttribute('readonly', 'true');
}

if (!dateReturned5.value) {
    dateSubmitted6.setAttribute('readonly', 'true');
    dateReturned6.setAttribute('readonly', 'true');
}
*/
    </script>
</body>

</html>