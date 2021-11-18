<?php

require_once "website/views/header.php";

if (isset($_COOKIE)) {
    setcookie('PHPSESSID', '', -1);
}

?>

<div>
    <div id="homeh1">
        <h1>Scrape a new site!</h1>
    </div>
    <div>
        <form action="/website/launchRobot.php" method="post" enctype="multipart/form-data">
                <p>Token<br>
                (if you have one)</p>
                <input class="home_inputs" type="text" name="token" placeholder="">
                <p>Domain*<br>
                (mandatory)</p>
                <input class="home_inputs" type="text" name="domain" placeholder="https://example.com">
                <p>Starting Urls<br>
                (default: same as domain)</p>
                <input class="home_inputs" type="text" name="startingUrl" placeholder="https://site1.com,https://site2.com">
                <p>Starting urls file<br>
                (not compatible with the input above)</p>
                <input id="startingUrlFile" type="file" name="startingUrlFile">
                <p class="home_p" >Save file name<br>
                (default: "default.json")</p>
                <input class="home_inputs" type="text" name="savefile" placeholder="saveFile">
                <p class="home_p" >Words to include when filtering saving links</p>
                <input class="home_inputs" type="text" name="includeSaving" placeholder="home,.com,http://,/rental">
                <p class="home_p" >Words to exclude when filtering saving links</p>
                <input class="home_inputs" type="text" name="excludeSaving" placeholder="blog,store,https://,.fr">
                <p class="home_p" >Words to include when filtering entering links<br>
                (auto: "/")</p>
                <input class="home_inputs" type="text" name="includeEntering" placeholder="home,.com,http://,/rental">
                <p class="home_p" >Words to exclude when filtering entering links<br>
                (auto: "mailto:", "javascript:", "tel:", "#", "excel", "word", "pdf")</p>
                <input class="home_inputs" type="text" name="excludeEntering" placeholder="blog,store,https://,.fr">
                <p class="home_p" >Custom Selectors<br>
                (some can be automatically added below)</p>
                <input class="home_inputs" type="text" name="userSelectors" placeholder="h1,h2,.class,#id">
                <p>Maximum depth<br>
                (default: 999)</p>
                <input class="home_inputs" type="text" name="maxDepth" placeholder="3">
                <p>Wheel scroll X axis<br>
                (scroll times -> no default, scroll size -> def: 500, pause between scrolls in miliseconds -> def: 250)</p>
                <input class="home_inputs" type="text" name="scrollX" placeholder="10, 500, 250">
                <p>Wheel scroll Y axis<br>
                (scroll times -> no default, scroll size -> def: 500, pause between scrolls in miliseconds -> def: 250)</p>
                <input class="home_inputs" type="text" name="scrollY" placeholder="200, 50">
                <p>Click items<br>
                (paste the JS path)</p>
                <input class="home_inputs" type="text" name="clickItems" placeholder="div.itemClass > div, #itemId">
                <p>Sitemap url<br>
                (only for non-clickable sitemap)</p>
                <input class="home_inputs" type="text" name="sitemapLink" placeholder="https://example.com/sitemap.xml">
                <p class="home_p">Formatted savefile</p>
                <label class="switch">
                    <input type="checkbox" name="formSavefile">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Get link's <?= htmlentities("<a>"); ?> tag</p>
                <label class="switch">
                    <input type="checkbox" name="getLinkArticle">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Get <?= htmlentities("<meta>"); ?> tags<br>(outerHTML)</p>
                <label class="switch">
                    <input type="checkbox" name="getMeta">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Get Heads (h1, h2, etc.)</p>
                <label class="switch">
                    <input type="checkbox" name="getHeads">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Get hreflang attribute</p>
                <label class="switch">
                    <input type="checkbox" name="getHreflang">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Get canonical attribute</p>
                <label class="switch">
                    <input type="checkbox" name="getCanonical">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Get <?= htmlentities("<title>"); ?> tag</p>
                <label class="switch">
                    <input type="checkbox" name="getTitle">
                    <div class="slider round"></div>
                </label>
                <p class="home_p" >Get first selector only<br>
                (for all custom selectors)</p>
                <label class="switch">
                    <input type="checkbox" name="allSelectors">
                    <div class="slider round"></div>
                </label>
                <p class="home_p">Headless browser</p>
                <label class="switch">
                    <input type="checkbox" name="headless">
                    <div class="slider round"></div>
                </label>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<?php

require_once "website/views/footer.php";