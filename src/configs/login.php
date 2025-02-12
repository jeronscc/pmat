<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Tracking and Monitoring System</title>
    <link rel="stylesheet" href="../css/loginpage.css">
    <link rel="stylesheet" href="../css/landingheader.css">
    <?php include '../components/landing_header.html'; ?>
</head>
<body>
    <div class="procurement-app">
        <header class="procurement-header">
            
        </header>
        <main class="procurement-content">
            <div class="login-container">
                <div class="login-header">
                    <img src="https://cdn.builder.io/api/v1/image/assets/290409bedf4e4a8c97971a85d2d24dfd/c0220c2d998879472d0894346184afda81aa699acbd17a8a23e6637af1eb6719?apiKey=290409bedf4e4a8c97971a85d2d24dfd&" alt="Login Logo" class="login-logo">
                    <button class="close-button" aria-label="Close">
                        <img src="https://cdn.builder.io/api/v1/image/assets/290409bedf4e4a8c97971a85d2d24dfd/00abdd59772332e39af7af08d008f16f15db6cd08c7b1db3a2f5b032e0b5d5a0?apiKey=290409bedf4e4a8c97971a85d2d24dfd&" alt="" class="close-icon">
                    </button>
                </div>
                <form class="login-form">
                    <div class="form-group">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" class="form-input" required placeholder="Enter your username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <div class="password-container">
                            <input type="password" id="password" class="form-input" required placeholder="Enter your password">
                            <img src="../assets/open_eye.png" alt="Show Password" class="eye-icon" id="toggle-password">
                        </div>
                    </div>
                    <button type="submit" class="login-submit">Login</button>
                </form>
            </div>
        </main>
    </div>
</body>
<script>
    document.getElementById("toggle-password").addEventListener("click", function() {
        const passwordField = document.getElementById("password");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            this.src = "../assets/open_eye.png";
        } else {
            passwordField.type = "password";
            this.src = "../assets/close_eye.png";
        }
    });
</script>
</html>