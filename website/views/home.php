<div>
    <div id="homeh1">
        <h1>Scrape a new site!</h1>
    </div>
    <div>
        <form action="/website/robot/launchRobot.php" method="post">
            <div>
                Domain
                <input type="text" name="domain" placeholder="https://example.com">
            </div>
            <div>
                Save file name
                <input type="text" name="savefile" placeholder="saveFile">
            </div>
            <div>
                Words to include
                <input type="text" name="include" placeholder="home .com http:// /rental">
            </div>
            <div>
                Words to exclude
                <input type="text" name="exclude" placeholder="blog store https:// .fr">
            </div>
            <div>
                Custom Selectors
                <input type="text" name="userSelectors" placeholder="h1 h2 .class #id">
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>