<nav class="nav">
    <div class="nav-container">
        <div class="logo">
            <i class="fas fa-bus"></i> Project 1
        </div>
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="nav-links">
            <li><a href="./user_welcome.php" class="<?= ($current_page == 'user_welcome.php') ? 'active' : '' ?>">HOME</a></li>
            <li><a href="./user_search.php" class="<?= ($current_page == 'user_search.php') ? 'active' : '' ?>">SEARCH</a></li>
            <li><a href="./user_profile.php" class="<?= ($current_page == 'user_profile.php') ? 'active' : '' ?>">PROFILE</a></li>
            <li><a href="./user_contact.php" class="<?= ($current_page == 'user_contact.php') ? 'active' : '' ?>">CONTACT</a></li>
            <li><a href="./user_about.php" class="<?= ($current_page == 'user_about.php') ? 'active' : '' ?>">ABOUT</a></li>
            <li><a href="./user_logout.php" class="logout">LOGOUT</a></li>
        </ul>
    </div>
</nav> 