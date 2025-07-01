<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>USER WELCOME</title>
</head>
<style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color:#ffdfb9;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            display: flex;
            flex-direction: column;
        }
        .nav {
            background: rgb(35, 178, 225);
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 0.4rem 0.6rem rgba(0, 0, 0, 0.2);
        }
        .top-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .top-bar h2 {
            color: black;
            font-size: 1.5rem;
            font-weight: bold;
            font-variant: small-caps;
        }
        .pages {
            display: flex;
            gap: 1rem;
        }
        .pages a {
            text-decoration: none;
            background-color: white;
            color: black;
            border: 1px solid rgb(35, 178, 225);
            padding: 0.5rem 1rem;
            font-weight: bold;
            border-radius: 0.5rem;
            transition: 0.3s ease-in-out;
        }
        .pages a:hover {
            background: linear-gradient(to right,rgb(100, 206, 103),rgb(64, 200, 234));
            color: white;
            transform: scale(1.05);
            box-shadow: 0 0.4rem 1rem rgba(0, 0, 0, 0.3);
        }
        .active-link {
            background: linear-gradient(to right,rgb(78, 239, 236),rgb(201, 76, 223));
            color: white;
            border: 2px solid white;
            transform: scale(1.1);
            font-weight: bold;
            padding: 0.5rem 1.2rem;
        }
        .menu-toggle {
            display: none;
            font-size: 1.8rem;
            color: #ffdfb9;
            cursor: pointer;
            background: none;
            border: none;
        }
        footer{
            background-color: rgb(35, 178, 225);
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }
        @media (max-width: 768px) {
            .pages {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background: rgb(35, 178, 225);
                padding: 1rem 0;
                text-align: center;
            }
            .pages a {
                display: block;
                padding: 1rem;
                margin: 0.5rem;
            }
            .menu-toggle {
                display: block;
            }
            .nav.active .pages {
                display: flex;
            }
            .about-text {
                width: 90%;
            }
        }
        .body{
            background-image: url("./assets/photo-1544620347-c4fd4a3d5957.jpg");
            width: 100%;
            height: auto;
            background-size: cover;
            background-position: center;
        }
        .main {
        text-align: center;
        padding: 1rem;
        flex-grow: 1;
        color: white;
        font-size: 1.5rem;
        }
        .main h1{
            font-size: 4rem;
        }
    .about-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        max-width: 90%;
        margin: auto;
        
    }
    .about-text {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 45%;
        text-align: center;
        color: black;
        margin: 0.8rem;
    }
     .about-text h2 {
        color: rgb(35, 178, 225);
        margin-bottom: 1rem;
    }
    .team-section {
        text-align: center;
        padding: 2rem;
        color: white;
    }
    #team-members {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
    .team-card {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 220px;
    }
    .team-card img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 1rem;
    }
</style>
<body class="body">
<header>
    <div class="nav">
    <div class="top-bar">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <h2>Project 1</h2>
        </div>
        <?php
            $current_page = basename($_SERVER['PHP_SELF']); // Get the current file name
            ?>
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
    <h1>About Us</h1>
    <br>
    <hr><br>
    <p>Learn more about our mission, vision, and the team behind this project.</p>
    <section class="about-container">
        <div class="about-text">
            <h2>Our Mission</h2>
            <p>We aim to simplify work scheduling for transport department employees.</p>
        </div>
        <div class="about-text">
            <h2>Our Vision</h2>
            <p>To create a digital platform that enhances workforce management.</p>
        </div>
    </section>
    <br>
    <hr>
    <br>
    <section class="team-section">
        <h2>Meet Our Team</h2>
        <div id="team-members"></div>
    </section>
</main>
<footer>&copy 2025 All Rights Reserved.</footer>
<script>
    function toggleMenu() {
        document.querySelector(".nav").classList.toggle("active");
    }
</script>
</body>
</html>
