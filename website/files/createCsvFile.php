<?php

require './../controllers/OpenFileController.php';
require './../controllers/SessionController.php';

use classes\OpenFileController;
use classes\SessionController;

set_time_limit(0);

if (isset($_POST) && isset($_POST['filename']) && $_POST['filename'] !== '' && isset($_POST['dataFile'])) {

    if (isset($_POST['token']) && $_POST['token'] !== '') {
        if (!isset($_COOKIE['token']))
        setcookie('token', $_POST['token'], time() + 60 * 60 * 24 * 30, '/');
        $session = new SessionController($_POST['token']);
        if ($session === NULL || $session->error) {
            echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
            return;
        }
        session_start();
    } else {
        echo "Error: make sure you've set your personal token";
        return;
    }

    $json_data = null;
    if (!$session->checkSessionFolderExists()) {
        echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
        return;
    }
    $filePath = '../../savefiles/' . $session->getSessionFolderName() . '/' . $_POST['dataFile'] . '.tmp';
    @ $json_data = file_get_contents($filePath);
    if ($json_data === false) {
        echo "Error: the file you sent doesn't exist.";
        return;
    }
    $json_data = json_decode($json_data, true);
    if ($json_data === null) {
        echo "Error: the savefile format is not correct. Make sure there are no errors in the syntax.";
        return;
    }

    $openFile = new OpenFileController($json_data);
    $csvName = str_replace('.json', '.csv', $_POST['filename']);
    $stream = fopen('../../savefiles/' . $session->getSessionFolderName() . '/' . $csvName, 'w');

    fwrite($stream,
        "'Id'," .
        "'Iteration'," .
        "'URL'," .
        "'Depth'," .
        "'Times URL found'," .
        "'Inlink'," .
        "'Status'," .
        "'Load time'," .
        "'Title'," . "'Title size'," . "'Nb Meta description (M.d.)'," .
        $openFile->getAllMetaDescriptionTitlesInCSV() . "'Nb Meta robots (M.r.)'," . $openFile->getAllMetaRobotsTitlesInCSV() .
        "'Nb hreflang'," . $openFile->getAllHreflangTitlesInCSV() . "'Nb canonicals'," .
        $openFile->getAllCanonicalTitlesInCSV() .
        "'Nb outlinks'," . $openFile->getAllLinksTitlesInCSV() .
        $openFile->getAllHeadTitlesInCSV() .
        $openFile->getAllUserSelectorTitlesInCSV() .
    "\n");


    $objectCount = $openFile->getObjectCount();

    if ($objectCount < 1) {
        echo "<br><br>There is no data to display. Make sure you accessed the correct file.";
        die;
    }

    for ($i = 1; $i <= $objectCount; $i++):
        fwrite($stream,
            "'" . $i . "','" .
            $openFile->getIteration($i) . "','" .
            $openFile->getUrl($i) . "','" .
            $openFile->getUrlDepth($i) . "','" .
            $openFile->getTimesUrlFound($i) . "','" .
            $openFile->getUrlPredecessor($i) . "','" .
            $openFile->getStatus($i) . "','" .
            $openFile->getResponseTime($i) . "','" .
            $openFile->getTitle($i) . "','" . $openFile->getTitleSize($i) . "'," .
            $openFile->getAllMetaDescriptionSize($i) . "," .
            $openFile->getAllMetaDescriptionDataInCSV($i) .
            $openFile->getAllMetaRobotsSize($i) . "," . $openFile->getAllMetaRobotsDataInCSV($i) .
            $openFile->getAllHreflangSize($i) . "," . $openFile->getAllHreflangDataInCSV($i) . "'" .
            $openFile->getAllCanonicalsSize($i) . "'," . $openFile->getAllCanonicalDataInCSV($i) . "'" .
            $openFile->getAllLinksSize($i) . "'," . $openFile->getAllLinksDataInCSV($i) .
            $openFile->getAllHeadDataInCSV($i) .
            $openFile->getAllUserSelectorDataInCSV($i) .
        "\n");
    endfor;
    fclose($stream);

    session_destroy();
}