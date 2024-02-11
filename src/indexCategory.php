<?php

use config\Config;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';

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

        <table class="table mx-auto mt-4" style="width: 80%;">
            <thead>
            <tr class="table-primary">
                <th class="text-center">Category Name</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $categoriesService = new CategoryService($config->db);
            $categories = $categoriesService->findAllCategories();
            ?>
            <?php foreach ($categories as $category): ?>
                <tr class="table-secondary">
                    <td class="text-center"><?php echo htmlspecialchars($category->name); ?></td>
                    <td class="text-center">
                        <a class="btn btn-primary btn-sm" href="detailsCategory.php?id=<?php echo $category->id; ?>">Details</a>
                        <a class="btn btn-secondary btn-sm" href="create_category.php?id=<?php echo $category->id; ?>">Create</a>
                        <a class="btn btn-warning btn-sm" href="updateCategory.php?id=<?php echo $category->id; ?>">Update</a>
                        <a class="btn btn-danger btn-sm" href="deleteCategory.php?id=<?php echo $category->id; ?>">Delete</a>
                        <a class="btn btn-success btn-sm"
                           href="activate.php?id=<?php echo $category->id; ?>"
                           onclick="return confirm('¿Estás seguro de que deseas activar esta categoría?');">
                            Activate
                        </a>
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