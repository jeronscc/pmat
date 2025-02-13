<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <link rel="stylesheet" href="landingpage.css">
    <?php include '../src/components/header_landing.html'; ?>
</head>
<body>
    
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
