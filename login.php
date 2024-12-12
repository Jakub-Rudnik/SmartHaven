<?php
require_once './Lib/Database.php';
require_once './Services/UsersService.php';

session_start();

// Sprawdzenie, czy użytkownik jest już zalogowany
if (isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$db = new DatabaseConnection();
$usersService = new UsersService($db);

$message = '';

// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $message = $usersService->loginUserByEmail($email, $password);

    if ($message === 'Login successful!') {
        header('Location: index.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p>Nie masz konta? <a href="registration.php">Zarejestruj się tutaj</a></p>
</body>
</html>
