<?php

session_start();

require '../controllers/SessionController.php';

use classes\SessionController;

$session = new SessionController();
$dir = $session->getSessionFolderPath();
$files = glob($dir . '/' . '*.{json,tmp,xlsx,csv}', GLOB_BRACE);

foreach($files as $file){
    unlink($file);
}
rmdir($session->getSessionFolderPath());


$savefilesData = scandir("../../savefiles/");

for ($i = 0; $i < count($savefilesData); $i++) {
    $folderPath = "../../savefiles/" . $savefilesData[$i];
    if (is_dir($folderPath) && !strpos($folderPath, ".")) {
        $dirDate = date_create(date("d-m-y G:i:s", filemtime($folderPath)));
        $dateInterval = date_diff(date_create(date("d-m-y G:i:s")), $dirDate);
        if ($dateInterval->y > 0 || $dateInterval->m > 0 || $dateInterval->d > 0 || $dateInterval->h > 8) {
            $files = glob($folderPath . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($folderPath);
        }
    }
}