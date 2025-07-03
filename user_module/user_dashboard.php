<?php
include('./user_middleware.php');
middleware();
include('../admin_module/database_connect.php');

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT u.name as user_name, u.email as user_email, e.name as emp_name, e.designation, e.department, e.status 
               FROM USER u 
               LEFT JOIN employee e ON u.id = e.id 
               WHERE u.id = ?";
$user_stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_info = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($user_stmt);

// Get upcoming schedules (next 7 days)
$upcoming_query = "SELECT es.*, e.name as employee_name 
                  FROM employee_schedule es 
                  JOIN employee e ON es.employee_id = e.ID 
                  WHERE e.id = ? AND es.schedule_date >= CURDATE() AND es.schedule_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  ORDER BY es.schedule_date ASC LIMIT 5";
$upcoming_stmt = mysqli_prepare($conn, $upcoming_query);
mysqli_stmt_bind_param($upcoming_stmt, "i", $user_id);
mysqli_stmt_execute($upcoming_stmt);
$upcoming_result = mysqli_stmt_get_result($upcoming_stmt);

// Get schedule statistics
$total_schedules_query = "SELECT COUNT(*) as count FROM employee_schedule es JOIN employee e ON es.employee_id = e.ID WHERE e.id = ?";
$total_stmt = mysqli_prepare($conn, $total_schedules_query);
mysqli_stmt_bind_param($total_stmt, "i", $user_id);
mysqli_stmt_execute($total_stmt);
$total_schedules = mysqli_fetch_assoc(mysqli_stmt_get_result($total_stmt))['count'];
mysqli_stmt_close($total_stmt);

// Get this month's schedules
$month_schedules_query = "SELECT COUNT(*) as count FROM employee_schedule es JOIN employee e ON es.employee_id = e.ID WHERE e.id = ? AND MONTH(es.schedule_date) = MONTH(CURDATE()) AND YEAR(es.schedule_date) = YEAR(CURDATE())";
$month_stmt = mysqli_prepare($conn, $month_schedules_query);
mysqli_stmt_bind_param($month_stmt, "i", $user_id);
mysqli_stmt_execute($month_stmt);
$month_schedules = mysqli_fetch_assoc(mysqli_stmt_get_result($month_stmt))['count'];
mysqli_stmt_close($month_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            pointer-events: none;
        }
        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, var(--success-color), #047857);
        }
        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }
        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .welcome-card {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .welcome-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
            font-weight: 700;
        }
        .upcoming-schedule {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .schedule-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        .schedule-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .schedule-content {
            padding: 1.5rem;
        }
        .schedule-item {
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        .schedule-item:hover {
            box-shadow: var(--shadow);
            transform: translateX(4px);
        }
        .schedule-item:last-child {
            margin-bottom: 0;
        }
        .schedule-date {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .schedule-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .action-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--text-primary);
            transition: var(--transition);
        }
        .action-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
            border-color: var(--primary-color);
        }
        .action-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }
        .action-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .action-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
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
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="welcome-avatar">
                <?php echo strtoupper(substr($user_info['user_name'] ?? 'U', 0, 2)); ?>
            </div>
            <h2>Welcome back, <?php echo htmlspecialchars($user_info['user_name'] ?? 'User'); ?>!</h2>
            <?php if ($user_info['emp_name']): ?>
                <p>Employee: <?php echo htmlspecialchars($user_info['emp_name']); ?> - <?php echo htmlspecialchars($user_info['designation']); ?></p>
                <p>Department: <?php echo htmlspecialchars($user_info['department']); ?></p>
            <?php else: ?>
                <p>Welcome to the Transport Management System</p>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_schedules; ?></div>
                <div class="stat-label">Total Schedules</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $month_schedules; ?></div>
                <div class="stat-label">This Month</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo mysqli_num_rows($upcoming_result); ?></div>
                <div class="stat-label">Upcoming</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $user_info['status'] === 'Active' ? '100' : '0'; ?>%</div>
                <div class="stat-label">Availability</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Upcoming Schedules -->
            <div class="upcoming-schedule">
                <div class="schedule-header">
                    <h3><i class="fas fa-calendar-alt"></i> Upcoming Schedules</h3>
                </div>
                <div class="schedule-content">
                    <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
                        <?php while ($schedule = mysqli_fetch_assoc($upcoming_result)): ?>
                            <div class="schedule-item">
                                <div class="schedule-date">
                                    <?php echo date('l, M d, Y', strtotime($schedule['schedule_date'])); ?>
                                </div>
                                <div class="schedule-details">
                                    <div><strong>Shift:</strong> <?php echo htmlspecialchars($schedule['shift_time']); ?></div>
                                    <div><strong>Duty:</strong> <?php echo htmlspecialchars($schedule['duty_type']); ?></div>
                                    <?php if ($schedule['bus_number']): ?>
                                        <div><strong>Bus:</strong> <?php echo htmlspecialchars($schedule['bus_number']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($schedule['route']): ?>
                                        <div><strong>Route:</strong> <?php echo htmlspecialchars($schedule['route']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="user_schedule.php" class="btn btn-primary">View All Schedules</a>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-calendar-times"></i>
                            <p>No upcoming schedules</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="upcoming-schedule">
                <div class="schedule-header">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="schedule-content">
                    <div class="quick-actions">
                        <a href="user_schedule.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="action-title">My Schedule</div>
                            <div class="action-description">View your work schedule and upcoming duties</div>
                        </a>
                        
                        <a href="user_search.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="action-title">Search Employees</div>
                            <div class="action-description">Find information about other staff members</div>
                        </a>
                        
                        <a href="user_profile.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="action-title">My Profile</div>
                            <div class="action-description">Update your personal information</div>
                        </a>
                        
                        <a href="user_contact.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="action-title">Contact Support</div>
                            <div class="action-description">Get help from the administration</div>
                        </a>
                    </div>
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