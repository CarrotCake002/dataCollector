<?php

require_once '../views/header.php';

?>

<h1>Token settings</h1>

<p>When you input a token for the first time it will be saved in your computer until it's automatically removed after 8h.</p>
<p>If you delete your token, all data associated to it will be deleted.</p>
<p>Alternatively you can choose to change your token. This will simply ask you for your token next time you go to another page,
but the token will still be active and no files will be deleted.</p>
<br><br>
<div id="tokenButtonsBox">
    <button class="tokenButton">Change token</button>
    <button class="tokenButton">Delete token</button>
</div>
<?php

require_once '../views/footer.php';