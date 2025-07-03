<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --accent-color: #06b6d4;
            --success-color: #059669;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-light: #94a3b8;
            --background: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-primary);
        }

        .header {
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 1rem 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
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
            gap: 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-link:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .nav-link.admin {
            background: var(--success-color);
        }

        .nav-link.admin:hover {
            background: #047857;
        }

        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
        }

        .hero {
            text-align: center;
            color: white;
            max-width: 800px;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 3rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: var(--surface);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: var(--border-radius-lg);
            font-weight: 600;
            font-size: 1.125rem;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .cta-button.admin {
            background: var(--success-color);
            color: white;
        }

        .cta-button.admin:hover {
            background: #047857;
        }

        .features {
            background: var(--surface);
            padding: 4rem 2rem;
        }

        .features-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: var(--text-primary);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
        }

        .feature-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .footer {
            background: var(--text-primary);
            color: white;
            text-align: center;
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 0 1rem;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .main {
                padding: 2rem 1rem;
            }

            .features {
                padding: 2rem 1rem;
            }

            .features h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-bus"></i>
                <span>Transport Manager</span>
            </div>
            <div class="nav-links">
                <a href="user_module/user_login.html" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    User Login
                </a>
                <a href="admin_module/admin_login.html" class="nav-link admin">
                    <i class="fas fa-shield-alt"></i>
                    Admin Portal
                </a>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="hero">
            <h1>Transport Management System</h1>
            <p>Streamline your transport operations with our comprehensive management platform. Manage employees, schedules, routes, and more with ease.</p>
            <div class="cta-buttons">
                <a href="user_module/user_login.html" class="cta-button">
                    <i class="fas fa-user"></i>
                    Employee Portal
                </a>
                <a href="admin_module/admin_login.html" class="cta-button admin">
                    <i class="fas fa-cog"></i>
                    Admin Dashboard
                </a>
            </div>
        </div>
    </main>

    <section class="features">
        <div class="features-content">
            <h2>Powerful Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Employee Management</h3>
                    <p>Comprehensive employee database with detailed profiles, contact information, and role assignments.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Schedule Management</h3>
                    <p>Efficient scheduling system for duty assignments, shift management, and route planning.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Route Tracking</h3>
                    <p>Monitor bus routes, track vehicle assignments, and optimize transportation efficiency.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Analytics & Reports</h3>
                    <p>Detailed reporting and analytics to track performance, attendance, and operational metrics.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Responsive</h3>
                    <p>Access the system from any device with our fully responsive design and mobile-friendly interface.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Built with security in mind, featuring user authentication and data protection measures.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2025 Transport Management System. All rights reserved.</p>
    </footer>
</body>
</html>