<?php
require_once '../views/header.php';



if (isset($_POST)) {
    echo $_POST['domain'] . "<br>" . $_POST['savefile'] . "<br>" .
    $_POST['include'] . "<br>" . $_POST['exclude'] . "<br>" . $_POST['userSelectors'];
}



require_once '../views/footer.php';