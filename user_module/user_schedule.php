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
</head>
<body>
    <?php include('user_nav.php'); ?>
    <main style="flex:1;max-width:1200px;margin:0 auto;padding:2rem;width:100%;">
        <section class="hero" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; padding: 3rem 2rem; border-radius: 20px; text-align: center; margin-bottom: 3rem; position: relative; overflow: hidden;">
            <div class="hero-content" style="position:relative;z-index:1;">
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
                            <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
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
                        <div style="text-align: center; padding: 1rem; background: rgba(99, 102, 241, 0.05); border-radius: var(--border-radius);">
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
                                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
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
    <?php include('user_footer.php'); ?>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const nav = document.querySelector('.nav');
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            
            if (!nav.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>