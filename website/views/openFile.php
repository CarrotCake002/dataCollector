<?php
require_once '../views/header.php';
?>


<div>
    <div id="resultsh2">
        <h2>Which file do you want to open?</h2>
    </div>
    <div>
        <form action="/website/robot/getRobotResults.php" method="post">
                <input type="text" name="openFile" placeholder="saveFile.json">
            <button type="submit">Get results</button>
        </form>
    </div>
</div>


<?php
require_once '../views/footer.php';
?>