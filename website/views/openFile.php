<?php
    require_once '../views/header.php';
?>


<div>
    <div id="resultsh2">
        <br>
        <h2>Which file do you want to open?</h2>
    </div>
    <div>
        <form action="/website/robot/getRobotResults.php" method="post" enctype="multipart/form-data">
            <input id="openFile" type="file" name="openFile">
            <input type="submit" name="submit">
        </form>
    </div>
</div>


<?php
    require_once '../views/footer.php';
?>