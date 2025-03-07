<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/landingheader.css">
    <link rel="stylesheet" href="/css/selection.css">
</head>
<body>
    <header class="d-flex align-items-center justify-content-between bg-black text-white p-3 shadow">
        <div class="logo">
            <img src="/assets/dict-logo.png" alt="DICT Logo" class="img-fluid" id="dictLogo">
        </div>
        <h1 class="text-center flex-grow-1 fs-4 m-0">Procurement Tracking and Monitoring System</h1> 
        
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn custom-btn">Log Out</button>
        </form>
    </header>
    
    <div class="container mt-4">
    <div class="row">
        <div class="col-md-6 p-3">
            <div class="box">
                <img src="/assets/ilcdb-logo-2.png" alt="ILCDB" class="img-fluid">
            </div>
        </div>
        <div class="col-md-6 p-3">
            <div class="box">
                <img src="/assets/dtc-logo.png" alt="DTC" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 p-3">
            <div class="box">
                <img src="/assets/spark-logo.png" alt="SPARK" class="img-fluid">
            </div>
        </div>
        <div class="col-md-6 p-3">
            <div class="box">
                <img src="/assets/click-logo.png" alt="PROJECT CLICK" class="img-fluid">
            </div>
        </div>
    </div>
</div>

</body>
</html>
