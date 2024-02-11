<?php


use config\Config;
use services\FunkoService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkoService.php';
require_once __DIR__ . '/models/Funko.php';

$session = SessionService::getInstance();


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$funko = null;

if ($id === false) {
    header('Location: index.php');
    exit;
} else {
    $config = Config::getInstance();
    $funkoService = new FunkoService($config->db);
    $funko = $funkoService->findById($id);
    if ($funko === null) {
        echo "<script type='text/javascript'>
                alert('No existe el funko');
                window.location.href = 'index.php';
                </script>";
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

    <div class="card mx-auto my-5 col-md-6">
        <div class="card-header text-center">
            <h1><?php echo htmlspecialchars($funko->name); ?></h1>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-2">Image:</dt>
                <dd class="col-sm-10"><img src="<?php echo htmlspecialchars($funko->image); ?>" alt="Imagen del funko"
                                           height="220" width="220">
                </dd>
            </dl>

            <form action="update_image_file.php" enctype="multipart/form-data" method="post">
                <?php if ($session->isAdmin()): ?>
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input accept="image/*" class="form-control-file" id="image" name="image" required type="file">
                        <small class="text-danger"></small>
                        <input name="id" value="<?php echo $id; ?>" type="hidden">
                    </div>

                    <button class="btn btn-primary" type="submit">Actualizar</button>
                <?php endif; ?>
                <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
            </form>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
    <?php require_once 'scripts.php'; ?>
</body>
</html>