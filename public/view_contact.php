<?php
session_start();

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$basename = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $basename);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id']; // Current logged-in user ID
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Dynamic SQL query based on filters
switch ($filter) {
    case 'sales_leads':
        $sql = "SELECT * FROM contacts WHERE type = 'Sales Lead'";
        break;
    case 'support':
        $sql = "SELECT * FROM contacts WHERE type = 'Support'";
        break;
    case 'assigned_to':
        $sql = "SELECT * FROM contacts WHERE assigned_to = $userID";
        break;
    default:
        $sql = "SELECT * FROM contacts";
        break;
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- External stylesheet reference -->
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="../assets/images/dolphin.jpg" alt="Dolphin Logo" style="width: 20px; height: auto;"> 
            <h1>Dolphin CRM</h1>
        </div>
        <a href="contact.php" class="add-btn">+ Add New Contact</a>
    </header>
    
    <div class="sidebar">
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">New Contact</a></li>
            <li><a href="#">Users</a></li>
            <hr>
            <li><a href="#">Logout</a></li>
        </ul>
    </div>
    
    <main>
        <h2>Dashboard</h2>
        <hr>
        <br>
        <div class="filter-links">
            <p>Filter By:</p>
            <a href="?filter=all">All Contacts</a>
            <a href="?filter=sales_leads">Sales Leads</a>
            <a href="?filter=support">Support</a>
            <a href="?filter=assigned_to">Assigned to me</a>
        </div>

        <table>
            <tr>
                <th>Title</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Type of Contact</th>
                <th>Details</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['company']); ?></td>
                        <td>
                            <?php if ($row['type'] === 'Sales Lead'): ?>
                                <span class="badge badge-sales">Sales Lead</span>
                            <?php elseif ($row['type'] === 'Support'): ?>
                                <span class="badge badge-support">Support</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="view-link" href="view_contact.php?id=<?php echo $row['id']; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No contacts found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </main>
</body>
</html>
<?php $conn->close(); ?>
