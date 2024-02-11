<?php

use config\Config;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/services/SessionService.php';

$session = SessionService::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$category = null;

if ($id === false) {
    header('Location: indexCategory.php');
    exit;
} else {
    $config = Config::getInstance();
    $categoryService = new CategoryService($config->db);
    $category = $categoryService->findById($id);
    if ($category) {
        if ($categoryService->isCategoryUsed($id)) {
            echo "<script type='text/javascript'>
                alert('No se puede eliminar la categoría porque está siendo utilizada por un funko');
                window.location.href = 'indexCategory.php';
                </script>";
        } else {
            $categoryService->setDeleted($id, true);
            echo "<script type='text/javascript'>
                alert('Category deleted successfully');
                window.location.href = 'indexCategory.php';
                </script>";
        }
    }
}