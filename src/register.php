<?php

use config\Config;
use services\SessionService;
use services\UserService;
use models\User;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/config/Config.php';

$session = SessionService::getInstance();
$config = Config::getInstance();

$error = '';
$usersService = new UserService($config->db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$username || !$password || !$name || !$email) {
        $error = 'All fields are required.';
    } else {
        try {
            $user = new User(null, $username, password_hash($password, PASSWORD_DEFAULT), $name, $email, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), ['USER']);
            $usersService->save($user);
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $error = 'Error creating user: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container mx-auto my-5" style="width: 40%;">
    <h1 class="text-center">Register</h1>
    <form action="register.php" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input class="form-control form-control-lg" id="name" name="name" required type="text">
            <label for="username">Username:</label>
            <input class="form-control form-control-lg" id="username" name="username" required type="text">
            <label for="password">Password:</label>
            <input class="form-control form-control-lg" id="password" name="password" required type="password">
            <label for="email">Email:</label>
            <input class="form-control form-control-lg" id="email" name="email" required type="email">
        </div>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="row">
            <div class="col">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Finish</button>
            </div>
            <div class="col">
                <a class="btn btn-secondary btn-lg btn-block" href="login.php">Login</a>
            </div>
        </div>
    </form>
</div>

<?php
require_once 'footer.php';
?>

<?php require_once 'scripts.php'; ?>
</body>
</html>