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
                <form action="" >
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
            <h3>PR Number: <span id="prNumber">{{ $prNumber }}</span></h3>
            <h3>Activity Name: <span id="activityName">{{ $activityName }}</span></h3>
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
                        <td><span class="indicator" id="indicator2"  style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
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
                        <td><span class="indicator" id="indicator3"  style="display: inline-block; width: 80px; padding: 5px; border-radius: 5px; text-align: center;"></span></td>
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
                    <div class="mb-3">
                        <label for="appFile1" class="form-label">APP / PPMP</label>
                        <input class="form-control" type="file" id="appFile1" name="appFile">
                    </div>
                    <div class="mb-3">
                        <label for="saroFile1" class="form-label">SARO</label>
                        <input class="form-control" type="file" id="saroFile1" name="saroFile">
                    </div>
                    <div class="mb-3">
                        <label for="budgetFile1" class="form-label">Budget Breakdown</label>
                        <input class="form-control" type="file" id="budgetFile1" name="budgetFile">
                    </div>
                    <div class="mb-3">
                        <label for="distributionFile1" class="form-label">Distribution List</label>
                        <input class="form-control" type="file" id="distributionFile1" name="distributionFile">
                    </div>
                    <div class="mb-3">
                        <label for="poiFile1" class="form-label">POI / Activity Design</label>
                        <input class="form-control" type="file" id="poiFile1" name="poiFile">
                    </div>
                    <div class="mb-3">
                        <label for="researchFile1" class="form-label">Market Research</label>
                        <input class="form-control" type="file" id="researchFile1" name="researchFile">
                    </div>
                    <div class="mb-3">
                        <label for="purchaseFile1" class="form-label">Purchase Request</label>
                        <input class="form-control" type="file" id="purchaseFile1" name="purchaseFile">
                    </div>
                    <div class="mb-3">
                        <label for="quotationsFile1" class="form-label">Quotations</label>
                        <input class="form-control" type="file" id="quotationsFile1" name="quotationsFile">
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

