<?php
include('./user_middleware.php');
middleware();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .contact-form-section {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .contact-info-section {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .section-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        
        .section-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .section-content {
            padding: 1.5rem;
        }
        
        .contact-info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .contact-info-item:last-child {
            border-bottom: none;
        }
        
        .contact-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.125rem;
        }
        
        .contact-details h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .contact-details p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
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
        
        .office-hours {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }
        
        .office-hours h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .hours-list {
            display: grid;
            gap: 0.5rem;
        }
        
        .hours-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
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
                <h1>Contact Us</h1>
                <p>Get in touch with our transport management team. We're here to help you with any questions or concerns.</p>
            </div>
        </section>
        
        <div class="contact-container">
            <div class="contact-form-section">
                <div class="section-header">
                    <h3><i class="fas fa-paper-plane"></i> Send us a Message</h3>
                </div>
                <div class="section-content">
                    <?php
                    $success_message = '';
                    $error_message = '';
                    
                    // Database Connection
                    $host = "localhost";
                    $user = "root";
                    $pass = "";
                    $dbname = "duty";

                    $conn = new mysqli($host, $user, $pass, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        $error_message = "Connection failed. Please try again later.";
                    }

                    if ($_SERVER["REQUEST_METHOD"] == "POST" && !$conn->connect_error) {
                        // Sanitize input to prevent XSS
                        $name = htmlspecialchars(trim($_POST['name']));
                        $email = htmlspecialchars(trim($_POST['email']));
                        $message = htmlspecialchars(trim($_POST['message']));

                        // Server-side Validation
                        if (empty($name)) {
                            $error_message = "Please enter your name.";
                        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $error_message = "Please enter a valid email address.";
                        } elseif (empty($message)) {
                            $error_message = "Please enter your message.";
                        } else {
                            // Prepare and bind SQL statement
                            $stmt = $conn->prepare("INSERT INTO newsletter_subscription (name, email, message) VALUES (?, ?, ?)");
                            $stmt->bind_param("sss", $name, $email, $message);

                            if ($stmt->execute()) {
                                $success_message = "Message sent successfully! We'll get back to you soon.";
                            } else {
                                $error_message = "Error sending message. Please try again.";
                            }

                            $stmt->close();
                        }
                    }
                    if ($conn && !$conn->connect_error) {
                        $conn->close();
                    }
                    ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required placeholder="Enter your email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-textarea" rows="5" required placeholder="Enter your message here..."></textarea>
                        </div>
                        
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="contact-info-section">
                <div class="section-header">
                    <h3><i class="fas fa-info-circle"></i> Contact Information</h3>
                </div>
                <div class="section-content">
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Office Address</h4>
                            <p>Transport Department<br>City Administration Building<br>Main Street, City Center</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Phone Number</h4>
                            <p>+1 (555) 123-4567<br>+1 (555) 123-4568</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email Address</h4>
                            <p>info@transport.gov<br>support@transport.gov</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Website</h4>
                            <p>www.transport.gov<br>www.publictransport.gov</p>
                        </div>
                    </div>
                    
                    <div class="office-hours">
                        <h4><i class="fas fa-clock"></i> Office Hours</h4>
                        <div class="hours-list">
                            <div class="hours-item">
                                <span>Monday - Friday</span>
                                <span>8:00 AM - 6:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span>Saturday</span>
                                <span>9:00 AM - 2:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span>Sunday</span>
                                <span>Closed</span>
                            </div>
                            <div class="hours-item">
                                <span>Emergency</span>
                                <span>24/7 Available</span>
                            </div>
                        </div>
                    </div>
                </div>
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