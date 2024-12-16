<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "dolphin_crm");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, firstname, lastname, email, role, created_at FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Dolphin CRM</title>
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        header .logo-container {
            display: flex;
            align-items: center;
        }

        header img {
            margin-right: 10px;
        }

        header h1 {
            font-size: 1.5em;
            margin: 0;
        }

        
        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #ffffff;
            padding-top: 60px; 
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
            text-align: center;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .sidebar ul li a:hover {
            background-color: #2563eb;
        }

        .sidebar img {
            margin-right: 10px;
        }

        
        .main {
            margin-left: 220px; 
            margin-top: 60px; 
            padding: 20px;
            background-color: #ffffff;
            min-height: 100vh;
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .button-container {
            text-align: right;
            margin-bottom: 10px;
        }

        .add-user-btn {
            background-color: #6366f1;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 0.9em;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .add-user-btn:hover {
            background-color: #4f46e5;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
        }

        th, td {
            text-align: left;
            padding: 12px 15px;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }
	.setter{
                margin: 30px;
                display: flex;
                align-items: center;
                width: 1.5%;
                height: auto;
            }

    </style>
<script>
        // Automatically load data when the page loads
        window.addEventListener('DOMContentLoaded', function () {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'data.txt', true); // Replace 'data.txt' with your endpoint or file
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('content').innerHTML = xhr.responseText;
                } else {
                    console.error("Error fetching data.");
                    document.getElementById('content').innerHTML = "<p>Error loading data.</p>";
                }
            };
            xhr.send();
        });
    </script>

</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo-container">
            <img src="../assets/images/dolphin.jpg" alt="Dolphin Logo" style="width: 30px; height: auto;">
            <h1>Dolphin CRM</h1>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
            <ul>
                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/home--v1.png" alt="home--v1" style="width: 30px; height: auto;"/>
                <li><a href="dashboard.php">Home</a></li>
                </div>

                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/contacts.png" alt="contacts" style="width: 30px; height: auto;"/>
                <li><a href="contact.php">New Contact</a></li>
                </div>

                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/conference-call--v1.png" alt="conference-call--v1" style="width: 30px; height: auto;"/>
                <li><a href="user.php">Users</a></li>
                </div>

                <hr>

                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/exit--v1.png" alt="exit--v1" style="width: 30px; height: auto;"/>
                <li><a href="logout.php">Logout</a></li>
                </div>
            </ul>
        </div>


    <!-- Main Content -->
    <div class="main">
        <h2>Users</h2>
        <div class="button-container">
            <a href="add_user.php" class="add-user-btn">+ Add User</a>
        </div>

        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["firstname"] . " " . $row["lastname"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No users found.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>