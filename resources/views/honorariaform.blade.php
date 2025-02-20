<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div class="container mt-5">
        <div class="activity-info">
            <h3>Project: <span id="project">Sample Activity</span></h3>
            <h3>Activity Name: <span id="activityName">Sample Activity</span></h3>
            <h3>PR/Transaction Number: <span id="prtrNumber">12345</span></h3>
        </div>
        <h2>Honoraria for Speakers Requirements</h2>
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
                @foreach ($checklistItems as $item)
                <tr>
                    <td>
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal{{ $item->id }}">
                            View Details
                        </button>
                    </td>
                    <td><input type="datetime-local" class="form-control" id="dateSubmitted{{ $item->id }}"></td>
                    <td><input type="datetime-local" class="form-control" id="dateReturned{{ $item->id }}"></td>
                    <td><span class="indicator" id="indicator{{ $item->id }}"></span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-buttons">
            <button type="button" class="btn btn-danger" id="cancelChanges">Cancel</button>
            <button type="button" class="btn btn-success" id="saveChanges">Save</button>
        </div>
    </div>

    <!-- Modals -->
    @foreach ($checklistItems as $item)
    <div class="modal fade" id="requirementsModal{{ $item->id }}" tabindex="-1" aria-labelledby="modalTitle{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle{{ $item->id }}">REQUIREMENTS DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Checklist for Button {{ $item->id }} -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ors{{ $item->id }}">
                            <label class="form-check-label" for="ors{{ $item->id }}">ORS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dv{{ $item->id }}">
                            <label class="form-check-label" for="dv{{ $item->id }}">DV</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="contract{{ $item->id }}">
                            <label class="form-check-label" for="contract{{ $item->id }}">Service Contract</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="classification{{ $item->id }}">
                            <label class="form-check-label" for="classification{{ $item->id }}">Certificate Honoraria Classification</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="report{{ $item->id }}">
                            <label class="form-check-label" for="report{{ $item->id }}">Terminal Report</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="attendance{{ $item->id }}">
                            <label class="form-check-label" for="attendance{{ $item->id }}">Attendance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="resume{{ $item->id }}">
                            <label class="form-check-label" for="resume{{ $item->id }}">Resume/CV</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="govid{{ $item->id }}">
                            <label class="form-check-label" for="govid{{ $item->id }}">Government ID</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="payslip{{ $item->id }}">
                            <label class="form-check-label" for="payslip{{ $item->id }}">Payslip/Certificate of Gross Income</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bank{{ $item->id }}">
                            <label class="form-check-label" for="bank{{ $item->id }}">TIN and Bank Account details</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cert{{ $item->id }}">
                            <label class="form-check-label" for="cert{{ $item->id }}">Certificate of Services Rendered</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveBtn{{ $item->id }}">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="script.js"></script>
</body>
</html>