<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/mainheader.css">
    <link rel="stylesheet" href="/css/sidenav.css">
</head>
<style>
#addUserModal {
    z-index: 9999 !important; /* Ensures it's above other elements */
}
#editUserModal {
    z-index: 9999 !important;
}

    </style>
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
            <script src="/js/menu.js"></script>

            <div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">User Accounts</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm edit-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editUserModal"
                                    data-userid="{{ $user->user_id }}"
                                    data-username="{{ $user->username }}"
                                    data-email="{{ $user->email }}"
                                    data-role="{{ $user->role }}">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <button class="btn btn-danger btn-sm delete-btn" data-userid="{{ $user->user_id }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editUserModal.show();
        });
    </script>
@endif

<!-- Edit Account Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editUserModalLabel">Edit Account: <span id="currentUsername"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form action="{{ route('accounts.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editUserId" name="user_id">

                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                               id="editUsername" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="editEmail" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="editRole" name="role" required>
                            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="User" {{ old('role') == 'User' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>





    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" action="{{ route('accounts.add') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" selected disabled>Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid role.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        <div id="passwordError" class="text-danger small mt-1" style="display: none;">
                            Password must be at least 6 characters.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("addUserForm").addEventListener("submit", function (event) {
        let passwordField = document.getElementById("password");
        let passwordError = document.getElementById("passwordError");

        if (passwordField.value.length < 6) {
            event.preventDefault(); // Prevent form submission
            passwordError.style.display = "block"; // Show error message
            passwordField.classList.add("is-invalid"); // Highlight the input field
        } else {
            passwordError.style.display = "none"; // Hide error if valid
            passwordField.classList.remove("is-invalid"); // Remove red highlight
        }
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            let userId = this.getAttribute("data-userid");
            let username = this.getAttribute("data-username");
            let email = this.getAttribute("data-email");
            let role = this.getAttribute("data-role");

            // Set values in the modal fields
            document.getElementById("editUserId").value = userId;
            document.getElementById("editUsername").value = username;
            document.getElementById("editEmail").value = email;
            document.getElementById("editRole").value = role;

            // Update modal header with the current username
            document.getElementById("currentUsername").textContent = username;

            // Set form action dynamically
            document.getElementById("editUserForm").setAttribute("action", `/accounts/${userId}/update`);
        });
    });
    // Clear error messages when modal is closed
    let editUserModal = document.getElementById("editUserModal");
    editUserModal.addEventListener("hidden.bs.modal", function () {
        // Remove validation error messages
        document.querySelectorAll(".invalid-feedback").forEach(el => el.innerHTML = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        // Optional: Clear the form fields if needed
        document.getElementById("editUserForm").reset();
    });
});
</script>
<script>
    document.getElementById("saveChangesBtn").addEventListener("click", function() {
    let userId = document.getElementById("editUserId").value;
    let form = document.getElementById("editUserForm");
    
    if (userId) {
        form.submit();
    } else {
        alert("User ID is missing.");
    }
});

    </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
