<?php

session_start();

require_once './header.php';
require './../controllers/OpenFileController.php';
require './../controllers/SpreadsheetController.php';
require './../controllers/SessionController.php';

use classes\OpenFileController;
use classes\SpreadsheetController;
use classes\SessionController;

set_time_limit(0);

$session = new SessionController();

if (isset($_GET)) :
    if (isset($_GET['filename'])) {
        @$json = file_get_contents($_GET['filename']);
        if ($json === false) {
            echo "The file you sent doesn't exist";
            return;
        }
        $json_data = json_decode($json, true);
        if ($json_data === null) {
            echo "The savefile format is not correct. Make sure there are no errors in the syntax.";
            return;
        }
        $openFile = new OpenFileController($json_data);
    } else {
        echo "The program encountered an error while opening the data file. Make sure you didn't delete the saved data.";
    }

    $sheet = new SpreadsheetController('allLinkDetails');
?>

    <div id="table_container">
        <button class="copyTableButton" onclick="copyDetailsTable()">Copy Table contents</button>
        <a href="<?= '/savefiles/' . $session->getSessionFolderName() . '/' . $sheet->sheetName . '.xlsx' ?>" download="<?= $sheet->sheetName ?>" class="downloadDataExcel" ><br><br>Download an excel with all data!</a>
        <table id="details_table">
            <tr>
                <th>Id<?= $sheet->setCellValue('A1', 'Id') ?></th>
                <th>Iteration<?= $sheet->setCellValue('B1', 'Iteration') ?></th>
                <th>URL<?= $sheet->setCellValue('C1', 'Url') ?></th>
                <th>Depth<?= $sheet->setCellValue('D1', 'Depth') ?></th>
                <th>Times URL found<?= $sheet->setCellValue('E1', 'Times Url found') ?></th>
                <th>Predecessor<?= $sheet->setCellValue('F1', 'Predecessor') ?></th>
                <th>Status<?= $sheet->setCellValue('G1', 'Status') ?></th>
                <th>Load time (sec)<?= $sheet->setCellValue('H1', 'Load time (sec)') ?></th>
                <th>Title <?= $openFile->addTabsToSizeCols(15); $sheet->setCellValue('I1', 'Title') ?></th>
                <th>Title size<?= $sheet->setCellValue('J1', 'Title size') ?></th>
                <th>Nb hreflang<?= $sheet->setCellValue('K1', 'Nb hreflang') ?></th>
                <th>Nb canonical<?= $sheet->setCellValue('L1', 'Nb canonical') ?></th>
                <th>Nb links<?= $sheet->setCellValue('M1', 'Nb links') ?></th>
                <th>First selector<?= $sheet->setCellValue('N1', 'First selector') ?></th>
            </tr>

            <?php for ($objectNb = 1; $objectNb <= $openFile->getObjectCount(); $objectNb++): ?>
                <tr>
                    <td><?= $objectNb; $sheet->setCellValue('A' . $objectNb + 1, $objectNb)?></td>
                    <td><?= $openFile->getIteration($objectNb); $sheet->setCellValue('B' . $objectNb + 1, $openFile->getIteration($objectNb)) ?></td>
                    <td>
                        <a href="<?= $openFile->getUrl($objectNb) ?>"><?= $openFile->getUrl($objectNb); $sheet->setCellValue('C' . $objectNb + 1, $openFile->getUrl($objectNb)) ?></a>
                        (<a href="<?= '/website/views/getLinkDetails.php/?object=' . $objectNb . '&filename=' . $_GET['filename']?>">details</a>)
                    </td>
                    <td><?= $openFile->getUrlDepth($objectNb); $sheet->setCellValue('D' . $objectNb + 1, $openFile->getUrlDepth($objectNb)) ?></td>
                    <td><?= $openFile->getTimesUrlFound($objectNb); $sheet->setCellValue('E' . $objectNb + 1, $openFile->getTimesUrlFound($objectNb)) ?></td>
                    <td>
                        <a href="<?= $openFile->getUrlPredecessor($objectNb) ?>"><?= $openFile->getUrlPredecessor($objectNb); $sheet->setCellValue('F' . $objectNb + 1, $openFile->getUrlPredecessor($objectNb)) ?></a>
                        (<a href="<?= '/website/views/getLinkDetails.php/?object=' . $openFile->getObjectFromUrl($openFile->getUrlPredecessor($objectNb)) . '&filename=' . $_GET['filename'] ?>">details</a>)
                    </td>
                    <td><?= $openFile->getStatus($objectNb); $sheet->setCellValue('G' . $objectNb + 1, $openFile->getStatus($objectNb)) ?></td>
                    <td><?= $openFile->getResponseTime($objectNb); $sheet->setCellValue('H' . $objectNb + 1, $openFile->getResponseTime($objectNb)) ?></td>
                    <td><?= $openFile->getTitle($objectNb); $sheet->setCellValue('I' . $objectNb + 1, $openFile->getTitle($objectNb)) ?></td>
                    <td><?= $openFile->getTitleSize($objectNb); $sheet->setCellValue('J' . $objectNb + 1, $openFile->getTitleSize($objectNb)) ?></td>
                    <td><?= $openFile->getAllHreflangSize($objectNb); $sheet->setCellValue('K' . $objectNb + 1, $openFile->getAllHreflangSize($objectNb)) ?></td>
                    <td><?= $openFile->getAllCanonicalsSize($objectNb); $sheet->setCellValue('L' . $objectNb + 1, $openFile->getAllCanonicalsSize($objectNb)) ?></td>
                    <td><?= $openFile->getAllLinksSize($objectNb); $sheet->setCellValue('M' . $objectNb + 1, $openFile->getAllLinksSize($objectNb)) ?></td>
                    <td><?= $openFile->getTelNb($objectNb); $sheet->setCellValue('N' . $objectNb + 1, $openFile->getTelNb($objectNb)) ?></td>
                </tr>
                <?php endfor; ?>
        </table>
    </div>

    <script>
        function copyDetailsTable() {
            var copy = document.getElementById('details_table');

            window.getSelection().selectAllChildren(copy);
            document.execCommand('Copy');
        }
    </script>

<?php
    $sheet->saveSpreadSheet($session->getSessionFolderPath() . '/');
else :
    echo "There has been an error while processing your request.
        Please try again and if the problem presists, contact the creator.";
    return;
endif;

require_once './footer.php';

session_destroy();