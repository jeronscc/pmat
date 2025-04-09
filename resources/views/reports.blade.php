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
    .text-navy {
    color:rgb(7, 85, 163); /* Navy blue color */
}

</style>

<body>
    <input type="hidden" id="loggedInUserId" value="{{ Auth::id() }}">

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
                @if (Auth::user()->role === 'Admin')
                <!-- Admins can access the Accounts page -->
                <form action="{{ route('accounts') }}" method="get">
                    <button type="submit">
                        <i class="fas fa-users"></i><img src="/assets/account_icon.png" alt=""> Accounts
                    </button>
                </form>
                @else
                <!-- Users see a disabled Accounts button with a lock icon -->
                <button class="disabled-menu" disabled>
                    <i class="fas fa-lock"></i><img src="/assets/account_icon.png" alt=""> Accounts <img src="/assets/lock_icon.png" alt="Locked" class="lock-icon">
                </button>
                @endif
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

    <div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12 col-md-10 mx-auto">
            <div class="card shadow border-0 mb-4">
                <!-- Dark header -->
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Reports Panel</h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="projectDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 170px;">
                            ILCDB
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="projectDropdown">
                            <li><a class="dropdown-item" href="#" onclick="selectProject('ILCDB')">ILCDB</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectProject('DTC')">DTC</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectProject('SPARK')">SPARK</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectProject('PROJECT CLICK')">PROJECT CLICK</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Light body content with Charts -->
                <div class="card-body bg-light" id="reportContent">
                    <h6 class="mb-3 text-dark">Report for <strong id="projectName">ILCDB</strong></h6>
                    
                    <!-- Dynamic Report Data Section -->
                    <div class="row g-3" id="reportData"></div>

                    <!-- Static Charts Section (Always visible, won't be overwritten) -->
                     <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-dark">Distribution of Procurement</h6>
                                    <canvas id="procurementChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                        <!-- Distribution of Category (Bar Graph with Project Filter) -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title text-dark mb-0">Distribution of Category</h6>
                                    <!-- Dropdown to filter by project -->
                                    <select id="categoryFilter" class="form-select mb-0" style="width: 200px;">
                                        <option value="ILCDB">ILCDB</option>
                                        <option value="DTC">DTC</option>
                                        <option value="SPARK">SPARK</option>
                                        <option value="PROJECT CLICK">PROJECT CLICK</option>
                                    </select>
                                </div>
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>

                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-dark">Which Quarter Has the Highest Expenditure?</h6>
                                    <!-- Dropdown to filter by project -->
                                    <select id="quarterFilter" class="form-select mb-3" style="width: 200px;">
                                        <option value="ALL">All Projects</option>
                                        <option value="ILCDB">ILCDB</option>
                                        <option value="DTC">DTC</option>
                                        <option value="SPARK">SPARK</option>
                                        <option value="PROJECT CLICK">PROJECT CLICK</option>
                                    </select>
                                    <canvas id="quarterChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title text-dark mb-0">Cost Savings</h6>
                                    <select id="projectFilter" class="form-select mb-0" style="width: 200px;" onchange="updateCostSavingsChart()">
                                        <option value="ILCDB">ILCDB</option>
                                        <option value="DTC">DTC</option>
                                        <option value="SPARK">SPARK</option>
                                        <option value="PROJECT CLICK">PROJECT CLICK</option>
                                    </select>
                                </div>
                                <canvas id="costSavingsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    </div>
                </div> <!-- End of Card Body -->
            </div>
        </div>
    </div>
</div>

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">© 2024 Procurement Monitoring and Tracking System. All rights reserved.</p>
    </footer>

<!-- Bootstrap JS (Optional, only needed for dropdowns, modals, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/js/menu.js"></script>
<script src="/js/charts.js"></script>

</body>

</html>