<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User signup</title>
</head>
<body style="background-color:#ffdfb7;">  
<?php
include '../admin_module/database_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    // Validate required fields
    if (empty($name) || empty($email) || empty($password)) {
    echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Please fill in all fields.</h1>";
    exit();
}
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Invalid email format.</h1>";
        exit();
    }
    // Validate password length (at least 8 characters)
    if (strlen($password) < 8) {
        echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Password must be atleast 8 characters</h1>";
        exit();
    }
    // Check if username or email already exists
    $check_sql = "SELECT id FROM USER WHERE name = ? OR email = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "ss", $name, $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color: #a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Username or email already exists</h1>";
            mysqli_stmt_close($check_stmt);
            exit();
        }
        mysqli_stmt_close($check_stmt);
    }
    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Insert new user
    $sql = "INSERT INTO USER (name, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: user_login.html");
            exit(); 
        } else {
            header("Location: user_error.html");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Unable to prepare SQL statement.";
    }
}
mysqli_close($conn);
?>
</body>
</html>