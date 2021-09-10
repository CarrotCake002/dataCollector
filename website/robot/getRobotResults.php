<?php

if (isset($_POST)) {
    if (isset($_POST['openFile'])) {
        @ $json = file_get_contents('../../savefiles/' . $_POST['openFile']);
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
}