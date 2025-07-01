<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Connet</title>
</head>
<body style="background-color:#ffdfb7 ;">
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "duty";

    // Establish database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("<h1 style='color: red; text-align: center;'>Error occurred during connection: " . mysqli_connect_error() . "</h1>");
    }

    /* echo "<h1 style='color: #d9851e; font-size: 22px; text-align: center; background-color:#a4193d ; padding: 10px; border: 2px solid red; border-radius: 5px;'>Database connected successfully.</h1>"; */
?>
</body>
</html>