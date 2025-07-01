<?php
include('./user_middleware.php');
middleware();
include('../admin_module/database_connect.php');

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
                
                // JavaScript Redirect to user_profile.php
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
    <title>Edit Profile - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .edit-form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .form-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .form-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .form-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .btn-cancel {
            background: var(--surface);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        
        .btn-cancel:hover {
            background: var(--surface-hover);
            color: var(--text-primary);
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .alert-error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
    </style>
</head>
<body>
    <header>
        <nav class="nav">
            <div class="nav-container">
                <a href="#" class="logo">
                    <i class="fas fa-bus"></i>
                    <span>Transport Manager</span>
                </a>
                
                <button class="menu-toggle" onclick="toggleMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <ul class="nav-links">
                    <li><a href="./user_welcome.php" class="<?= ($current_page == 'user_welcome.php') ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> <span>Home</span>
                    </a></li>
                    <li><a href="./user_search.php" class="<?= ($current_page == 'user_search.php') ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> <span>Search</span>
                    </a></li>
                    <li><a href="./user_profile.php" class="<?= ($current_page == 'user_profile.php') ? 'active' : '' ?>">
                        <i class="fas fa-user"></i> <span>Profile</span>
                    </a></li>
                    <li><a href="./user_contact.php" class="<?= ($current_page == 'user_contact.php') ? 'active' : '' ?>">
                        <i class="fas fa-envelope"></i> <span>Contact</span>
                    </a></li>
                    <li><a href="./user_about.php" class="<?= ($current_page == 'user_about.php') ? 'active' : '' ?>">
                        <i class="fas fa-info-circle"></i> <span>About</span>
                    </a></li>
                    <li><a href="./user_logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Edit Profile</h1>
                <p>Update your personal information and employment details.</p>
            </div>
        </section>
        
        <div class="edit-form-container">
            <div class="form-container">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h2 class="form-title">Update Profile</h2>
                    <p class="form-subtitle">Make changes to your personal information below</p>
                </div>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="designation">Designation</label>
                            <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($user['designation']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($user['department']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Active" <?php if ($user['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                <option value="Inactive" <?php if ($user['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="./user_profile.php" class="btn btn-cancel">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 Transport Management System. All rights reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const nav = document.querySelector('.nav');
            const navLinks = document.querySelector('.nav-links');
            
            if (!nav.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>