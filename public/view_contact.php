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
        // Assign contact to the user
        $sql_assign = "UPDATE contact SET assigned_to = $user_id, updated_at = NOW() WHERE id = $contact_id";
        $conn->query($sql_assign);
    } elseif ($action === 'change_type') {
        // Switch contact type between Sales Lead and Support
        $new_type = $contact['type'] === 'Sales Lead' ? 'Support' : 'Sales Lead';
        $sql_type = "UPDATE contact SET type = '$new_type', updated_at = NOW() WHERE id = $contact_id";
        $conn->query($sql_type);
    } elseif ($action === 'add_note') {
        // Add a new note
        $comment = $conn->real_escape_string($_POST['comment']);
        $sql_note = "INSERT INTO notes (contact_id, comment, created_by, created_at) VALUES ($contact_id, '$comment', $user_id, NOW())";
        $conn->query($sql_note);
    }

    // Refresh the page to reflect changes
    header("Location: view_contact.php?id=$contact_id");
    exit;
}

// Fetch notes for the contact
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
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        h1 { color: #333; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .details, .notes { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background-color: #343a40; color: #fff; text-align: left; }
        form { margin: 10px 0; }
        textarea { width: 100%; height: 80px; margin: 10px 0; }
        button { background-color: #007bff; color: #fff; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h1>Contact Details</h1>
    <div class="details">
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?></p>
        <p><strong>Company:</strong> <?php echo htmlspecialchars($contact['company']); ?></p>
        <p><strong>Telephone:</strong> <?php echo htmlspecialchars($contact['telephone']); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($contact['type']); ?></p>
        <p><strong>Assigned To:</strong> <?php echo $contact['assigned_to'] ? "User ID: " . $contact['assigned_to'] : "Not Assigned"; ?></p>
        <p><strong>Created At:</strong> <?php echo htmlspecialchars($contact['created_at']); ?></p>
        <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($contact['updated_at']); ?></p>
    </div>

    <form method="POST">
        <button type="submit" name="action" value="assign">Assign to Me</button>
        <button type="submit" name="action" value="change_type">
            Switch to <?php echo $contact['type'] === 'Sales Lead' ? 'Support' : 'Sales Lead'; ?>
        </button>
    </form>

    <h2>Notes</h2>
    <div class="notes">
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
    </div>

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
