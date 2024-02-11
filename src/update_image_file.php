<?php

use config\Config;
use services\FunkoService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkoService.php';
require_once __DIR__ . '/models/Funko.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $config = Config::getInstance();

        $id = $_POST['id'];
        $uploadDir = $config->uploadPath;

        $archivo = $_FILES['image'];

        $name = $archivo['name'];
        $tipo = $archivo['type'];
        $tmpPath = $archivo['tmp_name'];
        $error = $archivo['error'];

        $allowedTypes = ['image/jpeg', 'image/png'];
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($fileInfo, $tmpPath);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (in_array($detectedType, $allowedTypes) && in_array($extension, $allowedExtensions)) {
            $funkoService = new FunkoService($config->db);
            $funko = $funkoService->findById($id);
            if ($funko === null) {
                header('Location: index.php');
                exit;
            }

            $newName = $funko->id . '.' . $extension;

            move_uploaded_file($tmpPath, $uploadDir . $newName);

            $funko->image = $config->uploadUrl . $newName;

            $funkoService->update($funko);


            header('Location: index.php');
            exit;
        }
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}