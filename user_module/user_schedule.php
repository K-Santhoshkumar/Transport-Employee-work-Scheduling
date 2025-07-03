<?php
include('./user_middleware.php');
middleware();
include('../admin_module/database_connect.php');

$user_id = $_SESSION['user_id'];

// Get user's schedules
$schedules_query = "SELECT es.*, e.name as employee_name 
                   FROM employee_schedule es 
                   JOIN employee e ON es.employee_id = e.ID 
                   WHERE e.id = ? 
                   ORDER BY es.schedule_date DESC";
$stmt = mysqli_prepare($conn, $schedules_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$schedules_result = mysqli_stmt_get_result($stmt);

// Get upcoming schedules (next 7 days)
$upcoming_query = "SELECT es.*, e.name as employee_name 
                  FROM employee_schedule es 
                  JOIN employee e ON es.employee_id = e.ID 
                  WHERE e.id = ? AND es.schedule_date >= CURDATE() AND es.schedule_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  ORDER BY es.schedule_date ASC";
$upcoming_stmt = mysqli_prepare($conn, $upcoming_query);
mysqli_stmt_bind_param($upcoming_stmt, "i", $user_id);
mysqli_stmt_execute($upcoming_stmt);
$upcoming_result = mysqli_stmt_get_result($upcoming_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Schedule - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .schedule-card {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-content {
            padding: 1.5rem;
        }
        .upcoming-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .upcoming-item {
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            transition: var(--transition);
            background: rgba(37, 99, 235, 0.02);
        }
        .upcoming-item:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            transform: translateX(4px);
        }
        .upcoming-date {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .upcoming-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .table-container {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }
        .table th {
            background: var(--surface-hover);
            font-weight: 600;
            color: var(--text-primary);
        }
        .table tbody tr:hover {
            background: var(--surface-hover);
        }
        .date-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .shift-badge {
            background: rgba(6, 182, 212, 0.1);
            color: var(--accent-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid var(--accent-color);
        }
        .duty-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .duty-driver {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        .duty-conductor {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid #d97706;
        }
        .duty-supervisor {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        .duty-maintenance {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
        .duty-office-work {
            background: rgba(100, 116, 139, 0.1);
            color: var(--text-secondary);
            border: 1px solid var(--text-secondary);
        }
        .no-data {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
        }
        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <header>
        <nav class="nav">
            <div class="nav-container">
                <a href="#" class="logo">
                    <i class="fas fa-bus"></i>
                    <span>Transport Manager</span>
                </a>
                
                <button class="menu-toggle" onclick="toggleMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <ul class="nav-links">
                    <li><a href="./user_welcome.php" class="<?= ($current_page == 'user_welcome.php') ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> <span>Home</span>
                    </a></li>
                    <li><a href="./user_dashboard.php" class="<?= ($current_page == 'user_dashboard.php') ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a></li>
                    <li><a href="./user_schedule.php" class="<?= ($current_page == 'user_schedule.php') ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i> <span>Schedule</span>
                    </a></li>
                    <li><a href="./user_search.php" class="<?= ($current_page == 'user_search.php') ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> <span>Search</span>
                    </a></li>
                    <li><a href="./user_profile.php" class="<?= ($current_page == 'user_profile.php') ? 'active' : '' ?>">
                        <i class="fas fa-user"></i> <span>Profile</span>
                    </a></li>
                    <li><a href="./user_contact.php" class="<?= ($current_page == 'user_contact.php') ? 'active' : '' ?>">
                        <i class="fas fa-envelope"></i> <span>Contact</span>
                    </a></li>
                    <li><a href="./user_about.php" class="<?= ($current_page == 'user_about.php') ? 'active' : '' ?>">
                        <i class="fas fa-info-circle"></i> <span>About</span>
                    </a></li>
                    <li><a href="./user_logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Your Schedule</h1>
                <p>View your upcoming and past schedules below.</p>
            </div>
        </section>
        
        <div class="schedule-grid">
            <!-- Upcoming Schedules -->
            <div class="schedule-card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Upcoming This Week</h3>
                </div>
                <div class="card-content">
                    <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
                        <div class="upcoming-list">
                            <?php while ($schedule = mysqli_fetch_assoc($upcoming_result)): ?>
                                <div class="upcoming-item">
                                    <div class="upcoming-date">
                                        <?php echo date('l, M d, Y', strtotime($schedule['schedule_date'])); ?>
                                    </div>
                                    <div class="upcoming-details">
                                        <div><strong>Shift:</strong> <?php echo htmlspecialchars($schedule['shift_time']); ?></div>
                                        <div><strong>Duty:</strong> <?php echo htmlspecialchars($schedule['duty_type']); ?></div>
                                        <?php if ($schedule['bus_number']): ?>
                                            <div><strong>Bus:</strong> <?php echo htmlspecialchars($schedule['bus_number']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($schedule['route']): ?>
                                            <div><strong>Route:</strong> <?php echo htmlspecialchars($schedule['route']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($schedule['notes']): ?>
                                        <div style="margin-top: 0.5rem; font-style: italic; color: var(--text-secondary);">
                                            <strong>Notes:</strong> <?php echo htmlspecialchars($schedule['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-calendar-times"></i>
                            <p>No upcoming schedules for this week</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="schedule-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Schedule Overview</h3>
                </div>
                <div class="card-content">
                    <?php
                    // Reset result pointer
                    mysqli_data_seek($schedules_result, 0);
                    $total_schedules = mysqli_num_rows($schedules_result);
                    
                    // Count schedules by duty type
                    $duty_counts = [];
                    while ($schedule = mysqli_fetch_assoc($schedules_result)) {
                        $duty_type = $schedule['duty_type'];
                        $duty_counts[$duty_type] = ($duty_counts[$duty_type] ?? 0) + 1;
                    }
                    ?>
                    
                    <div style="display: grid; gap: 1rem;">
                        <div style="text-align: center; padding: 1rem; background: rgba(37, 99, 235, 0.05); border-radius: var(--border-radius);">
                            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color);"><?php echo $total_schedules; ?></div>
                            <div style="color: var(--text-secondary);">Total Schedules</div>
                        </div>
                        
                        <?php if (!empty($duty_counts)): ?>
                            <div>
                                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Duty Breakdown</h4>
                                <?php foreach ($duty_counts as $duty => $count): ?>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span><?php echo htmlspecialchars($duty); ?></span>
                                        <span style="font-weight: 600;"><?php echo $count; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Schedules -->
        <div class="schedule-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All My Schedules</h3>
            </div>
            <div class="card-content" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift Time</th>
                                <th>Duty Type</th>
                                <th>Bus Number</th>
                                <th>Route</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Reset result pointer again
                            mysqli_data_seek($schedules_result, 0);
                            if (mysqli_num_rows($schedules_result) > 0): 
                            ?>
                                <?php while ($schedule = mysqli_fetch_assoc($schedules_result)): ?>
                                    <tr>
                                        <td>
                                            <span class="date-badge">
                                                <?php echo date('M d, Y', strtotime($schedule['schedule_date'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="shift-badge">
                                                <?php echo htmlspecialchars($schedule['shift_time']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="duty-badge duty-<?php echo strtolower(str_replace(' ', '-', $schedule['duty_type'])); ?>">
                                                <?php echo htmlspecialchars($schedule['duty_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($schedule['bus_number'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($schedule['route'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if ($schedule['notes']): ?>
                                                <span title="<?php echo htmlspecialchars($schedule['notes']); ?>">
                                                    <?php echo substr(htmlspecialchars($schedule['notes']), 0, 50) . (strlen($schedule['notes']) > 50 ? '...' : ''); ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--text-light); font-style: italic;">No notes</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="no-data">
                                        <i class="fas fa-calendar-times"></i>
                                        <p>No schedules assigned yet</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 Transport Management System. All rights reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const nav = document.querySelector('.nav');
            const navLinks = document.querySelector('.nav-links');
            
            if (!nav.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>