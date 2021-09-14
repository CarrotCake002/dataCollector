<div>
    <div id="homeh1">
        <h1>Scrape a new site!</h1>
    </div>
    <div>
        <form action="/website/robot/launchRobot.php" method="post">
                Domain*
                <input class="home_inputs" type="text" name="domain" placeholder="https://example.com">
                <p class="home_p" >Save file name<br>
                (default: "default.json")</p>
                <input class="home_inputs" type="text" name="savefile" placeholder="saveFile">
                <p class="home_p" >Words to include<br>
                (auto: "/")</p>
                <input class="home_inputs" type="text" name="include" placeholder="home .com http:// /rental">
                <p class="home_p" >Words to exclude<br>
                (auto: "mailto:", "javascript:", "tel:", "#", "excel", "word", "pdf")</p>
                <input class="home_inputs" type="text" name="exclude" placeholder="blog store https:// .fr">
                <p class="home_p" >Custom Selectors<br>
                (auto: "meta", "title", "link"->"hreflang")</p>
                <input class="home_inputs" type="text" name="userSelectors" placeholder="h1 h2 .class #id">
                <p class="home_p" >Get only the first selector<br>
                (default: all selectors)</p>
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