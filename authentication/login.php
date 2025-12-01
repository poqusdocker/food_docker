<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../user/index.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/login.css">
    <title>Document</title>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="decorative-line"></div>
            <h1>Log in</h1>
            <p>Not a member yet? <a href="register.php">Register now</a></p>
        </div>
        <form method="POST" action="process_login.php">
            <div class="form-group">
                <input type="text" name="username" placeholder="Email or Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="submit-btn">Sign In</button>
        </form>
    </div>
</body>

</html>