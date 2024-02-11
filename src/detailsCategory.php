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
$funkos = [];

if ($id === false) {
    header('Location: indexCategory.php');
    exit;
} else {
    $config = Config::getInstance();
    $categoryService = new CategoryService($config->db);
    $category = $categoryService->findById($id);
    $funkos = $categoryService->getFunkosByCategoryId($id);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Category Details</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
    <?php require_once 'header.php'; ?>
    <div class="card mx-auto my-5 col-md-6">
        <div class="card-header text-center">
            <h1><?php echo htmlspecialchars($category->name); ?></h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-body">
                        <p><strong>ID:</strong> <?php echo $category->id; ?></p>
                        <p><strong>Is Deleted:</strong> <?php echo $category->is_deleted ? 'Yes' : 'No'; ?></p>

                        <h4>Funkos in this category:</h4>
                        <ul>
                            <?php foreach ($funkos as $funko): ?>
                                <li><?php echo $funko->name; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-primary" href="indexCategory.php">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<?php require_once 'scripts.php'; ?>
</body>
</html>