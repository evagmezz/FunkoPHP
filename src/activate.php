<?php

use config\Config;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/SessionService.php';

$session = SessionService::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

if ($id === false) {
    header('Location: indexCategory.php');
    exit;
} else {
    $config = Config::getInstance();
    $categoryService = new CategoryService($config->db);
    $categoryService->setDeleted($id, false);
    echo "<script type='text/javascript'>
            alert('Category activated successfully');
            window.location.href = 'indexCategory.php';
            </script>";
}