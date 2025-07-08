<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$success = '';
$error = '';

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get employee info if exists
$stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ?");
$stmt->execute([$user['email']]);
$employee = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    if (empty($name) || empty($email)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $_SESSION['user_id']])) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $success = 'Profile updated successfully!';
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } else {
            $error = 'Error updating profile.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Transport Management</title>
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
                    <a href="profile.php" class="nav-link" style="background: var(--primary-color);">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="schedules.php" class="nav-link">
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
            <h1 class="dashboard-title">My Profile</h1>
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

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="table-container">
                <div class="table-header">
                    <h2 class="table-title">Update Profile</h2>
                </div>
                <div style="padding: 2rem;">
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <?php if ($employee): ?>
            <div class="table-container">
                <div class="table-header">
                    <h2 class="table-title">Employee Information</h2>
                </div>
                <div style="padding: 2rem;">
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <strong>Name:</strong> <?php echo htmlspecialchars($employee['name']); ?>
                        </div>
                        <div>
                            <strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?>
                        </div>
                        <div>
                            <strong>Phone:</strong> <?php echo htmlspecialchars($employee['phone']); ?>
                        </div>
                        <div>
                            <strong>Designation:</strong> <?php echo htmlspecialchars($employee['designation']); ?>
                        </div>
                        <div>
                            <strong>Department:</strong> <?php echo htmlspecialchars($employee['department']); ?>
                        </div>
                        <div>
                            <strong>Working Date:</strong> <?php echo date('M d, Y', strtotime($employee['working_date'])); ?>
                        </div>
                        <div>
                            <strong>Shift Time:</strong> <?php echo htmlspecialchars($employee['shift_time']); ?>
                        </div>
                        <div>
                            <strong>Status:</strong> 
                            <span style="color: <?php echo $employee['status'] === 'Active' ? 'var(--success-color)' : 'var(--danger-color)'; ?>;">
                                <?php echo htmlspecialchars($employee['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>