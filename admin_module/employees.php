<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$success = '';
$error = '';

// Handle employee deletion
if (isset($_GET['delete'])) {
    $employee_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
    if ($stmt->execute([$employee_id])) {
        $success = 'Employee deleted successfully!';
    } else {
        $error = 'Error deleting employee.';
    }
}

// Handle employee creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_employee'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $designation = trim($_POST['designation']);
    $department = trim($_POST['department']);
    $working_date = $_POST['working_date'];
    $shift_time = trim($_POST['shift_time']);
    $bus_number = trim($_POST['bus_number']) ?: 'N/A';
    $route = trim($_POST['route']) ?: 'N/A';
    $co_partner = trim($_POST['co_partner']) ?: 'N/A';
    $co_partner_desg = trim($_POST['co_partner_desg']) ?: 'N/A';
    $status = $_POST['status'];
    $batch_no = trim($_POST['batch_no']);
    
    if (empty($name) || empty($email) || empty($phone) || empty($designation) || empty($department) || empty($working_date) || empty($shift_time) || empty($batch_no)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO employees (name, email, phone, designation, department, working_date, shift_time, bus_number, route, co_partner, co_partner_desg, status, batch_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $phone, $designation, $department, $working_date, $shift_time, $bus_number, $route, $co_partner, $co_partner_desg, $status, $batch_no])) {
            $success = 'Employee created successfully!';
        } else {
            $error = 'Error creating employee. Email or batch number might already exist.';
        }
    }
}

// Get all employees
$stmt = $pdo->query("SELECT * FROM employees ORDER BY created_at DESC");
$employees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">
                    <i class="fas fa-bus"></i>
                    Transport Manager - Admin
                </a>
                <div class="nav-links">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        Users
                    </a>
                    <a href="employees.php" class="nav-link admin">
                        <i class="fas fa-user-tie"></i>
                        Employees
                    </a>
                    <a href="schedules.php" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        Schedules
                    </a>
                    <a href="logout.php" class="nav-link" style="background: var(--danger-color);">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container dashboard">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Manage Employees</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Add Employee Form -->
        <div class="table-container" style="margin-bottom: 2rem;">
            <div class="table-header">
                <h2 class="table-title">Add New Employee</h2>
            </div>
            <div style="padding: 2rem;">
                <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone *</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="designation">Designation *</label>
                        <input type="text" id="designation" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department *</label>
                        <input type="text" id="department" name="department" required>
                    </div>
                    <div class="form-group">
                        <label for="working_date">Working Date *</label>
                        <input type="date" id="working_date" name="working_date" required>
                    </div>
                    <div class="form-group">
                        <label for="shift_time">Shift Time *</label>
                        <input type="text" id="shift_time" name="shift_time" required>
                    </div>
                    <div class="form-group">
                        <label for="batch_no">Batch No *</label>
                        <input type="text" id="batch_no" name="batch_no" required>
                    </div>
                    <div class="form-group">
                        <label for="bus_number">Bus Number</label>
                        <input type="text" id="bus_number" name="bus_number">
                    </div>
                    <div class="form-group">
                        <label for="route">Route</label>
                        <input type="text" id="route" name="route">
                    </div>
                    <div class="form-group">
                        <label for="co_partner">Co-Partner</label>
                        <input type="text" id="co_partner" name="co_partner">
                    </div>
                    <div class="form-group">
                        <label for="co_partner_desg">Co-Partner Designation</label>
                        <input type="text" id="co_partner_desg" name="co_partner_desg">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <button type="submit" name="create_employee" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Add Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employees List -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">All Employees</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary);">No employees found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['id']; ?></td>
                                <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                                <td><?php echo htmlspecialchars($employee['designation']); ?></td>
                                <td><?php echo htmlspecialchars($employee['department']); ?></td>
                                <td>
                                    <span style="color: <?php echo $employee['status'] === 'Active' ? 'var(--success-color)' : 'var(--danger-color)'; ?>;">
                                        <?php echo htmlspecialchars($employee['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?delete=<?php echo $employee['id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this employee?')">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>