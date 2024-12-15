<?php
$host = "localhost";
$username = "root";
$password= "";
$basename = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $basename);

if ($conn->connect_error){
    die("There was a problem with the connection");
}

$userID = 1;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

switch ($filter){
    case 'sales_leads':
        $sql = "SELECT * FROM contact WHERE type = 'Sales Lead'";
        break;
    case 'support':
        $sql = "SELECT * FROM contact WHERE type = 'Support'";
        break;
    case 'assigned_to':
        $sql = "SELECT * FROM contact WHERE assigned_to = $userID";
        break;
    default:
        $sql = "SELECT * FROM contact";
        break;
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UFT-8">
        <title>Dashboard</title>
        <style>
            body{
                font-family: Arial, sans-serif;
                padding: 0;
                margin: 0;
                background-color: #f8f9fa;
            }
            
            header{
                background-color: #343a40;
                color: #fff;
                padding: 10px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            header h1{
                margin: 0;
                font-size: 18px;
            }

            header a{
                border-radius: 5px;
                color: #fff;
                background-color: #007bff;
                padding: 8px 12px;
                text-decoration: none;
            }

            .sidebar{
                padding: 20px 0;
                color: #fff;
                width: 200px;
                height: 100vh;
                background-color: #f2f2f2;
                position: fixed;
            }

            .sidebar ul{
                list-style-type: none;
                padding: 0;
            }

            .sidebar ul li{
                margin: 20px 0;
                text-align: center;
            }

            .sidebar a{
                text-decoration: none;
                color: #000000;
            }

            .sidebar ul li a:hover{
                background-color: #ADD8E6;
                padding: 10px 0;
            }

            main{
                margin-left: 220px;
                padding: 20px;
            }

            .filter-links a{
                margin-right: 15px;
                text-decoration: none;
                color: #575757;
                font-weight: bold;
            }

            .filter-links a:hover{
                text-decoration: underline;

            }

            p{
                color: #000000;
                font-weight: bold;
            }

            table{
                border-collapse: collapse; 
                width: 100%;
                background-color: #fff;
                margin-top: 20px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            th, td{
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align:left;
            }

            th{
                background-color: #343a40 ;
                color: #fff;
            }

            .badge{
                color: #fff;
                font-weight: bold;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 12px;
            }

            .badge-sales{
                background-color: #ffc107;
            }

            .badge-support{
                background-color: #007bff;
            }

            a.view-link{
                text-decoration: none;
                color: #28a745;
                font-weight: bold;
            }

            a.view-link:hover{
                text-decoration: underline;
            }

            .logo-container{
                display: flex;
                align-items: center;
                width: 1.5%;
                height: auto;
            }

            .logo-container img.logo{
                width: 1.5%;
                height: auto;
                margin-right: 5PX;
            }

            .setter{
                margin: 30px;
                display: flex;
                align-items: center;
                width: 1.5%;
                height: auto;
            }

            <div class="add-btn">float: right; margin: 10px 0; padding: 8px 12px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px</div>
        </style>
    </head>
    <body>
        <header>
            <div class="logo-container">
                <img src="../assets/images/dolphin.jpg" alt="Dolphin Logo" style="width: 20px; height: auto;"> 
                <h1>Dolphin CRM</h1>
            </div>
            <a href="contact.php" class="add-btn"> + Add New Contact</a>
        </header>
        
        <div class="sidebar">
            <ul>
                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/home--v1.png" alt="home--v1" style="width: 30px; height: auto;"/>
                <li><a href="#">Home</a></li>
                </div>

                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/contacts.png" alt="contacts" style="width: 30px; height: auto;"/>
                <li><a href="#">New Contact</a></li>
                </div>

                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/conference-call--v1.png" alt="conference-call--v1" style="width: 30px; height: auto;"/>
                <li><a href="#">Users</a></li>
                </div>

                <hr>

                <div class="setter">
                <img width="50" height="50" src="https://img.icons8.com/ios/50/exit--v1.png" alt="exit--v1" style="width: 30px; height: auto;"/>
                <li><a href="#">Logout</a></li>
                </div>
            </ul>
        </div>
        
        <main>
        <h2>Dashboard</h2>
        <hr>
        <br>
        <div class="filter-links">
            <p> Filter By: </p>
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

            <?php if($result && $result->num_rows > 0): ?>
                <?php while ($row = $result-> fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['company']); ?></td>
                        <td><?php echo htmlspecialchars($row['type_of_contact']); ?></td>
                        <td><a href="view_contact.php?id=<?php echo $row['id']; ?>">View Details</a></td>
                    </td>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No contacts found.</td></tr>
            <?php endif; ?>
        </table>
        </main>

    </body>
    </html>
    <?php $conn->close(); ?>