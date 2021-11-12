<?php
    require_once 'header.php';
?>

<div>
    <div id="resultsh2">
        <br>
        <h2>Which file do you want to open?</h2>
    </div>
    <div>
        <form action="./../robot/getRobotResults.php" method="post" enctype="multipart/form-data">
            <p style="margin-top: 100px;">Token</p>
            <input type="text" name="token">
            <input id="openFile" type="file" name="openFile"><br>
            <input type="submit" name="submit">
        </form>
    </div>
</div>

<?php

require_once 'footer.php';