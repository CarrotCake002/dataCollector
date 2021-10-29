<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/header.php';
require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/OpenFileController.php';
require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/SpreadsheetController.php';

use classes\OpenFileController;
use classes\SpreadsheetController;

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
                <th>Canonical<?= $sheet->setCellValue('L1', 'Canonical') ?></th>
                <th>Nb links<?= $sheet->setCellValue('M1', 'Nb links') ?></th>
                <th>First selector<?= $sheet->setCellValue('N1', 'First selector') ?></th>
            </tr>

            <?php for ($objectNb = 1; $objectNb < $openFile->getObjectCount() - 1; $objectNb++): ?>
                <tr>
                    <td><?= $objectNb ?></td>
                    <td><?= $openFile->getIteration($objectNb) ?></td>
                    <td><a href="<?= $openFile->getUrl($objectNb) ?>"><?= $openFile->getUrl($objectNb) ?></a></td>
                    <td><?= $openFile->getUrlDepth($objectNb) ?></td>
                    <td><?= $openFile->getTimesUrlFound($objectNb) ?></td>
                    <td>
                        <a href="<?= $openFile->getUrlPredecessor($objectNb) ?>"><?= $openFile->getUrlPredecessor($objectNb) ?></a>
                        (<a href="<?= '/website/views/getLinkDetails.php/?object=' . $openFile->getObjectFromUrl($openFile->getUrlPredecessor($objectNb)) . '&filename=' . $_GET['filename'] ?>">details</a>)
                    </td>
                    <td><?= $openFile->getStatus($objectNb) ?></td>
                    <td><?= $openFile->getResponseTime($objectNb) ?></td>
                    <td><?= $openFile->getTitle($objectNb) ?></td>
                    <td><?= $openFile->getTitleSize($objectNb) ?></td>
                    <td><?= $openFile->getAllHreflangSize($objectNb) ?></td>
                    <td><?= $openFile->displayAllCanonicals($objectNb) ?></td>
                    <td><?= $openFile->getAllLinksSize($objectNb) ?></td>
                    <td><?= $openFile->getTelNb($objectNb) ?></td>
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
    $sheet->saveSpreadSheet($_SERVER['DOCUMENT_ROOT'] . '/website/');
else :
    echo "There has been an error while processing your request.
        Please try again and if the problem presists, contact the creator.";
    return;
endif;

require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/footer.php';
