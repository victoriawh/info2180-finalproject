<link rel="stylesheet" href="../assets/css/styles.css">

<?php

require '../src/auth.php';

/*if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}*/

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInput = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $passwordInput = $_POST['password'];

    if ($emailInput && $passwordInput) {
        if (login($emailInput, $passwordInput)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errorMessage = "Incorrect email or password. Please try again.";
        }
    } else {
        $errorMessage = "Please ensure both email and password are entered correctly.";
    }
}


/*HTML*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM Login</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header class="banner">
        <img src="../assets/images/dolphin.jpg" alt="Dolphin Logo"> 
        <h1>Welcome to Dolphin CRM</h1>
    </header>

    <section class="login-container">
        <h1>Login</h1>
        <?php if (isset($errorMessage) && $errorMessage !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Log In</button>
        </form>
    </section>
</body>
</html>
