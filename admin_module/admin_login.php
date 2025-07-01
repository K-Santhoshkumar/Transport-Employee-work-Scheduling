<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body style="background-color:#ffdfb7;">
<?php
include('database_connect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Please fill in all fields.</h1>";
        exit();
    }
    
    // Prepare a secure SQL query
    $sql = "SELECT id, email, password FROM ADMIN WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_email'] = $row['email'];       
                header("Location: admin_welcome.php"); // Redirect to admin panel
                exit();
            } else {
                echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Invalid email or password.</h1>";
            }
        } else {
            echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Invalid email or password.</h1>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<h1 style='color: red; text-align: center;'>Database error: Unable to prepare statement.</h1>";
    }
}
mysqli_close($conn);
?>
</body>
</html>