<?php
$host = "localhost";
$username = "root";
$password = "";
$basename = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $basename);

if ($conn->connect_error) {
    die("There was a problem with the connection");
}

// Retrieve contact ID
$contact_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$contact_id) {
    die("Invalid contact ID.");
}

// Fetch contact details
$sql_contact = "SELECT * FROM contact WHERE id = $contact_id";
$result_contact = $conn->query($sql_contact);

if (!$result_contact || $result_contact->num_rows == 0) {
    die("Contact not found.");
}

$contact = $result_contact->fetch_assoc();

// Handle assign and type change actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $user_id = 1; // Simulated logged-in user ID

    if ($action === 'assign') {
        $sql_assign = "UPDATE contact SET assigned_to = $user_id, updated_at = NOW() WHERE id = $contact_id";
        $conn->query($sql_assign);
    } elseif ($action === 'change_type') {
        $new_type = $contact['type'] === 'Sales Lead' ? 'Support' : 'Sales Lead';
        $sql_type = "UPDATE contact SET type = '$new_type', updated_at = NOW() WHERE id = $contact_id";
        $conn->query($sql_type);
    } elseif ($action === 'add_note') {
        $comment = $conn->real_escape_string($_POST['comment']);
        $sql_note = "INSERT INTO notes (contact_id, comment, created_by, created_at) VALUES ($contact_id, '$comment', $user_id, NOW())";
        $conn->query($sql_note);
    }
    header("Location: view_contact.php?id=$contact_id");
    exit;
}

// Fetch notes
$sql_notes = "SELECT n.comment, n.created_at, u.firstname, u.lastname 
              FROM notes n 
              JOIN users u ON n.created_by = u.id 
              WHERE n.contact_id = $contact_id 
              ORDER BY n.created_at DESC";
$result_notes = $conn->query($sql_notes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Details</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
.subheading {
    margin-top: 5px;
    font-size: 0.9em;
    color: #666;
}

.subheading p {
    margin: 2px 0;
}

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            color: #333;
            padding-top: 60px; /* Adjust for header */
        }

        /* Sidebar Navigation */
        .sidebar {
    width: 250px;
    background-color: #ffffff;
    color: white;
    height: 100vh;
    position: fixed;
    top: 60px; /* Adjust for the header */
    left: 0;
    padding: 20px 0; /* Add padding for proper spacing */
    display: flex;
    flex-direction: column;
}

        .sidebar a {
    color: #000000;
    text-decoration: none;
    font-weight: bold;
    padding: 10px 15px;
    border-radius: 5px;
    margin: 5px 20px; /* Spaced margins for better look */
    display: block;
    transition: background-color 0.3s;
}

        .sidebar a:hover {
            background-color: #4f46e5;
            color: white;
        }

        /* Header Section */
        header {
    background-color: #343a40;
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10;
}

        .header-title {
            font-size: 1.5em;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            margin-top: 60px; /* Adjust for header */
            background-color: #fff;
        }

        .contact-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .contact-header h1 {
            margin: 0;
            color: #1f2937;
            font-size: 1.8em;
        }

        .contact-header-buttons {
            display: flex;
            gap: 10px;
        }

        .contact-info-box {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    margin-top: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.contact-info-box p {
    margin: 10px 0;
    font-size: 1em;
}

        p {
            margin-bottom: 10px;
            font-size: 1em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #6366f1;
        }

        button[type="submit"][name="action"][value="assign"] {
            background-color: #28a745;
        }

        button[type="submit"][name="action"][value="assign"]:hover {
            background-color: #218838;
        }

        button[type="submit"][name="action"][value="change_type"] {
            background-color: #ffc107;
            color: #1f2937;
        }

        button[type="submit"][name="action"][value="change_type"]:hover {
            background-color: #e0a800;
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
        <div class="header-title">Dolphin CRM</div>
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
    <div class="main-content">
        <!-- Page Title -->
        <h2>Dolphin CRM - Contact Details</h2>

        <!-- Contact Header -->
        <div class="contact-header">
    <h1><?php echo htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']); ?></h1>
    <div class="subheading">
        <p><strong>Created On:</strong> <?php echo htmlspecialchars($contact['created_at']); ?></p>
        <p><strong>Updated On:</strong> <?php echo htmlspecialchars($contact['updated_at']); ?></p>
    </div>
    <div class="contact-header-buttons">
        <form method="POST" style="display: inline;">
            <button type="submit" name="action" value="assign">Assign to Me</button>
        </form>
        <form method="POST" style="display: inline;">
            <button type="submit" name="action" value="change_type">
                Switch to <?php echo $contact['type'] === 'Sales Lead' ? 'Support' : 'Sales Lead'; ?>
            </button>
        </form>
    </div>
</div>

        <!-- Contact Info -->
        <div class="contact-info-box">
    <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?></p>
    <p><strong>Company:</strong> <?php echo htmlspecialchars($contact['company']); ?></p>
    <p><strong>Telephone:</strong> <?php echo htmlspecialchars($contact['telephone']); ?></p>
    <p><strong>Assigned To:</strong> 
        <?php echo $contact['assigned_to'] ? "User ID: " . htmlspecialchars($contact['assigned_to']) : "Unassigned"; ?>
    </p>
</div>

        <!-- Notes -->
        <h2>Notes</h2>
        <?php if ($result_notes && $result_notes->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Comment</th>
                    <th>Added By</th>
                    <th>Date</th>
                </tr>
                <?php while ($note = $result_notes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($note['comment']); ?></td>
                        <td><?php echo htmlspecialchars($note['firstname'] . ' ' . $note['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($note['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No notes available.</p>
        <?php endif; ?>

        <!-- Add Note -->
        <form method="POST">
            <textarea name="comment" placeholder="Add a new note..." required></textarea>
            <button type="submit" name="action" value="add_note">Add Note</button>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>