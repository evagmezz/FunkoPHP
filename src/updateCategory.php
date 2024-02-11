<?php

use config\Config;
use models\Category;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Category.php';

$session = SessionService::getInstance();

$config = Config::getInstance();
$categoryService = new CategoryService($config->db);

$errores = [];
$category = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $categoryId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    $category = $categoryService->findById($categoryId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $category = $categoryService->findById($categoryId);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    if (empty($name)) {
        $errores['name'] = 'Name is required.';
    } elseif (strlen($name) < 1) {
        $errores['name'] = 'Name must be at least 1 characters.';
    } elseif (strlen($name) > 255) {
        $errores['name'] = 'Name cannot be longer than 255 characters.';
    }

    if (count($errores) === 0) {
        $category->name = $name;

        try {
            $categoryService->update($category);
            echo "<script type='text/javascript'>
            alert('Categoria actualizada correctamente');
                window.location.href = 'indexCategory.php';
            </script>";
        }
        catch (Exception $e) {
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
    <title>Update Category</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
    <?php require_once 'header.php'; ?>
    <h1>Actualizar Categoria</h1>
    <form action="updateCategory.php" method="post">
        <input type="hidden" name="id" value="<?= $category->id ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input id="name" name="name" type="text" class="form-control" value="<?= $category->name ?>">
            <?php if (isset($errores['name'])): ?>
                <div class="alert alert-danger"><?= $errores['name'] ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-primary" href="indexCategory.php">Volver</a>
    </form>
</div>
</body>
</html>