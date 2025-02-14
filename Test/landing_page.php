<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
        <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="landingpage.css">
    <link rel="stylesheet" href="../src/css/landingheader.css">
</head>
<body>
    
    <header class="d-flex align-items-center justify-content-between bg-black text-white p-3 shadow">
        <div class="logo">
            <img src="../src/assets/dict-logo.png" alt="DICT Logo" class="img-fluid" id="dictLogo">
        </div>
        <h1 class="text-center flex-grow-1 fs-4 m-0">Procurement Tracking and Monitoring System</h1> 
        
        <a href="../src/configs/login.php">
            <button class="btn custom-btn">Log In</button>
        </a>
    </header>

    <main>
        <aside>
            <div class="balance-box">
                <span>Remaining Balance:</span>
                <p>₱1,000,000</p> <!-- Replace with dynamic value as needed -->
            </div>
            <button class="accordion">ILCDB <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel">
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
            </div>

            <button class="accordion">DTC <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel">
            <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
            </div>

            <button class="accordion">SPARK <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel">
            <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
            </div>

            <button class="accordion">PROJECT CLICK <span class="dropdown-icon">&#x25BC;</span></button>
            <div class="panel">
            <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
                 <p>SARO 1</p>
            </div>
        </aside>
        <section class="records">
        <div class="header">
            <h5><span>Currently Viewing:</span></h5>
            <div class="search">
                <div class="search-box">
                <input type="text" class="form-control me-2" id="searchBar" placeholder="Search...">
                    <button type="button" class="icon-button" onclick="searchProcurement()">
                    <img src="../src/assets/scan.png" alt="Search" style="width: 30px; height: 30px;">
                </button>
                </div>
            </div>
        </div>
        <!-- Scrollable table -->
        <div class="table-container">
            <div class="record-box">
                <table>
            <thead>
                <tr>
                    <th>PR NUMBER</th>
                    <th>ACTIVITY</th>
                    <th>STATUS</th>
                </tr>
            </thead>      
            <tbody id="procurementTable"> 
                <tr>
                    <td>02-05647</td>
                    <td>ILCDB Orientation</td>
                    <td><span class="badge bg-warning text-dark">at Supply Unit</span></td>
                </tr>
                <tr>
                    <td>02-36421</td>
                    <td>Cybersecurity Workshop</td>
                    <td><span class="badge bg-success">Done</span></td>
                </tr>
                <tr>
                    <td>02-75482</td>
                    <td>Training Camp 2025</td>
                    <td><span class="badge bg-warning text-dark">at Budget Unit</span></td> 
                </tr>  
            </tbody> 
        </table> 
            </div>
        </div>
    </section>
    </main>
    <script src="landingpage.js"></script>
</body>
</html>
