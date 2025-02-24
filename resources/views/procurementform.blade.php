<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/forms.css">
    <link rel="stylesheet" href="/css/mainheader.css"> 
    <link rel="stylesheet" href="/css/sidenav.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <div class="container mt-5">
        <div class="activity-info">
            <h3>Activity Name: <span id="activityName">Sample Activity</span></h3>
            <h3>PR Number: <span id="prNumber">12345</span></h3>
        </div>
        <h2>Pre-Procurement Requirements</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Requirements</th>
                    <th>Date Submitted</th>
                    <th>Date Returned</th>
                    <th>Indicator</th>
                </tr>
            </thead>
            <tbody id="requirementsTable">
                <!-- Requirements will be populated here -->
            </tbody>
        </table>
        <div class="table-buttons">
            <button type="button" class="btn btn-danger" id="cancelChanges">Cancel</button>
            <button type="button" class="btn btn-success" id="saveChanges">Save</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/fetch-procurement-ilcdb')
                .then(response => response.json())
                .then(data => {
                    const requirementsTable = document.getElementById('requirementsTable');
                    requirementsTable.innerHTML = ''; // Clear any existing rows

                    data.forEach(procurement => {
                        procurement.requirements.forEach((requirement, index) => {
                            const row = document.createElement('tr');

                            const requirementCell = document.createElement('td');
                            requirementCell.textContent = requirement.requirement;
                            row.appendChild(requirementCell);

                            const dateSubmittedCell = document.createElement('td');
                            const dateSubmittedInput = document.createElement('input');
                            dateSubmittedInput.type = 'datetime-local';
                            dateSubmittedInput.classList.add('form-control');
                            dateSubmittedInput.value = requirement.date_submitted;
                            dateSubmittedInput.disabled = index !== 0 && !data[index - 1].requirements.every(req => req.is_checked);
                            dateSubmittedInput.addEventListener('change', function() {
                                updateRequirement(requirement.id, { date_submitted: this.value });
                            });
                            dateSubmittedCell.appendChild(dateSubmittedInput);
                            row.appendChild(dateSubmittedCell);

                            const dateReturnedCell = document.createElement('td');
                            const dateReturnedInput = document.createElement('input');
                            dateReturnedInput.type = 'datetime-local';
                            dateReturnedInput.classList.add('form-control');
                            dateReturnedInput.value = requirement.date_returned;
                            dateReturnedInput.disabled = index !== 0 && !data[index - 1].requirements.every(req => req.is_checked);
                            dateReturnedInput.addEventListener('change', function() {
                                updateRequirement(requirement.id, { date_returned: this.value });
                            });
                            dateReturnedCell.appendChild(dateReturnedInput);
                            row.appendChild(dateReturnedCell);

                            const indicatorCell = document.createElement('td');
                            const indicatorSpan = document.createElement('span');
                            indicatorSpan.classList.add('indicator');
                            if (requirement.is_checked) {
                                indicatorSpan.classList.add('bg-success');
                            }
                            indicatorCell.appendChild(indicatorSpan);
                            row.appendChild(indicatorCell);

                            requirementsTable.appendChild(row);
                        });
                    });
                })
                .catch(error => console.error('Error fetching procurement data:', error));
        });

        function updateRequirement(id, data) {
            fetch(`/api/update-requirement/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Requirement updated successfully');
            })
            .catch(error => console.error('Error updating requirement:', error));
        }
    </script>
</body>
</html>