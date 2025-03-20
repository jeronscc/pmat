<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/mainheader.css">
    <link rel="stylesheet" href="/css/sidenav.css">
</head>
<style>
#addUserModal {
    z-index: 9999 !important; /* Ensures it's above other elements */
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
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                </table>
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
                    <form>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Username</label>
                            <input type="text" class="form-control" id="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role">
                                <option value="Admin">Admin</option>
                                <option value="User">User</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
