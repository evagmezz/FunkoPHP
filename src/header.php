<?php require_once 'bootstrap.php'; ?>

<?php

use services\SessionService;

require_once __DIR__ . '/services/SessionService.php';
$session = SessionService::getInstance();
$username = $session->isLoggedIn() ? $session->getUserName() : 'Invitado';
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">
            <img alt="Logo" class="d-inline-block align-text-top" height="30" src="/images/loogo.png" width="30">
            Funkolandia
        </a>
        <button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"
                data-target="#navbarNav" data-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item mr-2">
                    <?php
                    if ($session->isLoggedIn()) {
                        echo '<a class="nav-link btn btn-outline-secondary" href="logout.php">Logout</a>';
                    } else {
                        echo '<a class="nav-link btn btn-secondary" href="login.php">Login</a>';
                    }
                    ?>
                </li>
                <li class="nav-item mr-2">
                    <?php if ($session->isAdmin()): ?>
                        <a class="nav-link btn btn-outline-secondary" href="create.php">New Funko</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item mr-2">
                    <?php if ($session->isAdmin()): ?>
                        <a class="nav-link btn btn-outline-secondary" href="indexCategory.php">Categories</a>
                    <?php endif; ?>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="navbar-text">
                        <?php echo htmlspecialchars($username); ?>
                    </span>
                </li>
            </ul>
        </div>
    </nav>
</header>