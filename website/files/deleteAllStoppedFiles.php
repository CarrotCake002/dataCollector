<?php

require_once '../controllers/FilesController.php';
require_once '../controllers/SessionController.php';

use classes\FilesController;
use classes\SessionController;

if (isset($_POST) && isset($_POST['token']) && isset($_POST['filetype'])) {
    $filetype = "." . $_POST['filetype'];
    $session = new SessionController($_POST['token']);
    session_start();
    $files = new FilesController($session->session_id);

    for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++) {
        if ($files->getFileStatus($fileNb) === "Stopped" && strpos($files->getFileName($fileNb), $filetype) !== false) {
            unlink($files->getFileRelativePath($fileNb));
        }
    }
    $files->updateFileList();
} else {
    echo 'Error: something happened when processing your request. Please try again.';
    return;
}

session_destroy();