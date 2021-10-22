const puppeteer = require('puppeteer');

async function startPage(params, formattedLink) {
    const time = Date.now();
    const browser = await puppeteer.launch({ headless: params['headlessBrowser'], args: ['--ignore-certificate-errors'] });
    const page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    const response = await page.goto(formattedLink, { waitUntil: 'networkidle0', timeout: 0 });
    return [browser, page, response, time];
}

function endPage(browser, time) {
    browser.close();
    return (Date.now() - time);
}

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