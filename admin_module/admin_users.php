<?php
include('./middleware.php');
middleware();
include('database_connect.php');

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $user_id = intval($_POST['user_id']);
                $delete_query = "DELETE FROM USER WHERE id = ?";
                $stmt = mysqli_prepare($conn, $delete_query);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "User deleted successfully!";
                } else {
                    $error_message = "Error deleting user.";
                }
                mysqli_stmt_close($stmt);
                break;
        }
    }
}

// Get all users
$users_query = "SELECT u.ID as id, u.name, u.email, e.name as emp_name, e.designation, e.department, e.status 
                FROM USER u 
                LEFT JOIN employee e ON u.ID = e.ID 
                ORDER BY u.ID DESC";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-bus"></i> Project 1</h2>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="active">
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
                <li>
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
                    <h1>User Management</h1>
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

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>All Users</h3>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <input type="text" class="form-control search-input" placeholder="Search users..." style="width: 250px;">
                            <a href="admin_user_add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Add User
                            </a>
                        </div>
                    </div>
                    <div class="card-content" style="padding: 0;">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="sortable">ID</th>
                                        <th class="sortable">Name</th>
                                        <th class="sortable">Email</th>
                                        <th class="sortable">Employee Name</th>
                                        <th class="sortable">Designation</th>
                                        <th class="sortable">Department</th>
                                        <th class="sortable">Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($users_result) > 0): ?>
                                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['emp_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($user['designation'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge <?php echo ($user['status'] ?? 'Active') === 'Active' ? 'badge-success' : 'badge-warning'; ?>">
                                                        <?php echo htmlspecialchars($user['status'] ?? 'Active'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div style="display: flex; gap: 0.5rem;">
                                                        <?php if (isset($user['id'])): ?>
                                                            <a href="admin_user_edit.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                                        data-confirm="Are you sure you want to delete this user?">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <button class="btn btn-secondary btn-sm" disabled>N/A</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="no-data">No users found</td>
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
</body>
</html>

<style>
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    border: 1px solid var(--success-color);
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning-color);
    border: 1px solid var(--warning-color);
}

.btn-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
}

.sortable:hover {
    background: rgba(37, 99, 235, 0.05);
}

.sortable::after {
    content: '\f0dc';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 0.5rem;
    opacity: 0.5;
}

.sortable.asc::after {
    content: '\f0de';
}

.sortable.desc::after {
    content: '\f0dd';
}
</style>