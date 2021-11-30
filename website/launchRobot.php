<?php
require_once './views/header.php';
require './controllers/SessionController.php';
require './controllers/FilesController.php';
require './controllers/ExecController.php';
?>

<script>
    window.addEventListener('beforeunload', function (e) {
        e.preventDefault();
        e.returnValue = '';
    });
</script>

<div style="text-align: left;" onunload="beforeClose()">

<?php

use classes\ExecController;
use classes\SessionController;

set_time_limit(0);

if (isset($_POST)) {
    if (isset($_POST['token']) && $_POST['token'] !== '') {
        $session_id = $_POST['token'];
    } else
        $session_id = false;

    $session = new SessionController($session_id);
    session_start();
    $session->checkSessionId();

    if ($session->error) {
        echo "Error: the token you sent is invalid.<br>If you don't have a valid token, launch the scraping without it and a token will automatically be provided to you.";
        return;
    }
    setcookie('token', $session->session_id, time() + 28800000, '/');

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

    function getSavefile($folder, $savefile) {
        $filepath = '../savefiles/' . $folder . '/' . $savefile . '.json';
        $cpyCount = 0;

        while (file_exists($filepath)) {
            $cpyCount += 1;
            $filepath = '../savefiles/' . $folder . '/' . $savefile . '(' . $cpyCount . ').json';
            echo $filepath . '<br>';
        }
        return (str_replace(['.json', '../savefiles/'], '', $filepath));
    }

    $exec = new ExecController($session->session_id);
    if ($exec->isRobotLimitReached()) {
        return;
    }

    if (isset($_POST['domain']) && $_POST['domain'] !== '') {
        $query = 'node ../scrapingRobot/main/main.js -D "' . $_POST['domain'] . '"';
    } else {
        echo "Error: you need to specify a domain to scrape.";
        return;
    }
    if (isset($_FILES) && isset($_FILES['startingUrlFile']) && $_FILES['startingUrlFile']['size'] !== 0) {
        if (!getStartingUrlFile($session))
            return;
        $query = $query . ' -uf "' . $session->getSessionFolderPath() . '/' . basename($_FILES['startingUrlFile']['tmp_name']) . '"';
    }
    if (isset($_POST['savefile']) && $_POST['savefile'] !== '')
        $query = $query . ' -f "' . getSavefile($session->session_id, $_POST['savefile']) . '"';
    else
        $query = $query . ' -f "' . $session->getSessionFolderName() . '/default"';
    if (isset($_POST['includeEntering']) && $_POST['includeEntering'] !== '')
        $query = $query . ' -i "' . $_POST['includeEntering'] . '"';
    if (isset($_POST['excludeEntering']) && $_POST['excludeEntering'] !== '')
        $query = $query . ' -x "' . $_POST['excludeEntering'] . '"';
    if (isset($_POST['includeSaving']) && $_POST['includeSaving'] !== '')
        $query = $query . ' -sL "' . $_POST['includeSaving'] . '"';
    if (isset($_POST['excludeSaving']) && $_POST['excludeSaving'] !== '')
        $query = $query . ' -nL "' . $_POST['excludeSaving'] . '"';
    if (isset($_POST['userSelectors']) && $_POST['userSelectors'] !== '')
        $query = $query . ' -s "' . $_POST['userSelectors'] . '"';
    if (isset($_POST['maxDepth']) && $_POST['maxDepth'] !== '')
        $query = $query . ' -d "' . $_POST['maxDepth'] . '"';
    if (isset($_POST['scrollX']) && $_POST['scrollX'] !== '')
        $query = $query . ' -sX "' . $_POST['scrollX'] . '"';
    if (isset($_POST['scrollY']) && $_POST['scrollY'] !== '')
        $query = $query . ' -sY "' . $_POST['scrollY'] . '"';
    if (isset($_POST['clickItems']) && $_POST['clickItems'] !== '')
        $query = $query . ' -c "' . $_POST['clickItems'] . '"';
    if (isset($_POST['sitemapLink']) && $_POST['sitemapLink'] !== '')
        $query = $query . ' -m "' . $_POST['sitemapLink'] . '"';
    if (isset($_POST['startingUrl']) && $_POST['startingUrl'] !== '')
        $query = $query . ' -u "' . $_POST['startingUrl'] . '"';
    if (isset($_POST['formSavefile']) && $_POST['formSavefile'] === 'on')
        $query = $query . ' -F';
    if (isset($_POST['headless']) && $_POST['headless'] === 'on')
        $query = $query . ' -H';
    if (isset($_POST['allSelectors']) && $_POST['allSelectors'] === 'on')
        $query = $query . ' -o';
    if (isset($_POST['getLinkArticle']) && $_POST['getLinkArticle'] === 'on')
        $query = $query . ' -gArticle';
    if (isset($_POST['getMeta']) && $_POST['getMeta'] === 'on')
        $query = $query . ' -gMeta';
    if (isset($_POST['getHeads']) && $_POST['getHeads'] === 'on')
        $query = $query . ' -gHeads';
    if (isset($_POST['getHreflang']) && $_POST['getHreflang'] === 'on')
        $query = $query . ' -gHreflang';
    if (isset($_POST['getCanonical']) && $_POST['getCanonical'] === 'on')
        $query = $query . ' -gCanonical';
    if (isset($_POST['getTitle']) && $_POST['getTitle'] === 'on')
        $query = $query . ' -gTitle';
    $params = "?query=" . $query;
    $params = str_replace(' ', '%20', $params);
    $url = "execRobotQuery.php";
    ?>
    <script>
        url = `<?=$url?><?=$params?>`;
        const xhttp = new XMLHttpRequest();
        xhttp.open("GET", url, true);
        xhttp.send();
    </script>
    <?php
} else {
    echo "An unknown error has occured. Please, try again and if the problem persists contact the creator.";
    return;
}

?>

<br>
<h3>The robot started scraping the site you specified!</h3><br>
<p>You can now check your data anytime with this token: <b style="font-size: 20px;"><?= $session->getSessionFolderName() ?></b><br><br>
Make sure to checkout the Token page to know how the token system works.</p>
</div>

<?php

require_once './views/footer.php';

session_destroy();