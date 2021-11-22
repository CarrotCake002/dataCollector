<?php
require_once './../views/header.php';
require '../controllers/SessionController.php';
require '../controllers/FilesController.php';
require_once '../views/header.php';

use classes\SessionController;
use classes\FilesController;

if (isset($_POST) && isset($_POST['token'])) {
    $session = new SessionController($_POST['token']);
    if ($session->error) {
        echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
        return;
    }
} else {
    echo "Error: a problem occured while fetching the data, please try again.";
    return;
}

setcookie('token', $session->session_id, time() + 28800000, '/');
session_start();
$files = new FilesController($session->session_id);
$files->deleteTemporalFiles();
$totalSize = 0;
?>

<script>

    function changeStatusColor(fileNb, status) {
        if (status === "Active") {
            document.getElementById("fileStatus" + fileNb).style.color = "blue";
        } else if (status === "Stopped") {
            document.getElementById("fileStatus" + fileNb).style.color = "red";
        } else if (status === "Finished") {
            document.getElementById("fileStatus" + fileNb).style.color = "#31d313";
        } else {
            document.getElementById("fileStatus" + fileNb).style.color = "black";
        }
    }

    function deleteAllStoppedFiles(token) {
        if (!confirm("Are you sure you want to delete all stopped files?"))
            return;
        $.ajax({
            method: "POST",
            type: "POST",
            url: 'deleteAllStoppedFiles.php',
            data: {
                'token': token,
            },
            success: function (response) {
                location.reload();
            },
            error: function () {
                alert("There was an error deleting the files. Please try again later.");
            }
        });
    }

    function deleteFile(fileNb, token) {
        $.ajax({
            method: "POST",
            type: "POST",
            url: 'deleteFile.php',
            data: {
                'fileNb' : fileNb,
                'token': token
            },
            success: function (response) {
                location.reload();
            },
            error: function () {
                alert("There was an error deleting the file. Please try again later.");
            }
        });
    }

</script>

<h1 style="text-align: center; font-size: 48px">Here you can see all your files</h1>
<form action="selectedFiles.php" method="post">
    <input type="hidden" name="token" value="<?=$session->session_id?>">
    <table>
        <tr>
            <th>File name</th>
            <th>Size (kB)</th>
            <th>Last updated</th>
            <th>Status</th>
            <th>Force stop</th>
            <th>Download</th>
            <th>Delete</th>
            <th>Select files to...</th>
        </tr>
        <?php for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++): ?>
        <tr>
            <td><?= $files->getFileName($fileNb) ?></td>
            <td><?php echo $files->getFileSize($fileNb); $totalSize += $files->getFileSize($fileNb) ?></td>
            <td><?= $files->getFileLastUpdate($fileNb) ?></td>
            <td id="fileStatus<?= $fileNb?>"><?= $files->getFileStatus($fileNb) ?><script>changeStatusColor(<?=$fileNb?>, "<?=$files->getFileStatus($fileNb)?>");</script></td>
            <td>Stop</td>
            <td>
                <a href="<?=$files->getFileRelativePath($fileNb)?>" download="<?=$files->file_list[$fileNb]?>">
                    <img src="../../assets/download_button.png" alt="download" style="width: 25px;">
                </a>
            </td>
            <td><img class="deleteFile" src="../../assets/delete_red_bin.png" alt="delete" onclick='deleteFile(<?=$fileNb?>,"<?=$files->folder?>")' style="width: 25px;"></td>
            <td><input class="fileCheckSelector" type="checkbox" id="fileSelected<?=$fileNb?>" name="<?=$fileNb?>" value="<?=$files->getFileName($fileNb)?>"></td>    
        </tr>
        <?php endfor; ?>
        <tr>
            <td class="filesTableCell"><b>Total</b></td>
            <td class="filesTableCell"><b><?= $totalSize ?> kB</b></td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell">Stop all active</td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell" style="text-align: center">
                <div>
                    <b>Delete all Stopped</b>
                </div>
                <img class="deleteFile" id="deleteAllStopped" src="../../assets/delete_red_bin.png" alt="delete all" onclick='deleteAllStoppedFiles("<?=$files->folder?>")' style="width: 25px; margin-top: 2px">
            </td>
            <td>
                <select name="selectedFilesOption" id="selectedFilesOption">
                    <option value="download">download</option>
                    <option value="delete">delete</option>
                </select>
                <button id="sendSelectedFiles">GO</button>
            </td>
        </tr>
    </table>
</form>
<?php

require_once './../views/footer.php';

session_destroy();