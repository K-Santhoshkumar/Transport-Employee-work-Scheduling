<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$success = '';
$error = '';

// Handle schedule deletion
if (isset($_GET['delete'])) {
    $schedule_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
    if ($stmt->execute([$schedule_id])) {
        $success = 'Schedule deleted successfully!';
    } else {
        $error = 'Error deleting schedule.';
    }
}

// Handle schedule creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_schedule'])) {
    $employee_id = (int)$_POST['employee_id'];
    $schedule_date = $_POST['schedule_date'];
    $shift_time = trim($_POST['shift_time']);
    $bus_number = trim($_POST['bus_number']) ?: null;
    $route = trim($_POST['route']) ?: null;
    $duty_type = trim($_POST['duty_type']);
    $notes = trim($_POST['notes']) ?: null;
    
    if (empty($employee_id) || empty($schedule_date) || empty($shift_time) || empty($duty_type)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO schedules (employee_id, schedule_date, shift_time, bus_number, route, duty_type, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$employee_id, $schedule_date, $shift_time, $bus_number, $route, $duty_type, $notes])) {
            $success = 'Schedule created successfully!';
        } else {
            $error = 'Error creating schedule.';
        }
    }
}

// Get all employees for dropdown
$stmt = $pdo->query("SELECT id, name FROM employees ORDER BY name");
$employees = $stmt->fetchAll();

// Get all schedules with employee names
$stmt = $pdo->query("SELECT s.*, e.name as employee_name FROM schedules s JOIN employees e ON s.employee_id = e.id ORDER BY s.schedule_date DESC");
$schedules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules - Admin</title>
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
                    <a href="dashboard.php" class="nav-link">
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
                    <a href="schedules.php" class="nav-link admin">
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
            <h1 class="dashboard-title">Manage Schedules</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Add Schedule Form -->
        <div class="table-container" style="margin-bottom: 2rem;">
            <div class="table-header">
                <h2 class="table-title">Add New Schedule</h2>
            </div>
            <div style="padding: 2rem;">
                <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label for="employee_id">Employee *</label>
                        <select id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule_date">Schedule Date *</label>
                        <input type="date" id="schedule_date" name="schedule_date" required>
                    </div>
                    <div class="form-group">
                        <label for="shift_time">Shift Time *</label>
                        <input type="text" id="shift_time" name="shift_time" required placeholder="e.g., 9:00 AM - 5:00 PM">
                    </div>
                    <div class="form-group">
                        <label for="duty_type">Duty Type *</label>
                        <select id="duty_type" name="duty_type" required>
                            <option value="">Select Duty Type</option>
                            <option value="Driver">Driver</option>
                            <option value="Conductor">Conductor</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bus_number">Bus Number</label>
                        <input type="text" id="bus_number" name="bus_number" placeholder="e.g., TN45AB1234">
                    </div>
                    <div class="form-group">
                        <label for="route">Route</label>
                        <input type="text" id="route" name="route" placeholder="e.g., Chennai to Madurai">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <button type="submit" name="create_schedule" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Add Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schedules List -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">All Schedules</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Shift Time</th>
                        <th>Duty Type</th>
                        <th>Bus Number</th>
                        <th>Route</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary);">No schedules found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo $schedule['id']; ?></td>
                                <td><?php echo htmlspecialchars($schedule['employee_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($schedule['schedule_date'])); ?></td>
                                <td><?php echo htmlspecialchars($schedule['shift_time']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['duty_type']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['bus_number'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($schedule['route'] ?: 'N/A'); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $schedule['id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this schedule?')">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>