<div class="modal fade" id="requirementsModal2" tabindex="-1" aria-labelledby="modalTitle2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle2">REQUIREMENTS DETAILS</h5>
            </div>
            <div class="modal-body">
                <form id="requirementsForm2" enctype="multipart/form-data">
                    <!-- Upload for Purchase Order -->
                    <div class="mb-3">
                        <label for="poFile1" class="form-label">Purchase Order</label>
                        <input class="form-control" type="file" id="poFile1" name="poFile">
                    </div>

                    <!-- Upload for Abstract/Philgeps posting -->
                    <div class="mb-3">
                        <label for="absFile1" class="form-label">Abstract / Philgeps Posting*</label>
                        <input class="form-control" type="file" id="absFile1" name="absFile">
                    </div>

                    <!-- Optional: Other files (already submitted files - you can show a note here) -->
                    <div class="mb-3">
                        <label class="form-label">Previously Submitted Requirements</label>
                        <ul>
                            <li>Purchase Request</li>
                            <li>Quotations</li>
                            <li>APP / PPMP</li>
                            <li>SARO</li>
                            <li>Budget Breakdown</li>
                            <li>Distribution List</li>
                            <li>POI / Activity Design</li>
                            <li>Market Research</li>
                        </ul>
                        <small class="text-muted">These were already submitted earlier and are listed here for reference.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBtn2">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="requirementsModal3" tabindex="-1" aria-labelledby="modalTitle3" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle3">REQUIREMENTS DETAILS</h5>
            </div>
            <div class="modal-body">
                <form id="requirementsForm3" enctype="multipart/form-data">
                    <!-- ORS Upload -->
                    <div class="mb-3">
                        <label for="orsFile" class="form-label">ORS (Obligation Request and Status)</label>
                        <input class="form-control" type="file" id="orsFile" name="orsFile">
                    </div>

                    <!-- Purchase Order Upload -->
                    <div class="mb-3">
                        <label for="poFile3" class="form-label">Purchase Order</label>
                        <input class="form-control" type="file" id="poFile3" name="poFile">
                    </div>

                    <!-- Abstract / Philgeps Posting Upload -->
                    <div class="mb-3">
                        <label for="absFile3" class="form-label">Abstract / Philgeps Posting*</label>
                        <input class="form-control" type="file" id="absFile3" name="absFile">
                    </div>

                    <!-- Previously Submitted Files - Listed (No upload, just for reference) -->
                    <div class="mb-3">
                        <label class="form-label">Previously Submitted Requirements (For Reference)</label>
                        <ul>
                            <li>Purchase Request</li>
                            <li>Quotations</li>
                            <li>APP / PPMP</li>
                            <li>SARO</li>
                            <li>Budget Breakdown</li>
                            <li>Distribution List</li>
                            <li>POI / Activity Design</li>
                            <li>Market Research</li>
                        </ul>
                        <small class="text-muted">These were already submitted in previous steps.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBtn3">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="requirementsModal4" tabindex="-1" aria-labelledby="modalTitle4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle4">REQUIREMENTS DETAILS</h5>
            </div>
            <div class="modal-body">
                <form id="requirementsForm4" enctype="multipart/form-data">
                    <!-- Attendance Upload -->
                    <div class="mb-3">
                        <label for="attendanceFile" class="form-label">Attendance Sheet</label>
                        <input class="form-control" type="file" id="attendanceFile" name="attendanceFile">
                    </div>

                    <!-- Certificate of Completion Upload -->
                    <div class="mb-3">
                        <label for="cocFile" class="form-label">Certificate of Completion/Satisfaction for Supplier</label>
                        <input class="form-control" type="file" id="cocFile" name="cocFile">
                    </div>

                    <!-- Photo Upload -->
                    <div class="mb-3">
                        <label for="photoFile" class="form-label">Photo</label>
                        <input class="form-control" type="file" id="photoFile" name="photoFile">
                    </div>

                    <!-- SOA/Billing Statement Upload -->
                    <div class="mb-3">
                        <label for="soaFile" class="form-label">SOA / Billing Statement</label>
                        <input class="form-control" type="file" id="soaFile" name="soaFile">
                    </div>

                    <!-- Delivery Receipt Upload -->
                    <div class="mb-3">
                        <label for="drFile" class="form-label">Delivery Receipt</label>
                        <input class="form-control" type="file" id="drFile" name="drFile">
                    </div>

                    <!-- Distribution List Upload -->
                    <div class="mb-3">
                        <label for="dlFile" class="form-label">Distribution List (Receiving Copy)</label>
                        <input class="form-control" type="file" id="dlFile" name="dlFile">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBtn4">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="requirementsModal5" tabindex="-1" aria-labelledby="modalTitle5" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle5">REQUIREMENTS DETAILS</h5>
            </div>
            <div class="modal-body">
                <form id="requirementsForm5" enctype="multipart/form-data">

                    <!-- Section 1: Upload Combined Document (ORS, PO, Abstract, Philgeps Posting, IAR, ICS/PAR, Request for Inspection) -->
                    <div class="mb-3">
                        <label for="budgetAllFile5" class="form-label">Upload Combined Document (ORS, PO, Abstract, Philgeps Posting, IAR, ICS/PAR, Request for Inspection)</label>
                        <input class="form-control" type="file" id="budgetAllFile5" name="budgetAllFile5[]" multiple>
                    </div>

                    <!-- Display Existing Files (Optional if you want to load previously uploaded files here) -->
                    <div class="mb-3">
                        <label class="form-label">Previously Uploaded Documents (ORS, PO, etc.)</label>
                        <div id="budgetAllFilePreview5">
                            <p class="text-muted">Loading files...</p>
                        </div>
                    </div>

                    <!-- Section 2: Upload Combined Document (Attendance, Certificate, Photos, SOA, DR, Distribution List) -->
                    <div class="mb-3">
                        <label for="supplyAllFile5" class="form-label">Upload Combined Document (Attendance, Certificate, Photos, SOA, DR, Distribution List)</label>
                        <input class="form-control" type="file" id="supplyAllFile5" name="supplyAllFile5[]" multiple>
                    </div>

                    <!-- Display Existing Files (Optional) -->
                    <div class="mb-3">
                        <label class="form-label">Previously Uploaded Documents (Attendance, Certificate, etc.)</label>
                        <div id="supplyAllFilePreview5">
                            <p class="text-muted">Loading files...</p>
                        </div>
                    </div>

                    <!-- Section 3: Upload Combined Document (PR, Quotations, APP/PPMP, SARO, Budget Breakdown, etc.) -->
                    <div class="mb-3">
                        <label for="allReqsFile5" class="form-label">Upload Combined Document (PR, Quotations, APP/PPMP, SARO, Budget Breakdown, Distribution List, POI, Market Research)</label>
                        <input class="form-control" type="file" id="allReqsFile5" name="allReqsFile5[]" multiple>
                    </div>

                    <!-- Display Existing Files (Optional) -->
                    <div class="mb-3">
                        <label class="form-label">Previously Uploaded Documents (PR, Quotations, etc.)</label>
                        <div id="allReqsFilePreview5">
                            <p class="text-muted">Loading files...</p>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBtn5">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="requirementsModal6" tabindex="-1" aria-labelledby="modalTitle6" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle6">REQUIREMENTS DETAILS</h5>
            </div>
            <div class="modal-body">
                <form id="requirementsForm6" enctype="multipart/form-data">
                    
                    <!-- Upload DV (Disbursement Voucher) -->
                    <div class="mb-3">
                        <label for="dvFile" class="form-label">Upload DV (Disbursement Voucher)</label>
                        <input class="form-control" type="file" id="dvFile" name="dvFile">
                    </div>

                    <!-- Display Existing Files (These are from previous steps, just display, no re-upload) -->

                    <!-- Section: ORS, PO, Abstract, etc. -->
                    <div class="mb-3">
                        <label class="form-label">Uploaded Documents (ORS, PO, Abstract, Philgeps Posting, IAR, ICS/PAR, Request for Inspection)</label>
                        <div id="budgetAllFilePreview6">
                            <p class="text-muted">Loading files...</p>
                        </div>
                    </div>

                    <!-- Section: Attendance, Certificate, Photos, etc. -->
                    <div class="mb-3">
                        <label class="form-label">Uploaded Documents (Attendance, Certificate, Photos, SOA, DR, Distribution List)</label>
                        <div id="supplyAllFilePreview6">
                            <p class="text-muted">Loading files...</p>
                        </div>
                    </div>

                    <!-- Section: Purchase Request, Quotations, etc. -->
                    <div class="mb-3">
                        <label class="form-label">Uploaded Documents (PR, Quotations, APP/PPMP, SARO, Budget Breakdown, etc.)</label>
                        <div id="allReqsFilePreview6">
                            <p class="text-muted">Loading files...</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBtn6">Save</button>
            </div>
        </div>
    </div>
</div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/menu.js"></script>
    <script src="/js/procurementformIndicator.js"></script>
    <script src="/js/addProcurementForm.js"></script>

    <script>
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

    </script>
</body>
</html>