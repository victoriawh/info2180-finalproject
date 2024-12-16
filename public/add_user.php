<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $conn = new mysqli('localhost', 'root', '', 'dolphin_crm');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $role);

    if ($stmt->execute()) {
        echo "New user added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Add User</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            color: #333;
            padding-top: 60px; /* Adjusted padding to account for the header height */
        }

        /* Sidebar Navigation */
        .sidebar {
            background-color: #ffffff;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 60px; /* Offset the sidebar to be below the header */
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar h1 {
            font-size: 1.5em;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar a {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #4f46e5;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            margin-top: 60px; /* Adjusted to account for the header height */
        }

        .add-user-container {
            background-color: #fff;
            margin: 30px auto;
            padding: 20px;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-user-container h1 {
            font-size: 1.8em;
            margin-bottom: 20px;
            text-align: center;
            color: #1f2937;
        }

        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        button {
            background-color: #4f46e5;
            color: #fff;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #6366f1;
        }

        a {
            display: block;
            margin-top: 15px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #3b82f6;
        }

        /* Header Styling */
        header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img.logo {
            width: 20px;
            height: auto;
            margin-right: 10px;
        }

        .logo-container h1 {
            margin: 0;
        }

        /* Footer Styling */
        footer {
            background-color: #1f2937;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
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
    <!-- Header Section -->
    <header>
        <div class="logo-container">
            <img src="../assets/images/dolphin.jpg" alt="Dolphin Logo" class="logo">
            <h1>Dolphin CRM</h1>
        </div>
    </header>

    <!-- Sidebar Navigation -->
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


    <!-- Main Content Area -->
    <div class="main-content">
        <div class="add-user-container">
            <h1>Add User</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <label for="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div>
                    <label for="lastname">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <small>Only admin users can add new users.</small>
                </div>

                <button type="submit">Add User</button>
            </form>
            <a href="user.php">Back to Users</a>
        </div>
    </div>
</body>
</html>