<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $working_date = trim($_POST['working_date'] ?? '');
    $shift_time = trim($_POST['shift_time'] ?? '');
    $bus_number = trim($_POST['bus_number'] ?? 'N/A');
    $route = trim($_POST['route'] ?? 'N/A');
    $co_partner = trim($_POST['co_partner'] ?? 'N/A');
    $co_partner_desg = trim($_POST['co_partner_desg'] ?? 'N/A');
    $status = trim($_POST['status'] ?? 'Active');
    $batch_no = trim($_POST['batch_no'] ?? '');

    // Basic validation
    if (!$name || !$email || !$phone || !$designation || !$department || !$working_date || !$shift_time || !$batch_no) {
        $error_message = 'Please fill in all required fields.';
    } else {
        $query = "INSERT INTO employee (name, email, phone, designation, department, working_date, shift_time, bus_number, route, co_partner, co_partner_desg, status, batch_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssssssss", $name, $email, $phone, $designation, $department, $working_date, $shift_time, $bus_number, $route, $co_partner, $co_partner_desg, $status, $batch_no);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Employee added successfully!';
        } else {
            $error_message = 'Error adding employee. Please check for duplicate email, phone, or batch number.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_employees.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Add Employee</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="dashboard-card">
                <form method="POST" class="form-container">
                    <div class="form-group"><label>Name*</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Email*</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Phone*</label><input type="text" name="phone" required></div>
                    <div class="form-group"><label>Designation*</label><input type="text" name="designation" required></div>
                    <div class="form-group"><label>Department*</label><input type="text" name="department" required></div>
                    <div class="form-group"><label>Working Date*</label><input type="date" name="working_date" required></div>
                    <div class="form-group"><label>Shift Time*</label><input type="text" name="shift_time" required></div>
                    <div class="form-group"><label>Bus Number</label><input type="text" name="bus_number"></div>
                    <div class="form-group"><label>Route</label><input type="text" name="route"></div>
                    <div class="form-group"><label>Co-Partner</label><input type="text" name="co_partner"></div>
                    <div class="form-group"><label>Co-Partner Designation</label><input type="text" name="co_partner_desg"></div>
                    <div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Batch No*</label><input type="text" name="batch_no" required></div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Employee</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html> 