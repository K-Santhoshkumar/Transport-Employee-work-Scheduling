<?php 
ob_start();
if (headers_sent($file, $line)) {
    die("⚠ Headers already sent in $file on line $line");
}
include('./user_middleware.php');
middleware();
ob_end_flush(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #f1f5f9;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-light: #94a3b8;
            --background: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* Navigation */
        .nav {
            background: var(--surface);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 0.5rem;
            list-style: none;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        .nav-links a.logout {
            background: var(--danger-color);
            color: white;
        }

        .nav-links a.logout:hover {
            background: #dc2626;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-primary);
            cursor: pointer;
        }

        /* Main Content */
        main {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 4rem 2rem;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('./assets/photo-1544620347-c4fd4a3d5957.jpg') center/cover;
            opacity: 0.1;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: white;
            color: var(--primary-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Features Grid */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .feature-card {
            background: var(--surface);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--border);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-card h3 {
            color: var(--text-primary);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                padding: 1rem;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--surface);
                flex-direction: column;
                padding: 1rem;
                box-shadow: var(--shadow);
            }

            .nav-links.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            main {
                padding: 1rem;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="nav">
            <div class="nav-container">
                <div class="logo">
                    <i class="fas fa-bus"></i>
                    <span>Transport Manager</span>
                </div>
                
                <button class="menu-toggle" onclick="toggleMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <ul class="nav-links">
                    <li><a href="./user_welcome.php" class="<?= ($current_page == 'user_welcome.php') ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> Home
                    </a></li>
                    <li><a href="./user_search.php" class="<?= ($current_page == 'user_search.php') ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> Search
                    </a></li>
                    <li><a href="./user_profile.php" class="<?= ($current_page == 'user_profile.php') ? 'active' : '' ?>">
                        <i class="fas fa-user"></i> Profile
                    </a></li>
                    <li><a href="./user_contact.php" class="<?= ($current_page == 'user_contact.php') ? 'active' : '' ?>">
                        <i class="fas fa-envelope"></i> Contact
                    </a></li>
                    <li><a href="./user_about.php" class="<?= ($current_page == 'user_about.php') ? 'active' : '' ?>">
                        <i class="fas fa-info-circle"></i> About
                    </a></li>
                    <li><a href="./user_logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Your Dashboard</h1>
                <p>Manage your transport schedules, view duty allocations, and stay updated with the latest information from the transport department.</p>
                <a href="./user_search.php" class="cta-button">
                    <i class="fas fa-search"></i>
                    Start Searching
                </a>
            </div>
        </section>
        
        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Schedule Management</h3>
                <p>View and manage your work schedules with our intuitive calendar interface.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Employee Directory</h3>
                <p>Search and find information about drivers, conductors, and other transport staff.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-route"></i>
                </div>
                <h3>Route Information</h3>
                <p>Access detailed information about bus routes, schedules, and assignments.</p>
            </div>
        </section>
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
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            
            if (!nav.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>