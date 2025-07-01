<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';
$user = null;
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_users.php');
    exit;
}
$user_id = intval($_GET['id']);

// Fetch user
$query = "SELECT * FROM USER WHERE ID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$user) {
    header('Location: admin_users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $update_password = !empty($password);

    if (!$name || !$email) {
        $error_message = 'Please fill in all required fields.';
    } else {
        if ($update_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE USER SET name=?, email=?, password=? WHERE ID=?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $hashed_password, $user_id);
        } else {
            $update_query = "UPDATE USER SET name=?, email=? WHERE ID=?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
        }
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'User updated successfully!';
        } else {
            $error_message = 'Error updating user. Please check for duplicate name or email.';
        }
        mysqli_stmt_close($stmt);
        // Refresh user data
        $query = "SELECT * FROM USER WHERE ID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .edit-user-title {
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
            .edit-user-title { font-size: 1.3rem; }
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
                <h1>Edit User</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="dashboard-card">
                <div class="edit-user-title"><i class="fas fa-user-edit"></i> Edit User</div>
                <form method="POST" class="form-container">
                    <div class="form-group"><label>Name*</label><input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="form-control"></div>
                    <div class="form-group"><label>Email*</label><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="form-control"></div>
                    <div class="form-group"><label>New Password</label><input type="password" name="password" placeholder="Leave blank to keep current password" class="form-control"></div>
                    <div class="btn-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html> 