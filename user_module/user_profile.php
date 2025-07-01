<?php
session_start();
include '..\admin_module\database_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php'); 
    exit();
}
$user_id = $_SESSION['user_id'];
$query = "SELECT employee.name AS emp_name, employee.email, employee.phone, 
                 employee.designation, employee.department, employee.status 
          FROM employee 
          JOIN user ON employee.id = user.id 
          WHERE user.id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Database query failed.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>USER PROFILE</title>
    <style>
        /* Use the same style as user_about.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color:#ffdfb9; min-height: 100vh; font-family: "Poppins", sans-serif; display: flex; flex-direction: column; }
        .nav { background: rgb(35, 178, 225); display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 1rem; position: sticky; top: 0; z-index: 1000; box-shadow: 0 0.4rem 0.6rem rgba(0, 0, 0, 0.2); }
        .top-bar { display: flex; align-items: center; gap: 1rem; }
        .top-bar h2 { color: black; font-size: 1.5rem; font-weight: bold; font-variant: small-caps; }
        .pages { display: flex; gap: 1rem; }
        .pages a { text-decoration: none; background-color: white; color: black; border: 1px solid rgb(35, 178, 225); padding: 0.5rem 1rem; font-weight: bold; border-radius: 0.5rem; transition: 0.3s ease-in-out; }
        .pages a:hover { background: linear-gradient(to right,rgb(100, 206, 103),rgb(64, 200, 234)); color: white; transform: scale(1.05); box-shadow: 0 0.4rem 1rem rgba(0, 0, 0, 0.3); }
        .active-link { background: linear-gradient(to right,rgb(78, 239, 236),rgb(201, 76, 223)); color: white; border: 2px solid white; transform: scale(1.1); font-weight: bold; padding: 0.5rem 1.2rem; }
        .menu-toggle { display: none; font-size: 1.8rem; color: #ffdfb9; cursor: pointer; background: none; border: none; }
        footer{ background-color: rgb(35, 178, 225); color: white; text-align: center; padding: 1rem; margin-top: auto; }
        @media (max-width: 768px) { .pages { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: rgb(35, 178, 225); padding: 1rem 0; text-align: center; } .pages a { display: block; padding: 1rem; margin: 0.5rem; } .menu-toggle { display: block; } .nav.active .pages { display: flex; } }
        .body{ background-image: url("./assets/photo-1544620347-c4fd4a3d5957.jpg"); width: 100%; height: auto; background-size: cover; background-position: center; }
        .main { text-align: center; padding: 1rem; flex-grow: 1; color: white; font-size: 1.5rem; }
        .main h1{ font-size: 4rem; }
        .profile-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 45%; text-align: center; color: black; margin: 2rem auto; }
        .profile-container h2 { color: rgb(35, 178, 225); margin-bottom: 1rem; }
        .profile-container p { font-size: 1.1rem; color: #333; margin: 0.5rem 0; }
        .profile-container a { display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgb(35, 178, 225); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .profile-container a:hover { background: linear-gradient(to right,rgb(78, 239, 236),rgb(201, 76, 223)); color: white; }
    </style>
</head>
<body class="body">
<header>
    <div class="nav">
    <div class="top-bar">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <h2>Project 1</h2>
        </div>
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <nav class="pages">
            <a href="./user_welcome.php" class="<?= ($current_page == 'user_welcome.php') ? 'active-link' : '' ?>">HOME</a>
            <a href="./user_search.php" class="<?= ($current_page == 'user_search.php') ? 'active-link' : '' ?>">SEARCH</a>
            <a href="./user_profile.php" class="<?= ($current_page == 'user_profile.php') ? 'active-link' : '' ?>">PROFILE</a>
            <a href="./user_contact.php" class="<?= ($current_page == 'user_contact.php') ? 'active-link' : '' ?>">CONTACT</a>
            <a href="./user_about.php" class="<?= ($current_page == 'user_about.php') ? 'active-link' : '' ?>">ABOUT</a>
            <a href="./user_logout.php">LOGOUT</a>
        </nav>
    </div>
</header>
<main>
    <div class="main">
        <h1>User Profile</h1>
        <br>
        <hr><br>
        <div class="profile-container">
            <h2>User Profile</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['emp_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Designation:</strong> <?php echo htmlspecialchars($user['designation']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status']); ?></p>
            <a href="./user_profile_edit.php">Edit Profile</a>
        </div>
    </div>
</main>
<footer>&copy; <?php echo date('Y'); ?> All Rights Reserved.</footer>
<script>
    function toggleMenu() {
        document.querySelector(".nav").classList.toggle("active");
    }
</script>
</body>
</html>
