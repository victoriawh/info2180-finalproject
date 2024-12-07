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

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Created At</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>". $row["id"]. "</td><td>". $row["firstname"]. "</td><td>". $row["lastname"]. "</td><td>". $row["email"]. "</td><td>". $row["role"]. "</td><td>". $row["created_at"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>

