<?php
require_once './Services/UsersService.php';
require_once './Lib/Database.php';

$db = new DatabaseConnection();
$usersService = new UsersService($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $usersService->registerUser($username, $email, $password);
    
    if ($result === 'Registration successful!') {
        header('Location: login.php');
        exit();
    } else {
        $message = $result;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Register">
</form>
<a href="login.php">Logowanie.</a>
</body>
</html>
