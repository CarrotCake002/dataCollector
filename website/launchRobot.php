<?php
session_start();
require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/header.php';
require $_SERVER["DOCUMENT_ROOT"] . '/website/controllers/SessionController.php';

use classes\SessionController;

set_time_limit(0);

$session = new SessionController();

if (!$session->checkSessionFolderExists()) {
    if (!$session->createSessionFolder()) {
        echo "Error: couldn't create the savefiles folder.";
        return;
    }
}

function getStartingUrlFile($session) {
    if (isset($_FILES['startingUrlFile']['tmp_name'])) {
        if (!$session->checkSessionFolderExists()) {
            if (!$session->createSessionFolder()) {
                echo "Error: the session folder couldn't be created.";
                return false;
            }
        }
        $filePath = $session->getSessionFolderPath() . '/' . basename($_FILES['startingUrlFile']['tmp_name']);
        if (move_uploaded_file($_FILES['startingUrlFile']['tmp_name'], $filePath) === false) {
            echo "There has been an error moving the file.";
            return false;
        }
    }
    return true;
}

if (isset($_POST)) {
    if (isset($_POST['domain']) && $_POST['domain'] !== '') {
    $query = 'node ../scrapingRobot/main/main.js -D "' . $_POST['domain'] . '" ';
    } else {
        echo "Error: you need to specify a domain to scrape.";
        return;
    }
    if (isset($_FILES) && isset($_FILES['startingUrlFile']) && $_FILES['startingUrlFile']['size'] !== 0) {
        if (!getStartingUrlFile($session))
            return;
        $query = $query . ' -uf "' . $session->getSessionFolderPath() . '/' . basename($_FILES['startingUrlFile']['tmp_name']) . '" ';
    }
    if (isset($_POST['savefile']) && $_POST['savefile'] !== '')
        $query = $query . ' -f "' . $session->getSessionFolderName() . '/' . $_POST['savefile'] . '" ';
    else
        $query = $query . ' -f "' . $session->getSessionFolderName() . '/default" ';
    if (isset($_POST['includeEntering']) && $_POST['includeEntering'] !== '')
        $query = $query . ' -i "' . $_POST['includeEntering'] . '" ';
    if (isset($_POST['excludeEntering']) && $_POST['excludeEntering'] !== '')
        $query = $query . ' -x "' . $_POST['excludeEntering'] . '" ';
    if (isset($_POST['includeSaving']) && $_POST['includeSaving'] !== '')
        $query = $query . ' -sL "' . $_POST['includeSaving'] . '" ';
    if (isset($_POST['excludeSaving']) && $_POST['excludeSaving'] !== '')
        $query = $query . ' -nL "' . $_POST['excludeSaving'] . '" ';
    if (isset($_POST['userSelectors']) && $_POST['userSelectors'] !== '')
        $query = $query . ' -s "' . $_POST['userSelectors'] . '" ';
    if (isset($_POST['maxDepth']) && $_POST['maxDepth'] !== '')
        $query = $query . ' -d "' . $_POST['maxDepth'] . '" ';
    if (isset($_POST['clickItems']) && $_POST['clickItems'] !== '')
        $query = $query . ' -c "' . $_POST['clickItems'] . '" ';
    if (isset($_POST['sitemapLink']) && $_POST['sitemapLink'] !== '')
        $query = $query . ' -m "' . $_POST['sitemapLink'] . '" ';
    if (isset($_POST['startingUrl']) && $_POST['startingUrl'] !== '')
        $query = $query . ' -u "' . $_POST['startingUrl'] . '" ';
    if (isset($_POST['formSavefile']) && $_POST['formSavefile'] === 'on')
        $query = $query . ' -F ';
    if (isset($_POST['headless']) && $_POST['headless'] === 'on')
        $query = $query . ' -H ';
    if (isset($_POST['allSelectors']) && $_POST['allSelectors'] === 'on')
        $query = $query . ' -o ';
    if (isset($_POST['getLinkArticle']) && $_POST['getLinkArticle'] === 'on')
        $query = $query . ' -gArticle ';
    if (isset($_POST['getMeta']) && $_POST['getMeta'] === 'on')
        $query = $query . ' -gMeta ';
    if (isset($_POST['getHeads']) && $_POST['getHeads'] === 'on')
        $query = $query . ' -gHeads ';
    if (isset($_POST['getHreflang']) && $_POST['getHreflang'] === 'on')
        $query = $query . ' -gHreflang ';
    if (isset($_POST['getCanonical']) && $_POST['getCanonical'] === 'on')
        $query = $query . ' -gCanonical ';
    if (isset($_POST['getTitle']) && $_POST['getTitle'] === 'on')
        $query = $query . ' -gTitle ';
    exec($query, $output);
} else {
    echo "An unknown error has occured. Please, try again and if the problem persists contact the creator.";
    return;
}

isset($_POST['savefile']) && $_POST['savefile'] !== '' ? $savefile = $session->getSessionFolderName() . '/' . $_POST['savefile'] : $savefile = $session->getSessionFolderName() . "/default";

?>
<br><br>
<a href="<?= "/savefiles/" . $savefile . ".json"; ?>" download="<?= $_POST['savefile'] ?>">Download your data!</a><br>
<a>(If you don't download the file now, you will lose it forever)</a>

<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/website/views/footer.php';

if ($_POST['getRobotLogs'] && $_POST['getRobotLogs'] === 'on') {
    for ($i = 0; $i < count($output); $i++) {
        echo $output[$i] . '<br>';
    }
}