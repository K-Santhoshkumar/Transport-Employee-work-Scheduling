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
                background-color: #ffdfb9;
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
            }
            .body{
            background-image: url("./assets/photo-1544620347-c4fd4a3d5957.jpg");
            width: 100%;
            height: auto;
            background-size: cover;
            background-position: center;
            }
            h2{
                text-align: center;
                font-size: 4rem;
                color: white;
            }
            hr {
                width: 100%; 
                height: .2rem; 
                background-color: white; 
            }
            .search-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                margin:auto;
            }
            .search-bar {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            .search-bar input {
                padding: 0.5rem;
                width: 300px;
                font-size: 1rem;
                border-radius: 5px;
                border: 1px solid black;
            }
            .btn {
                padding: 0.5rem 1rem;
                font-size: 1rem;
                font-weight: bold;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: 0.3s ease-in-out;
            }
            .btn-search {
                background: rgb(35, 178, 225);
                color: white;
            }
            .btn-clear {
                background: rgb(200, 50, 50);
                color: white;
            }
            .btn:hover {
                transform: scale(1.1);
                box-shadow: 0 0.4rem 0.6rem rgba(0, 0, 0, 0.2);
            }
            .table-container {
                margin: 20px auto;
                width: 90%;
                display: none;
                overflow-x: auto; 
            }
            table {
                width: 100%;
                border-collapse: collapse;
                background: white;
                text-align: left;
                table-layout: auto;
                word-wrap: break-word;
            }
            th, td {
                padding: 10px;
                border: 1px solid black;
                white-space: nowrap; /* Prevent text wrapping */
            }
            th {
                background: rgb(35, 178, 225);
                color: white;
            }
            .search-bar input {
                padding: 0.5rem;
                width: 300px;
                font-size: 1rem;
                border-radius: 5px;
                border: 1px solid black; 
            }
            @media (max-width: 768px) {
                th, td {
                    font-size: 0.9rem; /* Adjust font size for smaller screens */
                    padding: 8px;
                }
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
    <main ><h2>SEARCH USER</h2>
    <br>
    <hr>
    <br>
    <div class="search-container">
            <form method="post" class="search-bar">
            <input type="text" name="search" placeholder="Enter User details" />
            <button type="submit" class="btn btn-search">Search</button>
            <button type="button" class="btn btn-clear" onclick="clearResults()">Clear</button>
            </form>
        </div>
    <div class="table-container" id="searchResults">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "duty";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("<h1 style='color: red; text-align: center;'>Error: " . $conn->connect_error . "</h1>");
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['search'])) {
            $search = trim($_POST['search']);
            $searchLike = "%" . $search . "%";

            // Convert date from DD-MM-YYYY to YYYY-MM-DD if applicable
            if (preg_match("/^(\d{2})-(\d{2})-(\d{4})$/", $search, $matches)) {
                $search = $matches[3] . "-" . $matches[2] . "-" . $matches[1]; // Convert to YYYY-MM-DD
                $searchLike = "%" . $search . "%";
            }

            // Define the SQL query
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

            if ($result->num_rows > 0) {
                echo "<script>document.getElementById('searchResults').style.display = 'block';</script>";
                echo "<table>";
                echo "<tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Batch No</th>
                        <th>Bus No</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Shift</th>
                        <th>Working Date</th>
                        <th>Status</th>
                    </tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['ID']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['batch_no']}</td>
                            <td>{$row['bus_number']}</td>
                            <td>{$row['designation']}</td>
                            <td>{$row['department']}</td>
                            <td>{$row['shift_time']}</td>
                            <td>{$row['working_date']}</td>
                            <td>{$row['status']}</td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<h3 style='text-align: center; color: red;'>No results found</h3>";
            }
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>
    <!-- HTML Form -->
        
        </main>
        <footer>&copy 2025 All Rights Reserved.</footer>
        <script>
            function toggleMenu() {
                document.querySelector(".nav").classList.toggle("active");
            }
        function clearResults() {
            document.getElementById('searchResults').innerHTML = '';
        }
        document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.querySelector(".search-bar input");

        searchInput.addEventListener("focus", function () {
            this.placeholder = "";
        });

        searchInput.addEventListener("blur", function () {
            this.placeholder = "Enter User details";
        });

        function clearResults() {
            document.getElementById("searchResults").innerHTML = "";
        }
    });

        </script>
        </body>
    </html>