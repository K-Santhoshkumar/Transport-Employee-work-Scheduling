<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $admin_id = $_SESSION['admin_id'];
                $email = trim($_POST['email']);
                $current_password = trim($_POST['current_password']);
                $new_password = trim($_POST['new_password']);
                
                if (!$email) {
                    $error_message = 'Email is required.';
                } else {
                    // Verify current password if new password is provided
                    if (!empty($new_password)) {
                        if (empty($current_password)) {
                            $error_message = 'Current password is required to set new password.';
                        } else {
                            // Verify current password
                            $verify_query = "SELECT password FROM ADMIN WHERE id = ?";
                            $verify_stmt = mysqli_prepare($conn, $verify_query);
                            mysqli_stmt_bind_param($verify_stmt, "i", $admin_id);
                            mysqli_stmt_execute($verify_stmt);
                            $verify_result = mysqli_stmt_get_result($verify_stmt);
                            $admin_data = mysqli_fetch_assoc($verify_result);
                            
                            if (!password_verify($current_password, $admin_data['password'])) {
                                $error_message = 'Current password is incorrect.';
                            } else {
                                // Update with new password
                                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                                $update_query = "UPDATE ADMIN SET email = ?, password = ? WHERE id = ?";
                                $update_stmt = mysqli_prepare($conn, $update_query);
                                mysqli_stmt_bind_param($update_stmt, "ssi", $email, $hashed_password, $admin_id);
                                if (mysqli_stmt_execute($update_stmt)) {
                                    $success_message = 'Profile updated successfully!';
                                    $_SESSION['admin_email'] = $email;
                                } else {
                                    $error_message = 'Error updating profile.';
                                }
                                mysqli_stmt_close($update_stmt);
                            }
                            mysqli_stmt_close($verify_stmt);
                        }
                    } else {
                        // Update only email
                        $update_query = "UPDATE ADMIN SET email = ? WHERE id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_query);
                        mysqli_stmt_bind_param($update_stmt, "si", $email, $admin_id);
                        if (mysqli_stmt_execute($update_stmt)) {
                            $success_message = 'Email updated successfully!';
                            $_SESSION['admin_email'] = $email;
                        } else {
                            $error_message = 'Error updating email.';
                        }
                        mysqli_stmt_close($update_stmt);
                    }
                }
                break;
                
            case 'backup_database':
                // Simple backup functionality
                $backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
                $command = "mysqldump --user=root --password= --host=localhost duty > backups/$backup_file";
                
                // Create backups directory if it doesn't exist
                if (!file_exists('backups')) {
                    mkdir('backups', 0777, true);
                }
                
                exec($command, $output, $return_var);
                if ($return_var === 0) {
                    $success_message = "Database backup created successfully: $backup_file";
                } else {
                    $error_message = 'Error creating database backup.';
                }
                break;
        }
    }
}

// Get current admin info
$admin_query = "SELECT email FROM ADMIN WHERE id = ?";
$admin_stmt = mysqli_prepare($conn, $admin_query);
mysqli_stmt_bind_param($admin_stmt, "i", $_SESSION['admin_id']);
mysqli_stmt_execute($admin_stmt);
$admin_result = mysqli_stmt_get_result($admin_stmt);
$admin_info = mysqli_fetch_assoc($admin_result);
mysqli_stmt_close($admin_stmt);

// Get system statistics
$stats = [
    'total_users' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM USER"))['count'],
    'total_employees' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee"))['count'],
    'total_schedules' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM employee_schedule"))['count'] ?? 0,
    'total_contacts' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM newsletter_subscription"))['count'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .settings-card {
            background: var(--surface-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        .settings-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.05), rgba(99, 102, 241, 0.02));
        }
        .settings-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .settings-content {
            padding: 1.5rem;
        }
        .system-info {
            display: grid;
            gap: 1rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: var(--background-color);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }
        .info-label {
            font-weight: 500;
            color: var(--text-secondary);
        }
        .info-value {
            font-weight: 600;
            color: var(--primary-color);
        }
        .backup-btn {
            background: var(--warning-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            justify-content: center;
        }
        .backup-btn:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
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
                <h1>System Settings</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="settings-grid">
                <!-- Admin Profile Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h3><i class="fas fa-user-cog"></i> Admin Profile</h3>
                    </div>
                    <div class="settings-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($admin_info['email']); ?>" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Enter current password to change">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- System Information -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h3><i class="fas fa-info-circle"></i> System Information</h3>
                    </div>
                    <div class="settings-content">
                        <div class="system-info">
                            <div class="info-item">
                                <span class="info-label">Total Users</span>
                                <span class="info-value"><?php echo $stats['total_users']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total Employees</span>
                                <span class="info-value"><?php echo $stats['total_employees']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total Schedules</span>
                                <span class="info-value"><?php echo $stats['total_schedules']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contact Messages</span>
                                <span class="info-value"><?php echo $stats['total_contacts']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">PHP Version</span>
                                <span class="info-value"><?php echo phpversion(); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Server Time</span>
                                <span class="info-value"><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Database Management -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h3><i class="fas fa-database"></i> Database Management</h3>
                    </div>
                    <div class="settings-content">
                        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                            Create a backup of your database to ensure data safety.
                        </p>
                        <form method="POST">
                            <input type="hidden" name="action" value="backup_database">
                            <button type="submit" class="backup-btn">
                                <i class="fas fa-download"></i> Create Database Backup
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="settings-content">
                        <div style="display: grid; gap: 1rem;">
                            <a href="admin_users.php" class="btn btn-secondary">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                            <a href="admin_employees.php" class="btn btn-secondary">
                                <i class="fas fa-user-tie"></i> Manage Employees
                            </a>
                            <a href="admin_schedule.php" class="btn btn-secondary">
                                <i class="fas fa-calendar-alt"></i> Manage Schedules
                            </a>
                            <a href="admin_reports.php" class="btn btn-secondary">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>