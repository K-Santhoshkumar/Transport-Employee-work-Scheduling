<?php
include('./middleware.php');
middleware();
include('database_connect.php');

// Create the employee_schedule table if it doesn't exist
$create_table_query = "CREATE TABLE IF NOT EXISTS employee_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    schedule_date DATE NOT NULL,
    shift_time VARCHAR(50) NOT NULL,
    bus_number VARCHAR(20),
    route VARCHAR(100),
    duty_type VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employee(ID) ON DELETE CASCADE
)";
mysqli_query($conn, $create_table_query);

// Handle schedule actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_schedule':
                $employee_id = intval($_POST['employee_id']);
                $schedule_date = $_POST['schedule_date'];
                $shift_time = $_POST['shift_time'];
                $bus_number = $_POST['bus_number'];
                $route = $_POST['route'];
                $duty_type = $_POST['duty_type'];
                $notes = $_POST['notes'];
                
                // Check if schedule already exists for this employee on this date
                $check_query = "SELECT id FROM employee_schedule WHERE employee_id = ? AND schedule_date = ?";
                $check_stmt = mysqli_prepare($conn, $check_query);
                mysqli_stmt_bind_param($check_stmt, "is", $employee_id, $schedule_date);
                mysqli_stmt_execute($check_stmt);
                $check_result = mysqli_stmt_get_result($check_stmt);
                
                if (mysqli_num_rows($check_result) > 0) {
                    $error_message = "Schedule already exists for this employee on the selected date.";
                } else {
                    $insert_query = "INSERT INTO employee_schedule (employee_id, schedule_date, shift_time, bus_number, route, duty_type, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insert_query);
                    mysqli_stmt_bind_param($stmt, "issssss", $employee_id, $schedule_date, $shift_time, $bus_number, $route, $duty_type, $notes);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Schedule added successfully!";
                    } else {
                        $error_message = "Error adding schedule.";
                    }
                    mysqli_stmt_close($stmt);
                }
                mysqli_stmt_close($check_stmt);
                break;
                
            case 'delete_schedule':
                $schedule_id = intval($_POST['schedule_id']);
                $delete_query = "DELETE FROM employee_schedule WHERE id = ?";
                $stmt = mysqli_prepare($conn, $delete_query);
                mysqli_stmt_bind_param($stmt, "i", $schedule_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Schedule deleted successfully!";
                } else {
                    $error_message = "Error deleting schedule.";
                }
                mysqli_stmt_close($stmt);
                break;
        }
    }
}

// Get all employees for dropdown
$employees_query = "SELECT ID, name FROM employee ORDER BY name";
$employees_result = mysqli_query($conn, $employees_query);

// Get all schedules with employee names
$schedules_query = "SELECT es.*, e.name as employee_name 
                   FROM employee_schedule es 
                   JOIN employee e ON es.employee_id = e.ID 
                   ORDER BY es.schedule_date DESC, es.shift_time";
