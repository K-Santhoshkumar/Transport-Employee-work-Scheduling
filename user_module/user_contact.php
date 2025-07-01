<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>USER CONTACT</title>
    <style>
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
        .contact-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 45%; text-align: center; color: black; margin: 2rem auto; }
        .contact-container h2 { color: rgb(35, 178, 225); margin-bottom: 1rem; }
        .contact-container form { display: flex; flex-direction: column; }
        .contact-container label { font-weight: bold; margin: 0.5rem 0 0.2rem; color: #333; }
        .contact-container input, .contact-container textarea { width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
        .contact-container button { background: rgb(35, 178, 225); color: white; font-size: 1rem; font-weight: bold; padding: 1rem; border: none; border-radius: 5px; margin-top: 1rem; cursor: pointer; transition: 0.3s; }
        .contact-container button:hover { background: linear-gradient(to right,rgb(78, 239, 236),rgb(201, 76, 223)); transform: scale(1.05); box-shadow: 0 0.4rem 1rem rgba(0, 0, 0, 0.3); }
        .success-message { text-align: center; color: green; font-weight: bold; margin-bottom: 1rem; }
        .error-message { text-align: center; color: red; font-weight: bold; margin-bottom: 1rem; }
        @media (max-width: 768px) { .contact-container { width: 90%; } }
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
        <h1>Contact Us</h1>
        <br>
        <hr><br>
        <div class="contact-container">
            <h2>Contact Form</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required placeholder="Enter your name">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required placeholder="Enter your message"></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</main>
<footer>&copy; <?php echo date('Y'); ?> All Rights Reserved.</footer>
<script>
    function toggleMenu() {
        document.querySelector(".nav").classList.toggle("active");
    }
</script>
<?php
    // Database Connection
    $host = "localhost"; // Change if using a different host
    $user = "root";      // Database username
    $pass = "";          // Database password
    $dbname = "duty"; // Your database name

    $conn = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize input to prevent XSS
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $message = htmlspecialchars(trim($_POST['message']));

        // Server-side Validation
        if (empty($name)) {
            echo "<p class='error-message'>Please enter your name.</p>";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p class='error-message'>Please enter a valid email address.</p>";
        } elseif (empty($message)) {
            echo "<p class='error-message'>Please enter your message.</p>";
        } else {
            // Prepare and bind SQL statement
            $stmt = $conn->prepare("INSERT INTO newsletter_subscription (name, email, message) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $message);

            if ($stmt->execute()) {
                echo "<p class='success-message'>Message sent successfully!</p>";
            } else {
                echo "<p class='error-message'>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
    }
    $conn->close();
?>
</body>
</html>