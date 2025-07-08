<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM employees");
$total_employees = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM schedules");
$total_schedules = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
$total_contacts = $stmt->fetchColumn();

// Get recent employees
$stmt = $pdo->query("SELECT * FROM employees ORDER BY created_at DESC LIMIT 5");
$recent_employees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Transport Management</title>
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
                    <a href="dashboard.php" class="nav-link admin">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        Users
                    </a>
                    <a href="employees.php" class="nav-link">
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
            <h1 class="dashboard-title">Admin Dashboard</h1>
            <p style="color: var(--text-secondary);">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_employees; ?></div>
                <div class="stat-label">Total Employees</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_schedules; ?></div>
                <div class="stat-label">Total Schedules</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_contacts; ?></div>
                <div class="stat-label">Contact Messages</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">Recent Employees</h2>
                <a href="employees.php" class="btn btn-primary">Manage All</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_employees)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary);">No employees found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_employees as $employee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                <td><?php echo htmlspecialchars($employee['designation']); ?></td>
                                <td><?php echo htmlspecialchars($employee['department']); ?></td>
                                <td>
                                    <span style="color: <?php echo $employee['status'] === 'Active' ? 'var(--success-color)' : 'var(--danger-color)'; ?>;">
                                        <?php echo htmlspecialchars($employee['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($employee['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>