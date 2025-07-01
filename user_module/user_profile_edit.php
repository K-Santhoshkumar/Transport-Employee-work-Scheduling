<?php
session_start();
include '..\admin_module\database_connect.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = "";
$error_message = "";

// Fetch user details
$query = "SELECT name, email, phone, designation, department, status FROM employee WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $designation = trim($_POST['designation']);
    $department = trim($_POST['department']);
    $status = trim($_POST['status']);

    if (empty($name) || empty($email) || empty($phone) || empty($designation) || empty($department) || empty($status)) {
        $error_message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error_message = "Invalid phone number!";
    } else {
        $update_query = "UPDATE employee SET name = ?, email = ?, phone = ?, designation = ?, department = ?, status = ? WHERE id = ?";
        if ($stmt = $conn->prepare($update_query)) {
            $stmt->bind_param("ssssssi", $name, $email, $phone, $designation, $department, $status, $user_id);
            if ($stmt->execute()) {
                $success_message = "Profile updated successfully! Redirecting...";
                
                // **JavaScript Redirect to user_profile.php**
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'user_profile.php';
                        }, 2000); // Redirect after 2 seconds
                      </script>";
            } else {
                $error_message = "Error updating profile.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
<?php include('user_nav.php'); ?>
<main style="flex:1;max-width:1200px;margin:0 auto;padding:2rem;width:100%;">
    <section class="hero" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; padding: 3rem 2rem; border-radius: 20px; text-align: center; margin-bottom: 3rem; position: relative; overflow: hidden;">
        <div class="hero-content" style="position:relative;z-index:1;">
            <h1>Edit Profile</h1>
            <p>Update your profile details below.</p>
        </div>
    </section>
    <div class="form-container">
        <?php if ($success_message): ?>
            <p class="message"><?php echo $success_message; ?></p>
        <?php elseif ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <h5>Change Details</h5>
            <hr style="width: 100%; height: 0.1rem; background-color: black; border: none; margin:.5rem;">
            <label for="name">Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label for="designation">Designation</label>
            <input type="text" name="designation" value="<?php echo htmlspecialchars($user['designation']); ?>" required>

            <label for="department">Department</label>
            <input type="text" name="department" value="<?php echo htmlspecialchars($user['department']); ?>" required>

            <label for="status">Status</label>
            <select name="status" required>
                <option value="Active" <?php if ($user['status'] == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Inactive" <?php if ($user['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
            </select>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</main>
<?php include('user_footer.php'); ?>

<script>
    function toggleMenu() {
        document.querySelector(".nav").classList.toggle("active");
    }
</script>
</body>
</html>
