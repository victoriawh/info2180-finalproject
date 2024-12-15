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
    // Sanitize and validate inputs
    $title = htmlspecialchars(trim($_POST['title']));
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $company = htmlspecialchars(trim($_POST['company']));
    $type = htmlspecialchars(trim($_POST['type']));
    $assigned_to = intval($_POST['assigned_to']);
    $created_by = $_SESSION['user_id']; // User who created the contact
    $current_time = date('Y-m-d H:i:s'); // Current timestamp

    // Validate required fields
    if (!empty($firstname) && !empty($lastname) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Insert into contacts table
        $sql = "INSERT INTO contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssisss", 
            $title, $firstname, $lastname, $email, $telephone, $company, $type, 
            $assigned_to, $created_by, $current_time, $current_time
        );

        // Execute and provide feedback
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
            <li><a href="contacts.php">Contacts</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main class="add-contact-container">
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
    </main>
    <footer class="footer">
        &copy; 2024 Dolphin CRM
    </footer>
</body>
</html>
