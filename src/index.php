<?php

use config\Config;
use services\FunkoService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkoService.php';

$session = SessionService::getInstance();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
    <?php require_once 'header.php'; ?>
    <?php
    $config = Config::getInstance();
    ?>

    <form action="index.php" class="mb-3 mx-auto" method="get" style="width: 50%; margin-top: 20px;">
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Name">
        </div>
    </form>

    <table class="table mx-auto" style="width: 80%;">
        <thead>
        <tr class="table-primary">
            <th class="text-center">Name</th>
            <th class="text-center">Price</th>
            <th class="text-center">Image</th>
            <th class="text-center">Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $searchTerm = $_GET['search'] ?? null;
        $funkosService = new FunkoService($config->db);
        $funkos = $funkosService->findAllWithCategoryName($searchTerm);
        ?>
        <?php foreach ($funkos as $funko): ?>
            <tr class="table-secondary">
                <td class="text-center"><?php echo htmlspecialchars($funko->name); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($funko->price); ?></td>
                <td class="text-center">
                    <img src="<?php echo htmlspecialchars($funko->image); ?>" alt="Imagen del funko" height="90"
                         width="90">
                </td>
                <td class="text-center">
                    <a class="btn btn-primary btn-sm"
                       href="details.php?id=<?php echo $funko->id; ?>">Detalles</a>
                    <?php if ($session->isAdmin()): ?>
                        <a class="btn btn-secondary btn-sm"
                           href="update.php?id=<?php echo $funko->id; ?>">Editar</a>
                    <?php endif; ?>

                    <a class="btn btn-info btn-sm"
                       href="update-image.php?id=<?php echo $funko->id; ?>">Imagen</a>

                    <?php if ($session->isAdmin()): ?>
                        <a class="btn btn-danger btn-sm"
                           href="delete.php?id=<?php echo $funko->id; ?>"
                           onclick="return confirm('¿Estás seguro de que deseas eliminar este funko?');">
                            Eliminar
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="mt-4 text-center" style="font-size: smaller;">
        <?php
        if ($session->isLoggedIn()) {
            echo "<span>Nº de visitas: {$session->getVisitCount()}</span>";
            echo "<span>, desde el último login en: {$session->getLastLoginDate()}</span>";
        }
        ?>
    </p>

</div>

<?php require_once 'footer.php'; ?>
<?php require_once 'scripts.php'; ?>
</body>
</html>