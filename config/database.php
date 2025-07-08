<?php
$host = 'localhost';
$dbname = 'duty';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20) NOT NULL,
        designation VARCHAR(50) NOT NULL,
        department VARCHAR(50) NOT NULL,
        working_date DATE NOT NULL,
        shift_time VARCHAR(50) NOT NULL,
        bus_number VARCHAR(20) DEFAULT 'N/A',
        route VARCHAR(100) DEFAULT 'N/A',
        co_partner VARCHAR(100) DEFAULT 'N/A',
        co_partner_desg VARCHAR(50) DEFAULT 'N/A',
        status ENUM('Active', 'Inactive', 'On Leave') DEFAULT 'Active',
        batch_no VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        schedule_date DATE NOT NULL,
        shift_time VARCHAR(50) NOT NULL,
        bus_number VARCHAR(20),
        route VARCHAR(100),
        duty_type VARCHAR(50) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $table) {
    $pdo->exec($table);
}

// Create default admin if not exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE email = ?");
$stmt->execute(['admin@transport.com']);
if ($stmt->fetchColumn() == 0) {
    $stmt = $pdo->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['Admin', 'admin@transport.com', password_hash('admin123', PASSWORD_DEFAULT)]);
}
?>