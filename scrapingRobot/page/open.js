const puppeteer = require('puppeteer');

async function page(params, formattedLink) {

    const browser = await puppeteer.launch({ headless: params['headlessBrowser'], args: ['--ignore-certificate-errors'] });
    const page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    const response = await page.goto(formattedLink, { waitUntil: 'networkidle0', timeout: 0 });
    return [browser, page, response];
}

module.exports = { page };