<?php
session_start();
include 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        $stmt = $conn_user->prepare("SELECT user_id, designation, password FROM user_accs WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Direct string comparison (without hashing)
                if ($password === $row['password']) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['designation'] = $row['designation'];
                    header("Location: /pmat/pages/try_redirect.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Invalid username or password.";
                }
            } else {
                $_SESSION['error_message'] = "Invalid username or password.";
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Database error. Please try again later.";
        }
    } else {
        $_SESSION['error_message'] = "Please fill in all fields.";
    }
    header("Location: login.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: login.php");
    exit();
}

$conn_user->close();
?>
