<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "duty";

// Establish database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
mysqli_query($conn, $create_db);

// Select the database
mysqli_select_db($conn, $dbname);

// Create tables if they don't exist
$create_admin_table = "CREATE TABLE IF NOT EXISTS ADMIN (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_user_table = "CREATE TABLE IF NOT EXISTS USER (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_employee_table = "CREATE TABLE IF NOT EXISTS employee (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    designation VARCHAR(255) NOT NULL,
    department VARCHAR(255) NOT NULL,
    working_date DATE NOT NULL,
    shift_time VARCHAR(100) NOT NULL,
    bus_number VARCHAR(50) DEFAULT 'N/A',
    route VARCHAR(255) DEFAULT 'N/A',
    co_partner VARCHAR(255) DEFAULT 'N/A',
    co_partner_desg VARCHAR(255) DEFAULT 'N/A',
    status ENUM('Active', 'Inactive', 'On Leave') DEFAULT 'Active',
    batch_no VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_schedule_table = "CREATE TABLE IF NOT EXISTS employee_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    schedule_date DATE NOT NULL,
    shift_time VARCHAR(50) NOT NULL,
    bus_number VARCHAR(20),
    route VARCHAR(100),
    duty_type VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employee(ID) ON DELETE CASCADE
)";

$create_newsletter_table = "CREATE TABLE IF NOT EXISTS newsletter_subscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute table creation queries
mysqli_query($conn, $create_admin_table);
mysqli_query($conn, $create_user_table);
mysqli_query($conn, $create_employee_table);
mysqli_query($conn, $create_schedule_table);
mysqli_query($conn, $create_newsletter_table);

// Create default admin user if it doesn't exist
$check_admin = "SELECT id FROM ADMIN WHERE email = 'admin@transport.com'";
$admin_result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($admin_result) == 0) {
    $admin_email = 'admin@transport.com';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO ADMIN (email, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_admin);
    mysqli_stmt_bind_param($stmt, "ss", $admin_email, $admin_password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>