<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Get all schedules for this user
$stmt = $pdo->prepare("
    SELECT s.*, e.name as employee_name 
    FROM schedules s 
    JOIN employees e ON s.employee_id = e.id 
    WHERE e.email = (SELECT email FROM users WHERE id = ?) 
    ORDER BY s.schedule_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$schedules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedules - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">
                    <i class="fas fa-bus"></i>
                    Transport Manager
                </a>
                <div class="nav-links">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="profile.php" class="nav-link">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="schedules.php" class="nav-link" style="background: var(--primary-color);">
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
            <h1 class="dashboard-title">My Schedules</h1>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">All Schedules</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Shift Time</th>
                        <th>Duty Type</th>
                        <th>Bus Number</th>
                        <th>Route</th>
                        <th>Notes</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                No schedules found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($schedule['schedule_date'])); ?></td>
                                <td><?php echo htmlspecialchars($schedule['shift_time']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['duty_type']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['bus_number'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($schedule['route'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($schedule['notes'] ?: 'N/A'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($schedule['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>