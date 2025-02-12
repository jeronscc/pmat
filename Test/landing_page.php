<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <link rel="stylesheet" href="landingpage.css">
    <?php include '../src/components/landing_header.html'; ?>
</head>
<body>
    
    <main>
        <aside>
            <div class="balance-box">
                <span>Remaining Balance:</span>
                <p>₱1,000,000</p> <!-- Replace with dynamic value as needed -->
            </div>
            <button class="accordion">ILCDB</button>
            <div class="panel">
            
            </div>

            <button class="accordion">DTC</button>
            <div class="panel">
                
            </div>

            <button class="accordion">SPARK</button>
            <div class="panel">
                
            </div>

            <button class="accordion">PROJECT CLICK</button>
            <div class="panel">
                
            </div>
        </aside>
        <section class="records">
        <div class="header">
            <span>Currently Viewing:</span>
            <div class="search">
                <div class="search-box">
                    <input type="text" placeholder="Search">
                    <button>Search</button>
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
                    <tbody>
                        <tr>
                            <td data-label="PR NUMBER">12345</td>
                            <td data-label="ACTIVITY">Sample Activity</td>
                            <td data-label="STATUS">Done</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    </main>
    <script src="landingpage.js"></script>
</body>
</html>