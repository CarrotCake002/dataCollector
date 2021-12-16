<script src="../js/tokenConfig.js"></script>

<?php

require_once '../views/header.php';

?>

<h1 style="text-align: center">Token settings</h1>

<p>When you input a token for the first time it will be saved in your computer.</p>
<p>If you delete your token, all data associated to it will be deleted.</p>
<p>Alternatively you can choose to unset your token. This will simply ask you for your token next time you navigate to a different page,
but the token data will not be deleted and can be recovered as long as you save your token.</p>
<p>The token will be automatically unset after 8h of its last use.</p>
<p>It will also be unset if you use a different token or manually unset it form this page</p>
<br><br>

<?php if (isset($_COOKIE) && isset($_COOKIE['token'])): ?>

<p id="settingsTokenDisplay">Your token is: <b><?= $_COOKIE['token'] ?></b></p>
<div class="tokenButtonsBox">
    <button class="tokenButton" onclick="unsetToken()">Unset token</button>
    <button class="tokenButton" onclick="deleteToken()">Delete token data</button>
</div>

<?php else: ?>

<div class="tokenButtonsBox">
    <h3><b>You don't have any token set at the moment.</b></h3>
</div>

<?php

endif;

require_once '../views/footer.php';