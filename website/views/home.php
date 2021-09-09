<div>
    <div id="homeh1">
        <h1>Scrape a new site!</h1>
    </div>
    <div>
        <form action="/website/robot/launchRobot.php" method="post">
                Domain
                <input type="text" name="domain" placeholder="https://example.com">
                Save file name
                <input type="text" name="savefile" placeholder="saveFile">
                Words to include
                <input type="text" name="include" placeholder="home .com http:// /rental">
                Words to exclude
                <input type="text" name="exclude" placeholder="blog store https:// .fr">
                Custom Selectors
                <input type="text" name="userSelectors" placeholder="h1 h2 .class #id">
                Get all selectors (default: only the first)
                <label class="switch">
                    <input type="checkbox" name="allSelectors">
                    <div class="slider round"></div>
                </label>
                Headless browser
                <label class="switch">
                    <input type="checkbox" name="headless">
                    <div class="slider round"></div>
                </label>
                Formatted savefile
                <label class="switch">
                    <input type="checkbox" name="formSavefile">
                    <div class="slider round"></div>
                </label>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>