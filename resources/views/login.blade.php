

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/loginpage.css">
    <link rel="stylesheet" href="/css/landingheader.css">
</head>
<style>
    .error-message {
    color: red;
    font-weight: bold;
    text-align: center;
    margin-top: 10px;
    background-color: #ffe6e6;
    padding: 10px;
    border-radius: 5px;
    width: 100%;
}
</style>
<body>

    <!-- Display error pop-up alert if there is an error message in session -->
    @if(session('error'))
        <script>
            window.onload = function() {
                alert("{{ session('error') }}"); // Show the alert with the error message
            }
        </script>
    @endif

    <header class="d-flex align-items-center justify-content-between bg-black text-white p-3 shadow">
        <div class="logo">
            <img src="/assets/dict-logo.png" alt="DICT Logo" class="img-fluid" id="dictLogo">
        </div>
        <h1 class="text-center flex-grow-1 fs-4 m-0">Procurement Tracking and Monitoring System</h1> 
        
        <a href="/login">
            <button class="btn custom-btn">Log In</button>
        </a>
    </header>
    <div class="procurement-app">
        <main class="procurement-content">
            <div class="login-container">
                <div class="login-header">
                    <img src="https://cdn.builder.io/api/v1/image/assets/290409bedf4e4a8c97971a85d2d24dfd/c0220c2d998879472d0894346184afda81aa699acbd17a8a23e6637af1eb6719?apiKey=290409bedf4e4a8c97971a85d2d24dfd&" alt="Login Logo" class="login-logo">
                    <a href="/">
                        <button class="close-button" aria-label="Close">
                            <img src="https://cdn.builder.io/api/v1/image/assets/290409bedf4e4a8c97971a85d2d24dfd/00abdd59772332e39af7af08d008f16f15db6cd08c7b1db3a2f5b032e0b5d5a0?apiKey=290409bedf4e4a8c97971a85d2d24dfd&" alt="" class="close-icon">
                        </button>
                    </a>
                </div>
                <form class="login-form" action="/login" method="POST"> 
                    @csrf
                    <div class="form-group">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" name="username" class="form-input" required placeholder="Enter your username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" class="form-input" required placeholder="Enter your password">
                            <img src="/assets/close_eye.png" alt="Show Password" class="eye-icon" id="toggle-password">
                        </div>
                    </div>
                    <button class="login-submit">Login</button>
                </form>
            </div>
        </main>
    </div>
</body>
@if (isset($error))
<script>
    alert('{{ $error }}');
</script>
@endif
<script>
    document.getElementById("toggle-password").addEventListener("click", function() {
        const passwordField = document.getElementById("password");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            this.src = "/assets/open_eye.png";
        } else {
            passwordField.type = "password";
            this.src = "/assets/close_eye.png";
        }
    });
</script>
</html>
