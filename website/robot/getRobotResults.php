<?php

require_once __DIR__ . '/../views/header.php';
require '../controllers/OpenFileController.php';

use classes\OpenFileController;

if (isset($_FILES)) {
    if (isset($_FILES['openFile'])) {
        @ $json = file_get_contents($_FILES['openFile']['tmp_name']);
        if ($json === false) {
            echo "The file you sent doesn't exist";
            return;
        }
        $json_data = json_decode($json, true);
        if ($json_data === null) {
            echo "The save file format is not correct. Make sure there are no errors in the syntax.";
            return;
        }
    }
    $openFile = new OpenFileController($json_data);

    ?>


<div id="tableBlock">
        <button class="copyTableButton" onclick="copyLinksTable()">Copy Table contents</button>
        <table id="allLinksTable">
            <tr>
                <th>Id</th>
                <th>Iteration</th>
                <th>URL</th>
                <th>Depth</th>
                <th>Status</th>
                <th>Nb links found</th>
                <th>More details</th>
            </tr>

            <?php for ($i = 1; $i < $openFile->getObjectCount(); $i++): ?>

            <tr>
                <td><?= $i ?></td>
                <td><?= $openFile->getIteration($i) ?></td>
                <td><a href="<?= $openFile->getURl($i) ?>"><?= $openFile->getUrl($i) ?></a></td>
                <td><?= $openFile->getUrlDepth($i) ?></td>
                <td><?= $openFile->getStatus($i) ?></td>
                <td><?= $openFile->getAllLinksSize($i) ?></td>
                <td><a href="<?= '/website/views/getLinkDetails.php/?object=' . $i . '&filename=' . $_POST['openFile'] ?>">Click for more details</a></td>
            </tr>
            <?php
                endfor;
            ?>
        </table>
    </div>

    <script>
        function copyLinksTable() {
            var copy = document.getElementById('allLinksTable');
            
            window.getSelection().selectAllChildren(copy);
            document.execCommand('Copy');
        }
    </script>

<?php
}

require_once __DIR__ . '/../views/footer.php';