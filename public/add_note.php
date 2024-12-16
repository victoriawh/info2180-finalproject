<?php
$host = "localhost";
$dbname = "contacts_db";
$username = "root";
$password ="";

try{
	$conn = new PDO("mysql:host=$host; dbname= $dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
	die("Database connection failed: ".$e->getMessage());
}

function sanitizeInput($data){
	return htmlspecialchars(stripslashes(trim($data)));
}

if(isset($_GET['contact_id'])){
	$contact_id = (int)$_GET['contact_id'];
	$stmt = $conn->prepare("Select, created_at FROM notes WHERE contact_id = :contact_id ORDER BY created_at DESC");
	$stmt->bindParam(':contact_id',$contact_id, PDO::PARAM_INT);
	$stmt->execute();
	$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if($_SERVER['REQUEST_METHOD'] ==='POST'){
	$contact_id = (int)$_POST['contact_id'];
	$note = sanitizeINput($_POST['note']);
	
	if(!empty($note)){
		$insertNote = $conn->prepare("INSERT INTO notes(contact_id,note,created_at) VALUES (:contact_id, :note, NOW())");
		$insertNote->bindParam(':contact_id',$contact_id,PDO::PARAM_INT);
		$insertNote->bindParam(':note', $note, PDO::PARAM_STR);
		$insertNote->execute();

 		$updateContact = $conn ->prepare("UPDATE contacts SET 	updated_at = NOW() WHERE id = :contact_id");
		$updateContract->bindParam(':contract_id',$contact_id, PDO::PARAM_INT);
		$updateContact->execute();
		
		header("Location:add_note.php?contact_id=$contact_id");
		exit;
	}else{
		echo "Note cannot be empty.";
	}
}
?>
<!DOCTYPE html lang= "en">
<head>
	<meta charset = "UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Add Note to Contact</title>
</head>
<body>
	<h2>Add Note to Contact</h2>
	<form action="add_note.php" method="POST'>
		<input type="hidden" name="contact_id" value="<?php echo htmlspecialchars(_GET['contact_id']??'');?>">
		<textarea name="note' rows="5" cols="40" placeholder="Enter your note here..." required></textarea><br>
		<button type="submit">Add Note</button>
	</form>

	<h3>Existing Notes</h3>
	<?php if(isset($notes)&&count($notes)>0): ?>
		<ul>
			<?php foreach ($notes as $note): ?>
				<li>
					<strong><?php echo htmlspecialchars($note['created_at']);?>:</strong>
					<?php echo specialchars($note['note']);?>
				</li>
			<?php endforeach;?>
		</ul>
	<?php else:?>
		<p>No notes available for this contact.</p>
	<?php endif;?>
</body>
</html>