<?php
include('./middleware.php');
middleware();
include('database_connect.php');

// Get comprehensive statistics
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM USER"))['count'];
$total_employees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee"))['count'];
$total_schedules = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee_schedule"))['count'] ?? 0;
$total_contacts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM newsletter_subscription"))['count'] ?? 0;

// Employee status breakdown
$status_counts = [];
$status_query = "SELECT status, COUNT(*) as count FROM employee GROUP BY status";
$status_result = mysqli_query($conn, $status_query);
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_counts[$row['status']] = $row['count'];
}

// Department breakdown
$dept_counts = [];
$dept_query = "SELECT department, COUNT(*) as count FROM employee GROUP BY department";
$dept_result = mysqli_query($conn, $dept_query);
while ($row = mysqli_fetch_assoc($dept_result)) {
    $dept_counts[$row['department']] = $row['count'];
}

// Recent employees
$recent_employees = mysqli_query($conn, "SELECT * FROM employee ORDER BY created_at DESC LIMIT 10");

// Recent schedules
$recent_schedules = mysqli_query($conn, "SELECT es.*, e.name as employee_name FROM employee_schedule es JOIN employee e ON es.employee_id = e.ID ORDER BY es.created_at DESC LIMIT 10");

// Recent contact messages
$recent_contacts = mysqli_query($conn, "SELECT * FROM newsletter_subscription ORDER BY created_at DESC LIMIT 10");

