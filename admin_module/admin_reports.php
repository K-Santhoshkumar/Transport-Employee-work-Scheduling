<?php
include('./middleware.php');
middleware();
include('database_connect.php');

// Get statistics
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM USER"))['count'];
$total_employees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee"))['count'];
$total_schedules = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee_schedule"))['count'] ?? 0;

// Employee status breakdown
$status_counts = [];
$status_query = "SELECT status, COUNT(*) as count FROM employee GROUP BY status";
$status_result = mysqli_query($conn, $status_query);
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_counts[$row['status']] = $row['count'];
}

// Recent employees
$recent_employees = mysqli_query($conn, "SELECT * FROM employee ORDER BY created_at DESC LIMIT 5");

// Recent contact messages
$recent_contacts = mysqli_query($conn, "SELECT * FROM newsletter_subscription ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .report-section { margin-bottom: 2.5rem; }
        .report-title { font-size: 1.3rem; color: var(--primary-color); font-weight: 700; margin-bottom: 1rem; }
        .stat-list { display: flex; gap: 2rem; flex-wrap: wrap; }
        .stat-item { background: var(--surface-color); border-radius: 12px; box-shadow: var(--shadow); padding: 1.5rem 2rem; min-width: 180px; text-align: center; }
        .stat-item h2 { color: var(--primary-color); font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-item p { color: var(--text-secondary); font-size: 1rem; }
        .table-container { margin-top: 1rem; }
        .recent-list { list-style: none; padding: 0; margin: 0; }
        .recent-list li { background: var(--surface-color); border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 0.75rem; padding: 1rem; }
        .recent-list strong { color: var(--primary-color); }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Reports</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <div class="report-section">
                <div class="report-title"><i class="fas fa-chart-bar"></i> Overview</div>
                <div class="stat-list">
                    <div class="stat-item">
                        <h2><?php echo $total_users; ?></h2>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-item">
                        <h2><?php echo $total_employees; ?></h2>
                        <p>Total Employees</p>
                    </div>
                    <div class="stat-item">
                        <h2><?php echo $total_schedules; ?></h2>
                        <p>Total Schedules</p>
                    </div>
                </div>
            </div>
            <div class="report-section">
                <div class="report-title"><i class="fas fa-user-check"></i> Employee Status Breakdown</div>
                <div class="stat-list">
                    <div class="stat-item">
                        <h2><?php echo $status_counts['Active'] ?? 0; ?></h2>
                        <p>Active</p>
                    </div>
                    <div class="stat-item">
                        <h2><?php echo $status_counts['Inactive'] ?? 0; ?></h2>
                        <p>Inactive</p>
                    </div>
                    <div class="stat-item">
                        <h2><?php echo $status_counts['On Leave'] ?? 0; ?></h2>
                        <p>On Leave</p>
                    </div>
                </div>
            </div>
            <div class="report-section">
                <div class="report-title"><i class="fas fa-user-plus"></i> Recent Employees</div>
                <ul class="recent-list">
                    <?php while ($emp = mysqli_fetch_assoc($recent_employees)): ?>
                        <li><strong><?php echo htmlspecialchars($emp['name']); ?></strong> (<?php echo htmlspecialchars($emp['designation']); ?>, <?php echo htmlspecialchars($emp['department']); ?>) - Joined: <?php echo date('M d, Y', strtotime($emp['created_at'])); ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="report-section">
                <div class="report-title"><i class="fas fa-envelope"></i> Recent Contact Messages</div>
                <ul class="recent-list">
                    <?php while ($contact = mysqli_fetch_assoc($recent_contacts)): ?>
                        <li><strong><?php echo htmlspecialchars($contact['name']); ?></strong> (<?php echo htmlspecialchars($contact['email']); ?>): <?php echo substr(htmlspecialchars($contact['message']), 0, 60); ?>...</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </main>
</div>
</body>
</html> 