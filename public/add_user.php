<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role']!== 'admin') {
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
        die("Connection failed: ". $conn->connect_error);
    }

    $sql = "INSERT INTO users (firstname, lastname, email, password, role) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $firstname, $lastname, $email, $password, $role);

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

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dolphin CRM - Add User</title>
        <link rel="stylesheet" href="../assets/css/styles.css">
    </head>
    <body>
        <header class="banner">
            <img src="../assets/images/dolphin.jpg" alt="Dolphin Logo">
            <h1>Welcome to Dolphin CRM</h1>
        </header>
        <nav class="navbar">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="add-user-container">
            <h1>Add User</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
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
            <a href="users.php">Back to Users</a>
            </main>
            <footer class="footer">
                &copy; 2022 Dolphin CRM
            </footer>
            </body>
            </html>