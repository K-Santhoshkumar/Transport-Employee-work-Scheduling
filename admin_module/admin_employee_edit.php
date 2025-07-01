<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';
$employee = null;
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_employees.php');
    exit;
}
$employee_id = intval($_GET['id']);

// Fetch employee
$query = "SELECT * FROM employee WHERE ID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $employee_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$employee = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$employee) {
    header('Location: admin_employees.php');
    exit;
}

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

    if (!$name || !$email || !$phone || !$designation || !$department || !$working_date || !$shift_time || !$batch_no) {
        $error_message = 'Please fill in all required fields.';
    } else {
        $update_query = "UPDATE employee SET name=?, email=?, phone=?, designation=?, department=?, working_date=?, shift_time=?, bus_number=?, route=?, co_partner=?, co_partner_desg=?, status=?, batch_no=? WHERE ID=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssssssssssssi", $name, $email, $phone, $designation, $department, $working_date, $shift_time, $bus_number, $route, $co_partner, $co_partner_desg, $status, $batch_no, $employee_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Employee updated successfully!';
        } else {
            $error_message = 'Error updating employee. Please check for duplicate email, phone, or batch number.';
        }
        mysqli_stmt_close($stmt);
    }
    // Refresh employee data
    $query = "SELECT * FROM employee WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $employee_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $employee = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .edit-employee-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 2rem;
            justify-content: center;
        }
        .form-section {
            margin-bottom: 2rem;
        }
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
            padding-left: 0.75rem;
        }
        .form-container {
            background: linear-gradient(120deg, #f8fafc 60%, #e0e7ff 100%);
            box-shadow: 0 8px 32px 0 rgba(99,102,241,0.10);
            border-radius: 20px;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .btn-center {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        @media (max-width: 600px) {
            .form-container { padding: 1rem; }
            .edit-employee-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_employees.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Edit Employee</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="dashboard-card">
                <div class="edit-employee-title"><i class="fas fa-user-edit"></i> Edit Employee</div>
                <form method="POST" class="form-container">
                    <div class="form-section">
                        <div class="form-section-title">Personal Information</div>
                        <div class="form-group"><label>Name*</label><input type="text" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required class="form-control"></div>
                        <div class="form-group"><label>Email*</label><input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required class="form-control"></div>
                        <div class="form-group"><label>Phone*</label><input type="text" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>" required class="form-control"></div>
                    </div>
                    <div class="form-section">
                        <div class="form-section-title">Work Information</div>
                        <div class="form-group"><label>Designation*</label><input type="text" name="designation" value="<?php echo htmlspecialchars($employee['designation']); ?>" required class="form-control"></div>
                        <div class="form-group"><label>Department*</label><input type="text" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required class="form-control"></div>
                        <div class="form-group"><label>Working Date*</label><input type="date" name="working_date" value="<?php echo htmlspecialchars($employee['working_date']); ?>" required class="form-control"></div>
                        <div class="form-group"><label>Shift Time*</label><input type="text" name="shift_time" value="<?php echo htmlspecialchars($employee['shift_time']); ?>" required class="form-control"></div>
                        <div class="form-group"><label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Active" <?php if ($employee['status'] === 'Active') echo 'selected'; ?>>Active</option>
                                <option value="Inactive" <?php if ($employee['status'] === 'Inactive') echo 'selected'; ?>>Inactive</option>
                                <option value="On Leave" <?php if ($employee['status'] === 'On Leave') echo 'selected'; ?>>On Leave</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Batch No*</label><input type="text" name="batch_no" value="<?php echo htmlspecialchars($employee['batch_no']); ?>" required class="form-control"></div>
                    </div>
                    <div class="form-section">
                        <div class="form-section-title">Bus Assignment</div>
                        <div class="form-group"><label>Bus Number</label><input type="text" name="bus_number" value="<?php echo htmlspecialchars($employee['bus_number']); ?>" class="form-control"></div>
                        <div class="form-group"><label>Route</label><input type="text" name="route" value="<?php echo htmlspecialchars($employee['route']); ?>" class="form-control"></div>
                        <div class="form-group"><label>Co-Partner</label><input type="text" name="co_partner" value="<?php echo htmlspecialchars($employee['co_partner']); ?>" class="form-control"></div>
                        <div class="form-group"><label>Co-Partner Designation</label><input type="text" name="co_partner_desg" value="<?php echo htmlspecialchars($employee['co_partner_desg']); ?>" class="form-control"></div>
                    </div>
                    <div class="btn-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html> 