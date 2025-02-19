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
                <tr>
                    <td>
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#requirementsModal1">
                            View Details
                        </button>
                    </td>
                    <td><input type="datetime-local" class="form-control" id="dateSubmitted1"></td>
                    <td><input type="datetime-local" class="form-control" id="dateReturned1"></td>
                    <td><span class="indicator" id="indicator1"></span></td>
                </tr>
            </tbody>
        </table>
        <div class="table-buttons">
            <button type="button" class="btn btn-danger" id="cancelChanges">Cancel</button>
            <button type="button" class="btn btn-success" id="saveChanges">Save</button>
        </div>
    </div>

    <!-- Add Procurement Form -->
    <div class="container mt-5">
        <h2>Add Procurement</h2>
        <form action="{{ route('addProcurement') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="category" class="form-label">Select Procurement Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="SVP">SVP</option>
                    <option value="Honoraria">Honoraria</option>
                    <option value="Other expense">Other Expenses</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="pr-number" class="form-label">PR/TRANSACTION NUMBER</label>
                <input type="text" class="form-control" id="pr-number" name="pr_number" placeholder="Enter PR Number" required>
            </div>
            <div class="mb-3">
                <label for="activity" class="form-label">ACTIVITY</label>
                <input type="text" class="form-control" id="activity" name="activity" placeholder="Enter Activity" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">DESCRIPTION</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter Description" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Add Procurement</button>
        </form>
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
                        @foreach ($checklistItems as $item)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="{{ $item->id }}">
                            <label class="form-check-label" for="{{ $item->id }}">{{ $item->requirement_name }}</label>
                        </div>
                        @endforeach
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveBtn1">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>