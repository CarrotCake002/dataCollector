<?php

require_once '../controllers/SessionController.php';
require_once '../controllers/FilesController.php';
require_once '../views/header.php';

use classes\FilesController;
use classes\SessionController;

?>

<script>
    function downloadFile(filepath) {
        $.ajax({
            method: "GET",
            type: "GET",
            url: 'downloadFILE.php',
            data: {
                'url': filepath
            },
            success: function (response) {
                //location.reload();
            },
            error: function () {
                alert("There was an error deleting the files. Please try again later.");
            }
        });
    }
</script>

<?php

function deleteSelectedFiles($files, $selectedFiles) {
    for ($fileNb = 0; $fileNb < count($selectedFiles); $fileNb++) {
        if ($files->deleteFile($files->getFileId($selectedFiles[$fileNb])) === false) {
            echo 'Error: the file could not be deleted';
            return false;
        }
    }
    $files->updateFileList();
    return true;
}

function downloadSelectedFiles($files, $selectedFiles) {
    for ($fileNb = 0; $fileNb < count($selectedFiles); $fileNb++):
        $filepath = '/savefiles/' . $files->folder . '/' . $files->file_list[$fileNb];
        ?>
        <a href="<?=$filepath?>" id="file<?=$fileNb?>" download></a>
        <script>document.getElementById('file<?=$fileNb?>').click();</script>
        <?php
    endfor;
    return true;
}

if (isset($_POST)) {
    if (isset($_POST['token'])) {
        $session = new SessionController($_POST['token']);
        session_start();
        $files = new FilesController($session->session_id);
    } else {
        return'Error: your request couldn\'t be processed. Please try again.';
    }
    $concernedFiles = array();

    for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++) {
        if (isset($_POST[$fileNb])) {
            array_push($concernedFiles, $_POST[$fileNb]);
        }
    }
    if (isset($_POST['selectedFilesOption'])) {
        if ($_POST['selectedFilesOption'] === 'download') {
            if (downloadSelectedFiles($files, $concernedFiles) === false)
                echo 'false'; return;
        } elseif ($_POST['selectedFilesOption'] === 'delete') {
            deleteSelectedFiles($files, $concernedFiles);
        } else {
            echo 'Error: please one of the options for the selected files.';
            return;
        }
    } else {
        echo 'Error: please select a valid option for the selected files.';
        return;
    }
} else {
    echo 'Error: there has been a problem with your request. Please try again.';
    return;
}

header("Location: /website/views/getFiles.php");