const chromeLauncher = require('chrome-launcher');
const axios = require('axios');
const puppeteer = require('puppeteer-core');

// starts the timer, opens new browser and page with the specified parameters and returns all these values
async function startPage(formattedLink) {
    const time = Date.now();

    // opens new chrome browser
    const chrome = await chromeLauncher.launch({
        chromeFlags: [
            '--headless',
            '--disable-gpu',
            '--disable-dev-shm-usage',
            '--disable-setuid-sandbox',
            '--no-sandbox'
        ],
    });
    // connects puppeteer to the browser's new port
    const connection = await axios.get(`http://localhost:${chrome.port}/json/version`);
    const { webSocketDebuggerUrl } = connection.data;
    const browser = await puppeteer.connect({ browserWSEndpoint: webSocketDebuggerUrl })

    // opens a chrome page
    const page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    const response = await page.goto(formattedLink, { waitUntil: 'networkidle0', timeout: 0 });
    return [browser, page, response, time, chrome];
}

// closes browser and returns stopped timer
function endPage(browser, time, chrome) {
    browser.close();
    chrome.kill();
    return (Date.now() - time);
}

// cheks errors in the data returned by evaluate and the status of the website, and logs corresponding messages
function checkErrors(returnArray, status) {
    if (returnArray === undefined || returnArray === null) {
        logs.errorData();
        return false;
    }
    if (status === null) {
        logs.errorStatus();
        return false;
    }
    return true;
}

module.exports = { startPage, endPage, checkErrors };