// Handle export functionality
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $export_type . '_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    switch ($export_type) {
        case 'employees':
            fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Designation', 'Department', 'Status', 'Batch No', 'Bus Number', 'Created At']);
            $export_query = "SELECT * FROM employee ORDER BY created_at DESC";
            $export_result = mysqli_query($conn, $export_query);
            while ($row = mysqli_fetch_assoc($export_result)) {
                fputcsv($output, $row);
            }
            break;
            
        case 'schedules':
            fputcsv($output, ['ID', 'Employee Name', 'Schedule Date', 'Shift Time', 'Duty Type', 'Bus Number', 'Route', 'Notes', 'Created At']);
            $export_query = "SELECT es.*, e.name as employee_name FROM employee_schedule es JOIN employee e ON es.employee_id = e.ID ORDER BY es.schedule_date DESC";
            $export_result = mysqli_query($conn, $export_query);
            while ($row = mysqli_fetch_assoc($export_result)) {
                fputcsv($output, [$row['id'], $row['employee_name'], $row['schedule_date'], $row['shift_time'], $row['duty_type'], $row['bus_number'], $row['route'], $row['notes'], $row['created_at']]);
            }
            break;
            
        case 'contacts':
            fputcsv($output, ['ID', 'Name', 'Email', 'Message', 'Created At']);
            $export_query = "SELECT * FROM newsletter_subscription ORDER BY created_at DESC";
            $export_result = mysqli_query($conn, $export_query);
            while ($row = mysqli_fetch_assoc($export_result)) {
                fputcsv($output, $row);
            }
            break;
    }
    
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .report-section { 
            margin-bottom: 2.5rem; 
            background: var(--surface-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        .report-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.05), rgba(99, 102, 241, 0.02));
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .report-title { 
            font-size: 1.3rem; 
            color: var(--primary-color); 
            font-weight: 700; 
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .export-btn {
            padding: 0.5rem 1rem;
            background: var(--success-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .export-btn:hover {
            background: #047857;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        .report-content {
            padding: 1.5rem;
        }
        .stat-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-item { 
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: 12px; 
            padding: 1.5rem 2rem; 
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            pointer-events: none;
        }
        .stat-item:nth-child(2) {
            background: linear-gradient(135deg, var(--success-color), #047857);
        }
        .stat-item:nth-child(3) {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }
        .stat-item:nth-child(4) {
            background: linear-gradient(135deg, var(--info-color), #0891b2);
        }
        .stat-item h2 { 
            font-size: 2.5rem; 
            margin-bottom: 0.5rem; 
            font-weight: 800;
            position: relative;
            z-index: 1;
        }
        .stat-item p { 
            font-size: 1rem; 
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .breakdown-item {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }
        .breakdown-item:hover {
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }
        .breakdown-label {
            font-weight: 500;
            color: var(--text-primary);
        }
        .breakdown-value {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.125rem;
        }
        .recent-list { 
            list-style: none; 
            padding: 0; 
            margin: 0; 
        }
        .recent-list li { 
            background: var(--surface-color); 
            border: 1px solid var(--border-color);
            border-radius: 8px; 
            margin-bottom: 0.75rem; 
            padding: 1rem;
            transition: var(--transition);
        }
        .recent-list li:hover {
            box-shadow: var(--shadow);
            transform: translateX(4px);
        }
        .recent-list strong { 
            color: var(--primary-color); 
        }
        .recent-date {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .chart-placeholder {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: 3rem;
            text-align: center;
            color: var(--text-light);
            margin-top: 1rem;
        }
        .chart-placeholder i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Reports & Analytics</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <!-- Overview Statistics -->
            <div class="report-section">
                <div class="report-header">
                    <div class="report-title"><i class="fas fa-chart-bar"></i> System Overview</div>
                </div>
                <div class="report-content">
                    <div class="stat-grid">
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
                        <div class="stat-item">
                            <h2><?php echo $total_contacts; ?></h2>
                            <p>Contact Messages</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Status Breakdown -->
            <div class="report-section">
                <div class="report-header">
                    <div class="report-title"><i class="fas fa-user-check"></i> Employee Status Breakdown</div>
                    <a href="?export=employees" class="export-btn">
                        <i class="fas fa-download"></i> Export Employees
                    </a>
                </div>
                <div class="report-content">
                    <div class="breakdown-grid">
                        <?php foreach ($status_counts as $status => $count): ?>
                            <div class="breakdown-item">
                                <span class="breakdown-label"><?php echo htmlspecialchars($status); ?></span>
                                <span class="breakdown-value"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Department Breakdown -->
            <div class="report-section">
                <div class="report-header">
                    <div class="report-title"><i class="fas fa-building"></i> Department Distribution</div>
                </div>
                <div class="report-content">
                    <div class="breakdown-grid">
                        <?php foreach ($dept_counts as $dept => $count): ?>
                            <div class="breakdown-item">
                                <span class="breakdown-label"><?php echo htmlspecialchars($dept); ?></span>
                                <span class="breakdown-value"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem;">
                <!-- Recent Employees -->
                <div class="report-section">
                    <div class="report-header">
                        <div class="report-title"><i class="fas fa-user-plus"></i> Recent Employees</div>
                    </div>
                    <div class="report-content">
                        <ul class="recent-list">
                            <?php while ($emp = mysqli_fetch_assoc($recent_employees)): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($emp['name']); ?></strong> 
                                    (<?php echo htmlspecialchars($emp['designation']); ?>, <?php echo htmlspecialchars($emp['department']); ?>)
                                    <div class="recent-date">Joined: <?php echo date('M d, Y', strtotime($emp['created_at'])); ?></div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>

                <!-- Recent Schedules -->
                <div class="report-section">
                    <div class="report-header">
                        <div class="report-title"><i class="fas fa-calendar-alt"></i> Recent Schedules</div>
                        <a href="?export=schedules" class="export-btn">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                    <div class="report-content">
                        <ul class="recent-list">
                            <?php while ($schedule = mysqli_fetch_assoc($recent_schedules)): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($schedule['employee_name']); ?></strong> - 
                                    <?php echo htmlspecialchars($schedule['duty_type']); ?>
                                    <div class="recent-date"><?php echo date('M d, Y', strtotime($schedule['schedule_date'])); ?> - <?php echo htmlspecialchars($schedule['shift_time']); ?></div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>

                <!-- Recent Contacts -->
                <div class="report-section">
                    <div class="report-header">
                        <div class="report-title"><i class="fas fa-envelope"></i> Recent Messages</div>
                        <a href="?export=contacts" class="export-btn">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                    <div class="report-content">
                        <ul class="recent-list">
                            <?php while ($contact = mysqli_fetch_assoc($recent_contacts)): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($contact['name']); ?></strong> 
                                    (<?php echo htmlspecialchars($contact['email']); ?>)
                                    <div style="margin-top: 0.25rem; color: var(--text-secondary);">
                                        <?php echo substr(htmlspecialchars($contact['message']), 0, 80); ?>...
                                    </div>
                                    <div class="recent-date"><?php echo date('M d, Y H:i', strtotime($contact['created_at'])); ?></div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>