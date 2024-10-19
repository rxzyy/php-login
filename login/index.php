<?php
require_once 'functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $message = registerUser($_POST['reg_username'], $_POST['reg_password']);
    }
    if (isset($_POST['login'])) {
        $message = authenticateUser($_POST['login_username'], $_POST['login_password']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration & Authentication</title>
    <style>
        .message {
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f0f0;
        }
        form {
            margin-bottom: 20px;
        }
        input {
            margin: 5px 0;
            padding: 5px;
        }
    </style>
</head>
<body>
    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <h2>Registrasi User Baru</h2>
    <form method="post">
        <input type="text" name="reg_username" placeholder="Username" required><br>
        <input type="password" name="reg_password" placeholder="Password" required><br>
        <input type="submit" name="register" value="Register">
    </form>

    <h2>Login</h2>
    <form method="post">
        <input type="text" name="login_username" placeholder="Username" required><br>
        <input type="password" name="login_password" placeholder="Password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>