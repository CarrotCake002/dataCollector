<?php

$dir = $_SERVER["DOCUMENT_ROOT"].'/savefiles/';
$files = glob($dir.'*.{json,tmp}', GLOB_BRACE);

foreach($files as $file){
    unlink($file);
}

?>