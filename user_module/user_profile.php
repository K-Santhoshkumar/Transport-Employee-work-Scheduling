<?php
include('./user_middleware.php');
middleware();
include('../admin_module/database_connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT employee.name AS emp_name, employee.email, employee.phone, 
                 employee.designation, employee.department, employee.status,
                 employee.batch_no, employee.bus_number, employee.shift_time,
                 employee.working_date, employee.route, employee.co_partner
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
    <title>My Profile - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .profile-sidebar {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 2rem;
            text-align: center;
            height: fit-content;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
            font-weight: 700;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .profile-designation {
            color: var(--text-secondary);
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        .profile-status {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            margin-bottom: 1.5rem;
        }
        
        .status-active {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .status-inactive {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
        
        .edit-profile-btn {
            display: inline-flex;
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
        
        .edit-profile-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .profile-details {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .details-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        
        .details-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .details-content {
            padding: 1.5rem;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .detail-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .detail-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.025em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .detail-value {
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 500;
            padding: 0.5rem 0;
        }
        
        .detail-value.empty {
            color: var(--text-light);
            font-style: italic;
        }
        
        .stats-section {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .stats-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        
        .stats-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0;
        }
        
        .stat-item {
            padding: 1.5rem;
            text-align: center;
            border-right: 1px solid var(--border);
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stat-item {
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            
            .stat-item:nth-child(odd) {
                border-right: 1px solid var(--border);
            }
            
            .stat-item:last-child,
            .stat-item:nth-last-child(2) {
                border-bottom: none;
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
                <h1>My Profile</h1>
                <p>View and manage your personal information and employment details.</p>
            </div>
        </section>
        
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['emp_name'], 0, 2)); ?>
                </div>
                <h2 class="profile-name"><?php echo htmlspecialchars($user['emp_name']); ?></h2>
                <p class="profile-designation"><?php echo htmlspecialchars($user['designation']); ?></p>
                <div class="profile-status <?php echo strtolower($user['status']) === 'active' ? 'status-active' : 'status-inactive'; ?>">
                    <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i>
                    <?php echo htmlspecialchars($user['status']); ?>
                </div>
                <a href="./user_profile_edit.php" class="edit-profile-btn">
                    <i class="fas fa-edit"></i>
                    Edit Profile
                </a>
            </div>
            
            <div class="profile-details">
                <div class="details-header">
                    <h3><i class="fas fa-info-circle"></i> Personal Information</h3>
                </div>
                <div class="details-content">
                    <div class="details-grid">
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-phone"></i>
                                Phone Number
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['phone']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-building"></i>
                                Department
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['department']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-id-badge"></i>
                                Batch Number
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['batch_no'] ?? 'Not assigned'); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-bus"></i>
                                Bus Number
                            </div>
                            <div class="detail-value <?php echo empty($user['bus_number']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($user['bus_number'] ?? 'Not assigned'); ?>
                            </div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-clock"></i>
                                Shift Time
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['shift_time']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-calendar"></i>
                                Start Date
                            </div>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($user['working_date'])); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-route"></i>
                                Route
                            </div>
                            <div class="detail-value <?php echo empty($user['route']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($user['route'] ?? 'Not assigned'); ?>
                            </div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="fas fa-user-friends"></i>
                                Co-Partner
                            </div>
                            <div class="detail-value <?php echo empty($user['co_partner']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($user['co_partner'] ?? 'Not assigned'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-section">
            <div class="stats-header">
                <h3><i class="fas fa-chart-bar"></i> Quick Stats</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?php echo date('Y') - date('Y', strtotime($user['working_date'])); ?></div>
                    <div class="stat-label">Years of Service</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo date('z') + 1; ?></div>
                    <div class="stat-label">Days This Year</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $user['status'] === 'Active' ? '100' : '0'; ?>%</div>
                    <div class="stat-label">Availability</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo !empty($user['bus_number']) ? '1' : '0'; ?></div>
                    <div class="stat-label">Assigned Buses</div>
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