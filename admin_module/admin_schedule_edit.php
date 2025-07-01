<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';
$schedule = null;
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_schedule.php');
    exit;
}
$schedule_id = intval($_GET['id']);

// Fetch schedule
$query = "SELECT * FROM employee_schedule WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $schedule_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$schedule = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$schedule) {
    header('Location: admin_schedule.php');
    exit;
}

// Fetch employees for dropdown
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
        $update_query = "UPDATE employee_schedule SET employee_id=?, schedule_date=?, shift_time=?, bus_number=?, route=?, duty_type=?, notes=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "issssssi", $employee_id, $schedule_date, $shift_time, $bus_number, $route, $duty_type, $notes, $schedule_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Schedule updated successfully!';
        } else {
            $error_message = 'Error updating schedule.';
        }
        mysqli_stmt_close($stmt);
        // Refresh schedule data
        $query = "SELECT * FROM employee_schedule WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $schedule = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .edit-schedule-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 2rem;
            justify-content: center;
        }
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
            padding-left: 0.75rem;
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
            .edit-schedule-title { font-size: 1.3rem; }
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
                <h1>Edit Schedule</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="dashboard-card">
                <div class="edit-schedule-title"><i class="fas fa-calendar-edit"></i> Edit Schedule</div>
                <form method="POST" class="form-container">
                    <div class="form-section-title">Schedule Information</div>
                    <div class="form-group">
                        <label for="employee_id">Employee*</label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            <?php while ($emp = mysqli_fetch_assoc($employees_result)): ?>
                                <option value="<?php echo $emp['ID']; ?>" <?php if ($emp['ID'] == $schedule['employee_id']) echo 'selected'; ?>><?php echo htmlspecialchars($emp['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Schedule Date*</label><input type="date" name="schedule_date" value="<?php echo htmlspecialchars($schedule['schedule_date']); ?>" required class="form-control"></div>
                    <div class="form-group"><label>Shift Time*</label><input type="text" name="shift_time" value="<?php echo htmlspecialchars($schedule['shift_time']); ?>" required class="form-control"></div>
                    <div class="form-group"><label>Bus Number</label><input type="text" name="bus_number" value="<?php echo htmlspecialchars($schedule['bus_number']); ?>" class="form-control"></div>
                    <div class="form-group"><label>Route</label><input type="text" name="route" value="<?php echo htmlspecialchars($schedule['route']); ?>" class="form-control"></div>
                    <div class="form-group"><label>Duty Type*</label><input type="text" name="duty_type" value="<?php echo htmlspecialchars($schedule['duty_type']); ?>" required class="form-control"></div>
                    <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($schedule['notes']); ?></textarea></div>
                    <div class="btn-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html> 