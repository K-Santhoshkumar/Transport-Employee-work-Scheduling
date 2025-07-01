<?php
include('./user_middleware.php');
middleware();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .about-section {
            margin-bottom: 3rem;
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .about-card {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .about-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }
        
        .about-card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        
        .about-card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .about-card-content {
            padding: 1.5rem;
        }
        
        .about-card-content p {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        .stats-section {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 3rem 2rem;
            border-radius: var(--border-radius-xl);
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .team-section {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .team-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
            text-align: center;
        }
        
        .team-header h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .team-header p {
            color: var(--text-secondary);
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        .team-member {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: var(--transition);
        }
        
        .team-member:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
            border-color: var(--primary-color);
        }
        
        .member-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
            font-weight: 600;
        }
        
        .member-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .member-role {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }
        
        .member-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .values-section {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 2rem;
            margin-bottom: 3rem;
        }
        
        .values-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .values-header h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .value-item {
            text-align: center;
            padding: 1rem;
        }
        
        .value-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }
        
        .value-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .value-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
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
                <h1>About Our System</h1>
                <p>Learn more about our mission, vision, and the team behind this comprehensive transport management platform.</p>
            </div>
        </section>
        
        <section class="about-section">
            <div class="about-grid">
                <div class="about-card">
                    <div class="about-card-header">
                        <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                    </div>
                    <div class="about-card-content">
                        <p>To revolutionize transport management through innovative technology solutions that streamline operations, enhance efficiency, and improve the overall experience for both employees and passengers. We strive to create a seamless digital ecosystem that connects all aspects of public transportation.</p>
                    </div>
                </div>
                
                <div class="about-card">
                    <div class="about-card-header">
                        <h3><i class="fas fa-eye"></i> Our Vision</h3>
                    </div>
                    <div class="about-card-content">
                        <p>To become the leading platform for transport management systems globally, setting new standards for digital transformation in public transportation. We envision a future where technology empowers transport departments to deliver exceptional service while maintaining operational excellence.</p>
                    </div>
                </div>
                
                <div class="about-card">
                    <div class="about-card-header">
                        <h3><i class="fas fa-heart"></i> Our Values</h3>
                    </div>
                    <div class="about-card-content">
                        <p>We are committed to innovation, reliability, and user-centric design. Our core values include transparency, continuous improvement, and fostering collaborative relationships with our users. We believe in creating solutions that make a real difference in people's daily lives.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="stats-section">
            <h2>Our Impact in Numbers</h2>
            <p>See how our platform is making a difference in transport management</p>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Active Employees</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Bus Routes</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <span class="stat-label">Daily Schedules</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">99.9%</span>
                    <span class="stat-label">System Uptime</span>
                </div>
            </div>
        </section>
        
        <section class="values-section">
            <div class="values-header">
                <h3>What Drives Us</h3>
                <p>The core principles that guide our development and service delivery</p>
            </div>
            <div class="values-grid">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="value-title">Innovation</h4>
                    <p class="value-description">Constantly pushing boundaries to deliver cutting-edge solutions</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="value-title">Reliability</h4>
                    <p class="value-description">Building robust systems you can depend on 24/7</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="value-title">User-Centric</h4>
                    <p class="value-description">Designing with our users' needs at the forefront</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h4 class="value-title">Sustainability</h4>
                    <p class="value-description">Promoting eco-friendly transportation solutions</p>
                </div>
            </div>
        </section>
        
        <section class="team-section">
            <div class="team-header">
                <h3>Meet Our Development Team</h3>
                <p>The talented individuals behind this innovative platform</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-avatar">JS</div>
                    <h4 class="member-name">John Smith</h4>
                    <p class="member-role">Lead Developer</p>
                    <p class="member-description">Full-stack developer with 8+ years of experience in building scalable web applications and transport management systems.</p>
                </div>
                
                <div class="team-member">
                    <div class="member-avatar">MJ</div>
                    <h4 class="member-name">Maria Johnson</h4>
                    <p class="member-role">UI/UX Designer</p>
                    <p class="member-description">Creative designer focused on creating intuitive user experiences and modern interface designs for complex systems.</p>
                </div>
                
                <div class="team-member">
                    <div class="member-avatar">DW</div>
                    <h4 class="member-name">David Wilson</h4>
                    <p class="member-role">Backend Engineer</p>
                    <p class="member-description">Database specialist and API architect ensuring robust data management and system performance optimization.</p>
                </div>
                
                <div class="team-member">
                    <div class="member-avatar">SB</div>
                    <h4 class="member-name">Sarah Brown</h4>
                    <p class="member-role">Project Manager</p>
                    <p class="member-description">Experienced project coordinator ensuring timely delivery and seamless communication between all stakeholders.</p>
                </div>
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
            const navLinks = document.querySelector('.nav-links');
            
            if (!nav.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });

        // Animate numbers on scroll
        function animateNumbers() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(stat => {
                const target = parseInt(stat.textContent);
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    if (stat.textContent.includes('%')) {
                        stat.textContent = Math.floor(current) + '%';
                    } else if (stat.textContent.includes('+')) {
                        stat.textContent = Math.floor(current) + '+';
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 20);
            });
        }

        // Trigger animation when stats section is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateNumbers();
                    observer.unobserve(entry.target);
                }
            });
        });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>
</html>