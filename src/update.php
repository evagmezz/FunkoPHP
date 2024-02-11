<?php

use config\Config;
use models\Funko;
use services\CategoryService;
use services\FunkoService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkoService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Funko.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para modificar un funko');
            window.location.href = 'index.php';
          </script>";
    exit;
}


$config = Config::getInstance();
$categoryService = new CategoryService($config->db);
$funkoService = new FunkoService($config->db);

$categorias = $categoryService->findAll();
$errores = [];
$funko = null;

$funkoId = -1;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $funkoId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!$funkoId) {
        echo "<script type='text/javascript'>
            alert('No se proporcionó un ID de funko');
            window.location.href = 'index.php';
          </script>";
        header('Location: index.php');
        exit;
    }

    try {
        $funko = $funkoService->findById($funkoId);
    } catch (Exception $e) {
        $error = 'System error. Please try later.';
    }

    if (!$funko) {
        header('Location: index.php');
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funkoId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $funko = $funkoService->findById($funkoId);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    $category = $categoryService->findByName($category);

    $name = trim($name);
    if (empty($name)) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }

    if (!isset($price) || $price === '') {
        $errores['price'] = 'El price es obligatorio.';
    } elseif ($price < 0) {
        $errores['price'] = 'El price no puede ser negativo.';
    }

    if (!isset($stock) || $stock === '') {
        $errores['stock'] = 'El stock es obligatorio.';
    } elseif ($stock < 0) {
        $errores['stock'] = 'El stock no puede ser negativo.';
    }

    if (empty($category)) {
        $errores['category'] = 'La categoría es obligatoria.';
    }

    if (count($errores) === 0) {
        $funko = new Funko();
        $funko->id = $funkoId;
        $funko->name = $name;
        $funko->price = $price;
        $funko->stock = $stock;
        $funko->category_name = $category->name;

        try {
            $funkoService->update($funko);
            echo "<script type='text/javascript'>
                alert('Funko updated successfully');
                window.location.href = 'index.php';
                </script>";
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
    <title>Actualizar Funko</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
    <?php require_once 'header.php'; ?>
    <div class="row">
        <div class="col-md-6 offset-md-3 mt-5">
            <h1 class="text-center">Actualizar Funko</h1>

            <form action="update.php" method="post">

                <input type="hidden" name="id" value="<?php echo $funkoId; ?>">

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input class="form-control" id="name" name="name" type="text" required
                           value="<?php echo htmlspecialchars($funko->name); ?>">
                    <?php if (isset($errores['name'])): ?>
                        <small class="text-danger"><?php echo $errores['name']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input class="form-control" id="stock" name="stock" type="number" step="0.01" required
                           value="<?php echo htmlspecialchars($funko->stock); ?>">
                    <?php if (isset($errores['stock'])): ?>
                        <small class="text-danger"><?php echo $errores['stock']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input class="form-control" id="price" name="price" type="number" step="0.01" required
                           value="<?php echo htmlspecialchars($funko->price); ?>">
                    <?php if (isset($errores['price'])): ?>
                        <small class="text-danger"><?php echo $errores['price']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select class="form-control" id="category" name="category">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat->name); ?>" <?php if ($cat->name == $funko->category_name) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['category'])): ?>
                        <small class="text-danger"><?php echo $errores['category']; ?></small>
                    <?php endif; ?>
                </div>

                <button class="btn btn-primary" type="submit">Actualizar</button>
                <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
            </form>
        </div>

        <?php require_once 'footer.php'; ?>

        <?php require_once 'scripts.php'; ?>
</body>
</html>
