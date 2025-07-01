<?php
include('./middleware.php');
middleware();
include('database_connect.php');

// Get statistics
$total_users_query = "SELECT COUNT(*) as count FROM USER";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['count'];

$total_employees_query = "SELECT COUNT(*) as count FROM employee";
$total_employees_result = mysqli_query($conn, $total_employees_query);
$total_employees = mysqli_fetch_assoc($total_employees_result)['count'];

$active_employees_query = "SELECT COUNT(*) as count FROM employee WHERE status = 'Active'";
$active_employees_result = mysqli_query($conn, $active_employees_query);
$active_employees = mysqli_fetch_assoc($active_employees_result)['count'];

// Get total schedules
$total_schedules_query = "SELECT COUNT(*) as count FROM employee";
$total_schedules_result = mysqli_query($conn, $total_schedules_query);
$total_schedules = mysqli_fetch_assoc($total_schedules_result)['count'] ?? 0;

$recent_contacts_query = "SELECT * FROM newsletter_subscription ORDER BY id DESC LIMIT 5";
$recent_contacts_result = mysqli_query($conn, $recent_contacts_query);

// Get recent schedules
$recent_schedules_query = "SELECT * FROM employee ORDER BY created_at DESC LIMIT 5";
$recent_schedules_result = mysqli_query($conn, $recent_schedules_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Transport Management</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-bus"></i> Transport Manager</h2>
            </div>
            <ul class="sidebar-menu">
                <li class="active">
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
                <li>
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
                    <h1>Dashboard</h1>
                </div>
                <div class="top-bar-right">
                    <div class="admin-profile">
                        <i class="fas fa-user-circle"></i>
                        <span>Welcome, Admin</span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_employees; ?></h3>
                            <p>Total Employees</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $active_employees; ?></h3>
                            <p>Active Employees</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_schedules; ?></h3>
                            <p>Total Schedules</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-calendar-alt"></i> Recent Schedules</h3>
                            <a href="admin_schedule.php" class="view-all">View All</a>
                        </div>
                        <div class="card-content">
                            <?php if (mysqli_num_rows($recent_schedules_result) > 0): ?>
                                <div class="contact-list">
                                    <?php while ($schedule = mysqli_fetch_assoc($recent_schedules_result)): ?>
                                        <div class="contact-item">
                                            <div class="contact-info">
                                                <h4><?php echo htmlspecialchars($schedule['name']); ?></h4>
                                                <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($schedule['created_at'])); ?></p>
                                                <p><strong>Shift:</strong> <?php echo htmlspecialchars($schedule['shift_time']); ?></p>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-data">No recent schedules</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-envelope"></i> Recent Contact Messages</h3>
                            <a href="admin_contacts.php" class="view-all">View All</a>
                        </div>
                        <div class="card-content">
                            <?php if (mysqli_num_rows($recent_contacts_result) > 0): ?>
                                <div class="contact-list">
                                    <?php while ($contact = mysqli_fetch_assoc($recent_contacts_result)): ?>
                                        <div class="contact-item">
                                            <div class="contact-info">
                                                <h4><?php echo htmlspecialchars($contact['name']); ?></h4>
                                                <p><?php echo htmlspecialchars($contact['email']); ?></p>
                                                <span class="message-preview"><?php echo substr(htmlspecialchars($contact['message']), 0, 50) . '...'; ?></span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-data">No recent contact messages</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions">
                            <a href="admin_employees.php?action=add" class="action-btn">
                                <i class="fas fa-user-plus"></i>
                                Add Employee
                            </a>
                            <a href="admin_schedule.php" class="action-btn">
                                <i class="fas fa-calendar-plus"></i>
                                Create Schedule
                            </a>
                            <a href="admin_reports.php" class="action-btn">
                                <i class="fas fa-download"></i>
                                Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="admin_modern.js"></script>
</body>
</html>