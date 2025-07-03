<nav class="nav">
    <div class="nav-container">
        <div class="logo">
            <i class="fas fa-bus"></i> Transport Manager
        </div>
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="nav-links">
            <li><a href="./user_welcome.php" class="<?= ($current_page == 'user_welcome.php') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> <span>Home</span>
            </a></li>
            <li><a href="./user_dashboard.php" class="<?= ($current_page == 'user_dashboard.php') ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a></li>
            <li><a href="./user_schedule.php" class="<?= ($current_page == 'user_schedule.php') ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> <span>Schedule</span>
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