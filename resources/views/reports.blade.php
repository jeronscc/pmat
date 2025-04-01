<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Procurement Tracking System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="/css/homepage.css">
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
            <div class="container-fluid mt-3">
                <div class="row">
            

        <!-- BODY CONTENT -->

        <!-- Bootstrap JS (Optional, only needed for dropdowns, modals, etc.) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>