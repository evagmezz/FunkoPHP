<?php


use config\Config;
use services\FunkoService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkoService.php';
require_once __DIR__ . '/models/Funko.php';

$session = $sessionService = SessionService::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$funko = null;

if ($id === false) {
    header('Location: index.php');
    exit;
} else {
    $config = Config::getInstance();
    $funkosService = new FunkoService($config->db);
    $funko = $funkosService->findById($id);
    if ($funko === null) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Funko</title>
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
            <div class="row">
                <div class="col-sm-6">
                    <dl>
                        <dt>ID:</dt>
                        <dd><?php echo htmlspecialchars($funko->id); ?></dd>
                        <dt>Stock:</dt>
                        <dd><?php echo htmlspecialchars($funko->stock); ?>uds</dd>
                        <dt>Price:</dt>
                        <dd><?php echo htmlspecialchars($funko->price); ?>€</dd>
                        <dt>Category:</dt>
                        <dd><?php echo htmlspecialchars($funko->category_name); ?></dd>
                    </dl>
                </div>
                <div class="col-sm-6">
                    <img src="<?php echo htmlspecialchars($funko->image); ?>" alt="Imagen del funko" height="240" width="240">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a class="btn btn-primary" href="index.php">Volver</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<?php require_once 'scripts.php'; ?>
</body>
</html>