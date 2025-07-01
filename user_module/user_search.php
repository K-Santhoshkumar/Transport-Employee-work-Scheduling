<?php
include('./user_middleware.php');
middleware();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search Employees - Transport Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <style>
        .search-section {
            background: var(--surface);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border: 1px solid var(--border);
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .search-input-group {
            flex: 1;
            min-width: 250px;
        }
        
        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            transition: var(--transition);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .btn-secondary {
            background: var(--surface);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--surface-hover);
            color: var(--text-primary);
        }
        
        .results-section {
            background: var(--surface);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .results-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface-hover);
        }
        
        .results-header h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .results-count {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        .employee-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            transition: var(--transition);
        }
        
        .employee-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
            border-color: var(--primary-color);
        }
        
        .employee-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .employee-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
        }
        
        .employee-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .employee-info .designation {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .employee-details {
            display: grid;
            gap: 0.5rem;
        }
        
        .detail-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .detail-row i {
            width: 16px;
            color: var(--text-light);
        }
        
        .detail-row .label {
            color: var(--text-secondary);
            font-weight: 500;
            min-width: 80px;
        }
        
        .detail-row .value {
            color: var(--text-primary);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
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
        
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        .no-results h3 {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .no-results p {
            color: var(--text-light);
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-buttons {
                justify-content: center;
            }
            
            .employee-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
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
                <h1>Search Employees</h1>
                <p>Find information about drivers, conductors, and other transport staff members.</p>
            </div>
        </section>
        
        <section class="search-section">
            <form method="post" class="search-form">
                <div class="search-input-group">
                    <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Search Employee</label>
                    <input type="text" id="search" name="search" class="search-input" placeholder="Enter name, email, phone, batch number, bus number, or date (DD-MM-YYYY)" />
                </div>
                <div class="search-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="clearResults()">
                        <i class="fas fa-times"></i>
                        Clear
                    </button>
                </div>
            </form>
        </section>
        
        <section class="results-section" id="searchResults" style="display: none;">
            <div class="results-header">
                <h3><i class="fas fa-users"></i> Search Results</h3>
                <div class="results-count" id="resultsCount"></div>
            </div>
            <div id="resultsContent"></div>
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

        function clearResults() {
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('resultsContent').innerHTML = '';
            document.querySelector('input[name="search"]').value = '';
        }

        function getInitials(name) {
            return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0, 2);
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

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "duty";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<script>alert('Database connection failed: " . $conn->connect_error . "');</script>");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['search'])) {
        $search = trim($_POST['search']);
        $searchLike = "%" . $search . "%";

        // Convert date from DD-MM-YYYY to YYYY-MM-DD if applicable
        if (preg_match("/^(\d{2})-(\d{2})-(\d{4})$/", $search, $matches)) {
            $search = $matches[3] . "-" . $matches[2] . "-" . $matches[1];
            $searchLike = "%" . $search . "%";
        }

        $sql = "SELECT * FROM employee WHERE 
                name LIKE ? OR 
                email LIKE ? OR 
                phone LIKE ? OR 
                batch_no LIKE ? OR 
                bus_number LIKE ? OR 
                working_date LIKE ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $searchLike, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike);
        
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<script>document.getElementById('searchResults').style.display = 'block';</script>";
        
        if ($result->num_rows > 0) {
            echo "<script>document.getElementById('resultsCount').textContent = 'Found " . $result->num_rows . " employee(s)';</script>";
            echo "<script>document.getElementById('resultsContent').innerHTML = `<div class='employee-grid'>";
            
            while ($row = $result->fetch_assoc()) {
                $initials = strtoupper(substr($row['name'], 0, 2));
                $statusClass = strtolower($row['status']) === 'active' ? 'status-active' : 'status-inactive';
                
                echo "<div class='employee-card'>
                        <div class='employee-header'>
                            <div class='employee-avatar'>{$initials}</div>
                            <div class='employee-info'>
                                <h4>" . htmlspecialchars($row['name']) . "</h4>
                                <div class='designation'>" . htmlspecialchars($row['designation']) . "</div>
                            </div>
                        </div>
                        <div class='employee-details'>
                            <div class='detail-row'>
                                <i class='fas fa-envelope'></i>
                                <span class='label'>Email:</span>
                                <span class='value'>" . htmlspecialchars($row['email']) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-phone'></i>
                                <span class='label'>Phone:</span>
                                <span class='value'>" . htmlspecialchars($row['phone']) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-building'></i>
                                <span class='label'>Dept:</span>
                                <span class='value'>" . htmlspecialchars($row['department']) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-id-badge'></i>
                                <span class='label'>Batch:</span>
                                <span class='value'>" . htmlspecialchars($row['batch_no']) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-bus'></i>
                                <span class='label'>Bus:</span>
                                <span class='value'>" . htmlspecialchars($row['bus_number']) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-clock'></i>
                                <span class='label'>Shift:</span>
                                <span class='value'>" . htmlspecialchars($row['shift_time']) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-calendar'></i>
                                <span class='label'>Start:</span>
                                <span class='value'>" . date('M d, Y', strtotime($row['working_date'])) . "</span>
                            </div>
                            <div class='detail-row'>
                                <i class='fas fa-info-circle'></i>
                                <span class='label'>Status:</span>
                                <span class='status-badge {$statusClass}'>" . htmlspecialchars($row['status']) . "</span>
                            </div>
                        </div>
                    </div>";
            }
            
            echo "</div>`;</script>";
        } else {
            echo "<script>
                document.getElementById('resultsCount').textContent = 'No results found';
                document.getElementById('resultsContent').innerHTML = `
                    <div class='no-results'>
                        <i class='fas fa-search'></i>
                        <h3>No employees found</h3>
                        <p>Try adjusting your search terms or check the spelling.</p>
                    </div>
                `;
            </script>";
        }
        $stmt->close();
    }
    $conn->close();
    ?>
</body>
</html>