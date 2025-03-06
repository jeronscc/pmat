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

    <div class="container my-5">
            <div class="row justify-content-center">
            <div class="container-fluid my-0"> 
                    <div class="card shadow-lg p-4">
        <div class="container mt-0">
            <div class="activity-info">
                <h3>PR Number: <span id="prtrNumber">{{ $prNumber }}</span></h3>
                <h3>Activity Name: <span id="activityName">{{ $activity }}</span></h3>
            </div>
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
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal">
                            View Details
                        </button>
                    </td>
                    <td><input type="datetime-local" class="form-control" id="dateSubmitted"></td>
                    <td><input type="datetime-local" class="form-control" id="dateReturned"></td>
                    <td><span class="indicator" id="indicator"></span></td>
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
                                    <input type="number" class="form-control" id="budgetSpent" name="budget_spent">
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
        </div>

        <!-- Modals -->
        <div class="modal fade" id="requirementsModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalTitle">REQUIREMENTS DETAILS</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            <!-- Checklist for Button  -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ors">
                                <label class="form-check-label" for="ors">ORS</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dv">
                                <label class="form-check-label" for="dv">DV</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="contract">
                                <label class="form-check-label" for="contract">Service Contract</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="classification">
                                <label class="form-check-label" for="classification">Certificate Honoraria Classification</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="report">
                                <label class="form-check-label" for="report">Terminal Report</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="attendance">
                                <label class="form-check-label" for="attendance">Attendance</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="resume">
                                <label class="form-check-label" for="resume">Resume/CV</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="govid">
                                <label class="form-check-label" for="govid">Government ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="payslip">
                                <label class="form-check-label" for="payslip">Payslip/Certificate of Gross Income</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="bank">
                                <label class="form-check-label" for="bank">TIN and Bank Account details</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="cert">
                                <label class="form-check-label" for="cert">Certificate of Services Rendered</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="saveBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
                </div>
            </div>
        </div>

    
    <script src="/js/menu.js"></script>
</body>
</html>