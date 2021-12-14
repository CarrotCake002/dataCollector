<script src="../js/filesSorting.js"></script>

<?php

require_once './../views/header.php';
require '../controllers/SessionController.php';
require '../controllers/FilesController.php';

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

session_start();
setcookie('token', $session->session_id, time() + 60 * 60 * 24 * 30, '/');
$files = new FilesController($session->session_id);
$files->deleteTemporalFiles();

if ($files->checkTokenFolderEmpty()) {
    $files->deleteTokenFolder();
    header("Location: /website/views/getFiles.php");
}

$totalSize = 0;

?>

<h1 style="text-align: center; font-size: 48px">Here you can see all your files</h1>
<p style="margin-left: 40px">If you delete all the files from this token, the token will be deleted and will become useless.</p>
<form action="selectedFiles.php" method="post">
    <div>
        <p style="float: left; margin: 22px; margin-left: 40px; vertical-align:middle">Filter by: </p>
        <ul id="filter_files_type">
            <li>
                <input class="filterCheckbox" type="checkbox" id="filterAll" name="filter" value="all" onclick="showAll([<?=$files->getFilenamesInStr()?>], [<?=$files->getFileSizesInStr()?>])" checked>
                <label for="filterAll" class="file_filters_label">All</label>
            </li>
            <li>
                <input class="filterCheckbox" type="checkbox" id="filter_json" name="filter" value="json" onclick="showFiletype('json', [<?=$files->getFilenamesInStr()?>], [<?=$files->getFileSizesInStr()?>])">
                <label for="filter_json" class="file_filters_label">Json</label>
            </li>
            <li>
                <input class="filterCheckbox" type="checkbox" id="filter_csv" name="filter" value="csv" onclick="showFiletype('csv', [<?=$files->getFilenamesInStr()?>], [<?=$files->getFileSizesInStr()?>])">
                <label for="filter_csv" class="file_filters_label">CSV</label>
            </li>
        </ul>
    </div>
    <input type="hidden" name="token" value="<?=$session->session_id?>">
    <table id="showScrapings_table">
        <tr>
            <th>File name</th>
            <th>Type</th>
            <th>Size (kB)</th>
            <th>Last updated</th>
            <th>Status</th>
            <th>Force stop</th>
            <th>Download</th>
            <th>Delete</th>
            <th>Select files to...</th>
        </tr>
        <?php for ($fileNb = 0; $fileNb < $files->getFileListSize(); $fileNb++): ?>
        <tr id="rowFile<?= $files->getFileName($fileNb) ?>">
            <td><?= $files->getFileName($fileNb) ?></td>
            <td>
                <?php
                    if ($files->getFileType($fileNb) === 'json')
                        echo '<img src="../../assets/json_file_icon.png" alt="download" style="width: 45px">';
                    else if ($files->getFileType($fileNb) === 'csv')
                        echo '<img src="../../assets/csv_file_icon.png" alt="download" style="width: 40px">';
                    else
                        echo '<b>-</b>';
                ?>
            </td>
            <td><?php echo $files->getFileSize($fileNb); $totalSize += $files->getFileSize($fileNb) ?></td>
            <td><?= $files->getFileLastUpdate($fileNb) ?></td>
            <td id="fileStatus<?= $fileNb?>"><?= $files->getFileStatus($fileNb) ?><script>changeStatusColor(<?=$fileNb?>, "<?=$files->getFileStatus($fileNb)?>");</script></td>
            <?php if ($files->getFileStatus($fileNb) === 'Active'): ?>
                <td><a href="stopRobot.php?filename=<?= $files->getFileName($fileNb) ?>"><img class="stopRobotImg" src="../../assets/stop_icon.png" alt="Stop"></a></td>
            <?php else: ?>
                <td><b>-</b></td>
            <?php endif; ?>
            <td>
                <a href="<?=$files->getFileRelativePath($fileNb)?>" download="<?=$files->file_list[$fileNb]?>">
                    <img src="../../assets/download_button.png" alt="download" style="width: 25px;">
                </a>
            </td>
            <td><img class="deleteFile" src="../../assets/delete_red_bin.png" alt="delete" onclick='deleteFile(<?=$fileNb?>,"<?=$files->folder?>")'></td>
            <td><input class="fileCheckSelector" type="checkbox" id="fileSelected<?=$fileNb?>" name="<?=$fileNb?>" value="<?=$files->getFileName($fileNb)?>"></td>
        </tr>
        <?php endfor; ?>
        <tr>
            <td class="filesTableCell"><b>Total</b></td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell" id="totalFileSize"><b><?= $totalSize ?> kB</b></td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell"><b>Stop all active</b></td>
            <td class="filesTableCell"><b>-</b></td>
            <td class="filesTableCell" style="text-align: center">
            <a href="">
                <div>
                    <b>Delete all Stopped</b>
                    </div>
                    <img class="deleteFile" id="deleteAllStopped" src="../../assets/delete_red_bin.png" alt="delete all" onclick='deleteAllStoppedFiles("<?=$files->folder?>", getCurrentFilter())'>
                </a>
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