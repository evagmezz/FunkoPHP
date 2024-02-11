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
require_once __DIR__ . '/models/Funko.php';
require_once __DIR__ . '/services/CategoryService.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para crear un funko');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$config = Config::getInstance();
$categoryService = new CategoryService($config->db);
$funkoService = new FunkoService($config->db);

$categorias = $categoryService->findAll();

$errores = [];
$funko = new Funko();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
    $categoryName = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    $category = $categoryService->findByName($categoryName);

    if (trim($name) === '') {
        $errores['name'] = 'Name is required.';
    }

    if (!isset($price)) {
        $errores['price'] = 'Price is required.';
    } elseif ($price < 0) {
        $errores['price'] = 'Price cannot be negative.';
    }

    if (!isset($stock)) {
        $errores['stock'] = 'Stock is required.';
    } elseif ($stock < 0) {
        $errores['stock'] = 'Stock cannot be negative.';
    }

    if (!empty($category)) {
        $funko->category_id = $category->id;
    } else {
        $errores['category'] = 'Category not found.';
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $funko->image = $uploadFile;
        } else {
            $errores['image'] = 'No se pudo subir la imagen.';
        }
    } else {
        $errores['image'] = 'Imagen requerida.';
    }

    if (count($errores) === 0) {
        $funko->name = $name;
        $funko->price = $price;
        $funko->stock = $stock;
        $funko->created_at = date('Y-m-d H:i:s');
        $funko->updated_at = date('Y-m-d H:i:s');
        $funko->category_id = $category->id;
        $funko->is_deleted = false;

        try {
            $funkoService->save($funko);
            echo "<script type='text/javascript'>
            alert('Funko creado correctamente');
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
    <title>Create Funko</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container py-5 md-5">
    <?php require_once 'header.php'; ?>
    <div class="row">
        <div class="col-md-6 offset-md-3 mt-5">
            <h1 class="text-center">Crear Funko</h1>

            <form action="create.php" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input class="form-control" id="name" name="name" type="text" required>
                    <?php if (isset($errores['name'])): ?>
                        <small class="text-danger"><?php echo $errores['name']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input class="form-control" id="price" name="price" type="number" required
                           value="0">
                    <?php if (isset($errores['price'])): ?>
                        <small class="text-danger"><?php echo $errores['price']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input class="form-control" id="stock" name="stock" type="number" required
                           value="0">
                    <?php if (isset($errores['stock'])): ?>
                        <small class="text-danger"><?php echo $errores['stock']; ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="image">Image:</label>
                    <input accept="image/*" class="form-control-file" id="image" name="image" required type="file">
                    <small class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat->name; ?>"><?php echo $cat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['category'])): ?>
                        <small class="text-danger"><?php echo $errores['category']; ?></small>
                    <?php endif; ?>
                </div>

                <button class="btn btn-primary" type="submit">Crear</button>
                <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
            </form>
        </div>

        <?php require_once 'footer.php'; ?>

        <?php require_once 'scripts.php'; ?>
</body>
</html>
