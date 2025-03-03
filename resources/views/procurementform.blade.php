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
                                View Details
                            </button>
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateSubmitted1" name="dt_submitted1" value="{{ $record->dt_submitted1 ?? '' }}">
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateReturned1" name="dt_received1" value="{{ $record->dt_received1 ?? '' }}">
                        </td>
                        <td><span class="indicator" id="indicator1"></span></td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal2">
                                View Details
                            </button>
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateSubmitted2" name="dt_submitted2" value="{{ $record->dt_submitted2 ?? '' }}">
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateReturned2" name="dt_received2" value="{{ $record->dt_received2 ?? '' }}">
                        </td>
                        <td><span class="indicator" id="indicator2"></span></td>
                    </tr>
                </tbody>
            </table>

            <h3>Budget Unit</h3>
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
                                View Details
                            </button>
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateSubmitted3" name="dt_submitted3" value="{{ $record->dt_submitted3 ?? '' }}">
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateReturned3" name="dt_received3" value="{{ $record->dt_received3 ?? '' }}">
                        </td>
                        <td><span class="indicator" id="indicator3"></span></td>
                    </tr>
                </tbody>
            </table>

            <h2 class="fw-bold">Post-Procurement Requirements</h2>
            <h3>Supply Unit</h3>
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
                                View Details
                            </button>
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateSubmitted4" name="dt_submitted4" value="{{ $record->dt_submitted4 ?? '' }}">
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateReturned4" name="dt_received4" value="{{ $record->dt_received4 ?? '' }}">
                        </td>
                        <td><span class="indicator" id="indicator4"></span></td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal5">
                                View Details
                            </button>
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateSubmitted5" name="dt_submitted5" value="{{ $record->dt_submitted5 ?? '' }}">
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateReturned5" name="dt_received5" value="{{ $record->dt_received5 ?? '' }}">
                        </td>
                        <td><span class="indicator" id="indicator5"></span></td>
                    </tr>
                </tbody>
            </table>

            <h3>Accounting Unit</h3>
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
                                View Details
                            </button>
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateSubmitted6" name="dt_submitted6" value="{{ $record->dt_submitted6 ?? '' }}">
                        </td>
                        <td>
                            <input type="datetime-local" class="form-control" id="dateReturned6" name="dt_received6" value="{{ $record->dt_received6 ?? '' }}">
                        </td>
                        <td><span class="indicator" id="indicator6"></span></td>
                    </tr>
                </tbody>
            </table>

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
                                value="{{ $record->budget_spent ?? '' }}">
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
                    <h5 class="modal-title" id="modalTitle1">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Checklist for Button 1 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="app1">
                            <label class="form-check-label" for="app1">APP / PPMP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="saro1">
                            <label class="form-check-label" for="saro1">SARO</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="budget1">
                            <label class="form-check-label" for="budget1">Budget Breakdown</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="distribution1">
                            <label class="form-check-label" for="distribution1">Distribution List (for items/goods)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="poi1">
                            <label class="form-check-label" for="poi1">POI / Activity Design</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="research1">
                            <label class="form-check-label" for="research1">Market Research</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="purchase1">
                            <label class="form-check-label" for="purchase1">Purchase Request</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quotations1">
                            <label class="form-check-label" for="quotations1">Quotations</label>
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
                    <form>
                        <!-- Checklist for Button 2 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="po1">
                            <label class="form-check-label" for="po1">Purchase Order</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="abs1">
                            <label class="form-check-label" for="abs1">Abstract/Philgeps posting*</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="other1">
                            <label class="form-check-label" for="other1">Purchase Request, Quotations, APP / PPMP, SARO, Budget Breakdown, Distribution List, POI/Activity Design, Market Research</label>
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
                    <form>
                        <!-- Checklist for Button 3 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ors1">
                            <label class="form-check-label" for="ors1">ORS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="po2">
                            <label class="form-check-label" for="po2">Purchase Order</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="abs2">
                            <label class="form-check-label" for="abs2">Abstract/Philgeps posting*</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="budget3">
                            <label class="form-check-label" for="budget3">Purchase Request, Quotations, APP / PPMP, SARO, Budget Breakdown, Distribution List, POI/Activity Design, Market Research</label>
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
                    <form>
                        <!-- Checklist for Button 3 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="attendance1">
                            <label class="form-check-label" for="attendance1">Attendance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="coc1">
                            <label class="form-check-label" for="coc1">Certificate of Completion/Satisfaction for Supplier</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pic1">
                            <label class="form-check-label" for="pic1">Photo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="soa1">
                            <label class="form-check-label" for="soa1">SOA/ Billing Statement</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dr1">
                            <label class="form-check-label" for="dr1">Delivery Receipt</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dl1">
                            <label class="form-check-label" for="dl1">Distribution List(Receiving Copy)</label>
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
                    <form>
                        <!-- Checklist for Button 3 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="budgetall1">
                            <label class="form-check-label" for="budgetall1">ORS, Purchase Order, Abstract, Philgeps posting*, IAR, ICS/PAR, Request for Inspection(COA)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="supplyall1">
                            <label class="form-check-label" for="supplyall1">Attendance, Certificate of Completion/Satisfaction for Supplier, Photos, SOA/Billing Statement, Delivery Receipt, Distribution List(Receiving Copy)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allreqs1">
                            <label class="form-check-label" for="allreqs1">Purchase Request, Quotations, APP / PPMP, SARO, Budget Breakdown, Distribution List, POI/Activity Design, Market Research</label>
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
                    <form>
                        <!-- Checklist for Button 3 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dv1">
                            <label class="form-check-label" for="dv1">DV</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="budgetall2">
                            <label class="form-check-label" for="budgetall2">ORS, Purchase Order, Abstract, Philgeps posting*, IAR, ICS/PAR, Request for Inspection(COA)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="supplyall2">
                            <label class="form-check-label" for="supplyall2">Attendance, Certificate of Completion/Satisfaction for Supplier, Photos, SOA/Billing Statement, Delivery Receipt, Distribution List(Receiving Copy)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allreqs2">
                            <label class="form-check-label" for="allreqs2">Purchase Request, Quotations, APP / PPMP, SARO, Budget Breakdown, Distribution List, POI/Activity Design, Market Research</label>
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

<script src="/js/addProcurementForm.js"></script>

<script>
$(document).ready(function() {
    $('#saveChanges').on('click', function(e) {
        e.preventDefault();
        var formData = $('#procurementForm').serialize();
        $.ajax({
            url: '{{ route("procurement.update") }}',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                console.error('Error saving data:', xhr.responseText);
                alert('Error saving data. Check console for details.');
            }
        });
    });
});
</script>
</body>
</html>