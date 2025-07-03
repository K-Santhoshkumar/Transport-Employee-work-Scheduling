<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$name || !$email || !$password) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM USER WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = 'Email already exists. Please use a different email.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO USER (name, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = 'User added successfully!';
            } else {
                $error_message = 'Error adding user. Please try again.';
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
    <title>Add User</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .add-user-title {
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
            max-width: 500px;
            margin: 0 auto;
        }
        .btn-center {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        @media (max-width: 600px) {
            .form-container { padding: 1rem; }
            .add-user-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Add User</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="dashboard-card">
                <div class="add-user-title"><i class="fas fa-user-plus"></i> Add New User</div>
                <form method="POST" class="form-container">
                    <div class="form-group"><label>Name*</label><input type="text" name="name" required class="form-control"></div>
                    <div class="form-group"><label>Email*</label><input type="email" name="email" required class="form-control"></div>
                    <div class="form-group"><label>Password*</label><input type="password" name="password" required class="form-control" minlength="8"></div>
                    <div class="btn-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>