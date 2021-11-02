<?php

session_start();

require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/SessionController.php';

use classes\SessionController;

$session = new SessionController();
$dir = $session->getSessionFolderPath();
$files = glob($dir . '/' . '*.{json,tmp,xlsx}', GLOB_BRACE);

foreach($files as $file){
    unlink($file);
}
rmdir($session->getSessionFolderPath());


$savefilesData = scandir($_SERVER['DOCUMENT_ROOT'] . "/savefiles/");

for ($i = 0; $i < count($savefilesData); $i++) {
    $folderPath = $_SERVER['DOCUMENT_ROOT'] . "/savefiles/" . $savefilesData[$i];
    if (is_dir($folderPath) && !strpos($folderPath, ".")) {
        $dirDate = date_create(date("d-m-y G:i:s", filemtime($folderPath)));
        $dateInterval = date_diff(date_create(date("d-m-y G:i:s")), $dirDate);
        if ($dateInterval->y > 0 || $dateInterval->m > 0 || $dateInterval->d > 0 || $dateInterval->h > 0 || $dateInterval->i > 3) {
            $files = glob($folderPath . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($folderPath);
        }
    }
}