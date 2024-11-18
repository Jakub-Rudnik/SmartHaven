<?php
require_once './Services/UsersService.php';
require_once './Lib/Database.php';

session_start();
if (isset($_SESSION['userID'])) {
    // Usuń wszystkie zmienne sesji i zakończ sesję
    $_SESSION = [];
    session_destroy();
}

$db = new DatabaseConnection();
$usersService = new UsersService($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $usersService->loginUser($username, $password);
    if ($result === 'Login successful!') {
        header('Location: index.php');
        exit();
    } else {
        echo $result;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
<a href="register.php">Rejestracja</a>
</body>
</html>
