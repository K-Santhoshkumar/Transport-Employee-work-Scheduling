<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';

// Get all employees for dropdown
$employees_query = "SELECT ID, name FROM employee ORDER BY name";
$employees_result = mysqli_query($conn, $employees_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $schedule_date = trim($_POST['schedule_date'] ?? '');
    $shift_time = trim($_POST['shift_time'] ?? '');
    $bus_number = trim($_POST['bus_number'] ?? '');
    $route = trim($_POST['route'] ?? '');
    $duty_type = trim($_POST['duty_type'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!$employee_id || !$schedule_date || !$shift_time || !$duty_type) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Check if schedule already exists for this employee on this date
        $check_query = "SELECT id FROM employee_schedule WHERE employee_id = ? AND schedule_date = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "is", $employee_id, $schedule_date);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Schedule already exists for this employee on the selected date.";
        } else {
            $query = "INSERT INTO employee_schedule (employee_id, schedule_date, shift_time, bus_number, route, duty_type, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "issssss", $employee_id, $schedule_date, $shift_time, $bus_number, $route, $duty_type, $notes);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = 'Schedule added successfully!';
            } else {
                $error_message = 'Error adding schedule.';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .add-schedule-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 2rem;
            justify-content: center;
        }
        .form-container {
            background: linear-gradient(120deg, #f8fafc 60%, #e0e7ff 100%);
            box-shadow: 0 8px 32px 0 rgba(99,102,241,0.10);
            border-radius: 20px;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .btn-center {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        @media (max-width: 600px) {
            .form-container { padding: 1rem; }
            .add-schedule-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_schedule.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Add Schedule</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="dashboard-card">
                <div class="add-schedule-title"><i class="fas fa-calendar-plus"></i> Add New Schedule</div>
                <form method="POST" class="form-container">
                    <div class="form-group">
                        <label for="employee_id">Employee*</label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            <?php while ($emp = mysqli_fetch_assoc($employees_result)): ?>
                                <option value="<?php echo $emp['ID']; ?>"><?php echo htmlspecialchars($emp['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Schedule Date*</label><input type="date" name="schedule_date" required class="form-control" min="<?php echo date('Y-m-d'); ?>"></div>
                    <div class="form-group">
                        <label>Shift Time*</label>
                        <select name="shift_time" class="form-control" required>
                            <option value="">Select Shift</option>
                            <option value="Morning (6:00 AM - 2:00 PM)">Morning (6:00 AM - 2:00 PM)</option>
                            <option value="Afternoon (2:00 PM - 10:00 PM)">Afternoon (2:00 PM - 10:00 PM)</option>
                            <option value="Night (10:00 PM - 6:00 AM)">Night (10:00 PM - 6:00 AM)</option>
                            <option value="Full Day (6:00 AM - 10:00 PM)">Full Day (6:00 AM - 10:00 PM)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Duty Type*</label>
                        <select name="duty_type" class="form-control" required>
                            <option value="">Select Duty Type</option>
                            <option value="Driver">Driver</option>
                            <option value="Conductor">Conductor</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Office Work">Office Work</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Bus Number</label><input type="text" name="bus_number" class="form-control" placeholder="e.g., BUS-001"></div>
                    <div class="form-group"><label>Route</label><input type="text" name="route" class="form-control" placeholder="e.g., City Center - Airport"></div>
                    <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="3" placeholder="Additional notes or instructions..."></textarea></div>
                    <div class="btn-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>