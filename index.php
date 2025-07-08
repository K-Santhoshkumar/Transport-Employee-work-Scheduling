<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
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
                    <a href="user_module/login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        User Login
                    </a>
                    <a href="admin_module/login.php" class="nav-link admin">
                        <i class="fas fa-shield-alt"></i>
                        Admin Portal
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="hero">
            <h1>Transport Management System</h1>
            <p>Streamline your transport operations with our comprehensive management platform. Manage employees, schedules, routes, and more with ease.</p>
            <div class="cta-buttons">
                <a href="user_module/login.php" class="cta-button">
                    <i class="fas fa-user"></i>
                    Employee Portal
                </a>
                <a href="admin_module/login.php" class="cta-button admin">
                    <i class="fas fa-cog"></i>
                    Admin Dashboard
                </a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2025 Transport Management System. All rights reserved.</p>
    </footer>
</body>
</html>