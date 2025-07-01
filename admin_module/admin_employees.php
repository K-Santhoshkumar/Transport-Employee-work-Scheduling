<?php
include('./middleware.php');
middleware();
include('database_connect.php');

// Handle employee actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $employee_id = intval($_POST['employee_id']);
                $delete_query = "DELETE FROM employee WHERE id = ?";
                $stmt = mysqli_prepare($conn, $delete_query);
                mysqli_stmt_bind_param($stmt, "i", $employee_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Employee deleted successfully!";
                } else {
                    $error_message = "Error deleting employee.";
                }
                mysqli_stmt_close($stmt);
                break;
                
            case 'update_status':
                $employee_id = intval($_POST['employee_id']);
                $new_status = $_POST['status'];
                $update_query = "UPDATE employee SET status = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "si", $new_status, $employee_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Employee status updated successfully!";
                } else {
                    $error_message = "Error updating employee status.";
                }
                mysqli_stmt_close($stmt);
                break;
        }
    }
}

// Get all employees
$employees_query = "SELECT * FROM employee ORDER BY id DESC";
$employees_result = mysqli_query($conn, $employees_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-bus"></i> Project 1</h2>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="admin_users.php">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="active">
                    <a href="admin_employees.php">
                        <i class="fas fa-user-tie"></i>
                        <span>Employees</span>
                    </a>
                </li>
                <li>
                    <a href="admin_schedule.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Schedule</span>
                    </a>
                </li>
                <li>
                    <a href="admin_reports.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="admin_settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="admin_logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Employee Management</h1>
                </div>
                <div class="top-bar-right">
                    <div class="admin-profile">
                        <i class="fas fa-user-circle"></i>
                        <span>Welcome, Admin</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="dashboard-content">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>All Employees</h3>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <input type="text" class="form-control search-input" placeholder="Search employees..." style="width: 250px;">
                            <a href="admin_employee_add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Add Employee
                            </a>
                        </div>
                    </div>
                    <div class="card-content" style="padding: 0;">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="sortable">ID</th>
                                        <th class="sortable">Name</th>
                                        <th class="sortable">Email</th>
                                        <th class="sortable">Phone</th>
                                        <th class="sortable">Designation</th>
                                        <th class="sortable">Department</th>
                                        <th class="sortable">Batch No</th>
                                        <th class="sortable">Bus No</th>
                                        <th class="sortable">Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($employees_result) > 0): ?>
                                        <?php while ($employee = mysqli_fetch_assoc($employees_result)): ?>
                                            <tr>
                                                <td><?php echo $employee['ID']; ?></td>
                                                <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['designation']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['department']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['batch_no'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($employee['bus_number'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="employee_id" value="<?php echo $employee['ID']; ?>">
                                                        <select name="status" onchange="this.form.submit()" class="form-control" style="width: auto; padding: 0.25rem 0.5rem;">
                                                            <option value="Active" <?php echo $employee['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="Inactive" <?php echo $employee['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <div style="display: flex; gap: 0.5rem;">
                                                        <a href="admin_employee_edit.php?id=<?php echo $employee['ID']; ?>" class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="employee_id" value="<?php echo $employee['ID']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    data-confirm="Are you sure you want to delete this employee?">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="no-data">No employees found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="admin_modern.js"></script>
</body>
</html>