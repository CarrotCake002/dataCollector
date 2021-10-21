// function to get the urls from a non-clickable url sitemap. Will only be called if a sitemap has been specified
async function getSitemapUrls(linkList) {

    var browser = await puppeteer.launch({ headless: params['headlessBrowser'], args: ['--ignore-certificate-errors'] });
    var page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    await page.goto(params['sitemapLink'], { waitUntil: 'networkidle0', timeout: 0 });

    linkList = await page.evaluate((linkList) => {
        var elem = null;
        var i = 1;

        while (i < 100000) {
            elem = document.querySelector("#folder" + i + " > div.opened > div:nth-child(2) > span:nth-child(2)");

            if (elem === undefined || elem === null)
                return linkList;
            if (elem.innerHTML.includes("http"))
                linkList.push([elem.innerHTML, 1, 1]);
            else
                return linkList;
            i++;
        }
    }, linkList);
    browser.close();
    return linkList;
}

async function check(iList, linkList, params) {
    if (iList === 0 && params['sitemapLink'] !== null && params['sitemapLink'].includes('/sitemap.xml')) {
        linkList = await sitemap.getSitemapUrls(linkList);
        if (linkList.length < 1)
            return null;
    }
    return linkList;
}

module.exports = { getSitemapUrls, check };