<?php


use config\Config;
use services\SessionService;
use services\UserService;


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

    if (!$username || !$password) {
        $error = 'Invalid username or password.';
    } else {
        try {
            
            $user = $usersService->authenticate($username, $password);
            
            if ($user) {
                $isAdmin = in_array('ADMIN', $user->roles);
                $session->login($user->username, $isAdmin);
                header('Location: index.php');
                exit;
            } else {
                
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            
            echo "<script type='text/javascript'>
            alert('$error');
          </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container mx-auto my-5" style="width: 40%;">
    <h1 class="text-center">Login</h1>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input class="form-control form-control-lg" id="username" name="username" required type="username">
            <label for="password">Password:</label>
            <input class="form-control form-control-lg" id="password" name="password" required type="password">
        </div>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="row">
            <div class="col">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
            </div>
            <div class="col">
                <a class="btn btn-secondary btn-lg btn-block" href="register.php">Register</a>
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