$schedules_result = mysqli_query($conn, $schedules_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management</title>
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
                <li>
                    <a href="admin_employees.php">
                        <i class="fas fa-user-tie"></i>
                        <span>Employees</span>
                    </a>
                </li>
                <li class="active">
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
                    <h1>Schedule Management</h1>
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

                <!-- Add Schedule Form -->
                <div class="dashboard-card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h3><i class="fas fa-plus-circle"></i> Add New Schedule</h3>
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">
                            <i class="fas fa-plus"></i> Add Schedule
                        </button>
                    </div>
                    <div class="card-content" id="scheduleForm" style="display: none;">
                        <form method="POST" class="schedule-form">
                            <input type="hidden" name="action" value="add_schedule">
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="employee_id">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-control" required>
                                        <option value="">Select Employee</option>
                                        <?php while ($employee = mysqli_fetch_assoc($employees_result)): ?>
                                            <option value="<?php echo $employee['ID']; ?>">
                                                <?php echo htmlspecialchars($employee['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="schedule_date">Schedule Date</label>
                                    <input type="date" name="schedule_date" id="schedule_date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="shift_time">Shift Time</label>
                                    <select name="shift_time" id="shift_time" class="form-control" required>
                                        <option value="">Select Shift</option>
                                        <option value="Morning (6:00 AM - 2:00 PM)">Morning (6:00 AM - 2:00 PM)</option>
                                        <option value="Afternoon (2:00 PM - 10:00 PM)">Afternoon (2:00 PM - 10:00 PM)</option>
                                        <option value="Night (10:00 PM - 6:00 AM)">Night (10:00 PM - 6:00 AM)</option>
                                        <option value="Full Day (6:00 AM - 10:00 PM)">Full Day (6:00 AM - 10:00 PM)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="duty_type">Duty Type</label>
                                    <select name="duty_type" id="duty_type" class="form-control" required>
                                        <option value="">Select Duty Type</option>
                                        <option value="Driver">Driver</option>
                                        <option value="Conductor">Conductor</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Office Work">Office Work</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="bus_number">Bus Number</label>
                                    <input type="text" name="bus_number" id="bus_number" class="form-control" placeholder="e.g., BUS-001">
                                </div>

                                <div class="form-group">
                                    <label for="route">Route</label>
                                    <input type="text" name="route" id="route" class="form-control" placeholder="e.g., City Center - Airport">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Additional notes or instructions..."></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Schedule
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="toggleForm()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Schedules List -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-check"></i> All Schedules</h3>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <input type="text" class="form-control search-input" placeholder="Search schedules..." style="width: 250px;">
                            <input type="date" class="form-control" id="dateFilter" onchange="filterByDate()" style="width: 200px;">
                        </div>
                    </div>
                    <div class="card-content" style="padding: 0;">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="sortable">Date</th>
                                        <th class="sortable">Employee</th>
                                        <th class="sortable">Shift Time</th>
                                        <th class="sortable">Duty Type</th>
                                        <th class="sortable">Bus Number</th>
                                        <th class="sortable">Route</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($schedules_result) > 0): ?>
                                        <?php while ($schedule = mysqli_fetch_assoc($schedules_result)): ?>
                                            <tr>
                                                <td>
                                                    <span class="date-badge">
                                                        <?php echo date('M d, Y', strtotime($schedule['schedule_date'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($schedule['employee_name']); ?></strong>
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
                                                        <span class="notes-preview" title="<?php echo htmlspecialchars($schedule['notes']); ?>">
                                                            <?php echo substr(htmlspecialchars($schedule['notes']), 0, 30) . '...'; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">No notes</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div style="display: flex; gap: 0.5rem;">
                                                        <a href="admin_schedule_edit.php?id=<?php echo $schedule['id']; ?>" class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="delete_schedule">
                                                            <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    data-confirm="Are you sure you want to delete this schedule?">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="no-data">No schedules found</td>
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
    <script>
        function toggleForm() {
            const form = document.getElementById('scheduleForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function filterByDate() {
            const dateFilter = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('.table tbody tr');
            
            rows.forEach(row => {
                if (!dateFilter) {
                    row.style.display = '';
                    return;
                }
                
                const dateCell = row.querySelector('td:first-child');
                if (dateCell) {
                    const rowDate = new Date(dateCell.textContent.trim());
                    const filterDate = new Date(dateFilter);
                    
                    if (rowDate.toDateString() === filterDate.toDateString()) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }

        // Set minimum date to today
        document.getElementById('schedule_date').min = new Date().toISOString().split('T')[0];
    </script>

    <style>
        .schedule-form {
            max-width: none;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .date-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .shift-badge {
            background: rgba(6, 182, 212, 0.1);
            color: var(--info-color);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid var(--info-color);
        }

        .duty-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .duty-driver {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .duty-conductor {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
            border: 1px solid var(--warning-color);
        }

        .duty-supervisor {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .duty-maintenance {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }

        .duty-office-work {
            background: rgba(100, 116, 139, 0.1);
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
        }

        .notes-preview {
            cursor: help;
            color: var(--text-secondary);
            font-style: italic;
        }

        .text-muted {
            color: var(--text-light);
            font-style: italic;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>