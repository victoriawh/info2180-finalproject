<?php
session_start();

// Check if user is logged in and has the admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'dolphin_crm');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users for "Assigned To" dropdown
$user_list = [];
$result = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) AS fullname FROM users");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_list[] = $row;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $company = htmlspecialchars(trim($_POST['company']));
    $type = htmlspecialchars(trim($_POST['type']));
    $assigned_to = intval($_POST['assigned_to']);
    $created_by = $_SESSION['user_id'];
    $current_time = date('Y-m-d H:i:s');

    if (!empty($firstname) && !empty($lastname) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "INSERT INTO contact (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssisss", 
            $title, $firstname, $lastname, $email, $telephone, $company, $type, 
            $assigned_to, $created_by, $current_time, $current_time
        );

        if ($stmt->execute()) {
            echo "<p>New contact added successfully!</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Please ensure all required fields are filled correctly.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Add Contact</title>
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

        .add-contact-container {
            background-color: #fff;
            margin: 30px auto;
            padding: 20px;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-contact-container h1 {
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
        input[type="tel"],
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
            background-color: #1f2937;  /* Same color as the sidebar */
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
    </style>
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
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="contacts.php">Contacts</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="add-contact-container">
            <h1>Add New Contact</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <label for="title">Title:</label>
                    <select id="title" name="title" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <div>
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div>
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="telephone">Telephone</label>
                    <input type="tel" id="telephone" name="telephone">
                </div>
                <div>
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company">
                </div>
                <div>
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="Sales Lead">Sales Lead</option>
                        <option value="Support">Support</option>
                    </select>
                </div>
                <div>
                    <label for="assigned_to">Assigned To:</label>
                    <select id="assigned_to" name="assigned_to" required>
                        <option value="" disabled selected>Select a user</option>
                        <?php foreach ($user_list as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['fullname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Add Contact</button>
            </form>
            <a href="contacts.php">Back to Contacts</a>
        </div>
    </div>
</body>
</html>