// external libraries imports
const puppeteer = require('puppeteer');
var events = require('events');
const { exit, config } = require('process');
const { devNull } = require('os');

// my imported files
const link = require("./link.js");
const sitemap = require("./sitemap.js");
const logs = require("./logs.js");
const init = require("./init.js");
const configStart = require("./config.js");
const click = require("./click.js");
const eval = require("./evaluate.js");
const write = require("./write.js");


// main loop of the program. Recursive function that open/closes browsers and gets all the information from every page
async function getContent(linkList, iList, linkEnteredCount) {

    if (iList === 0 && params['sitemapLink'] !== null && params['sitemapLink'].includes('/sitemap.xml')) {
        linkList = await sitemap.getSitemapUrls(linkList);
        if (linkList.length < 1)
            return null;
    }

    logs.iteration(iList)
    var formattedLink = link.formatEnteringLink(linkList[iList][0], params['domain']);
    logs.link(formattedLink);
    linkEnteredCount++;

    // open a new browser and page with the new url
    var time = Date.now();
    var browser = await puppeteer.launch({ headless: params['headlessBrowser'], args: ['--ignore-certificate-errors'] });
    const page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    const response = await page.goto(formattedLink, { waitUntil: 'networkidle0', timeout: 0 });

    // --> click items here
    
    await click.clickItems(params['clickItems']);

    var returnArray = await eval.evaluate(linkList, params, iList, page);

    browser.close();
    time = Date.now() - time;

    if (returnArray === undefined || returnArray === null) {
        logs.errorData();
        return returnArray;
    }

    var siteStatus = response.status();
    if (siteStatus === null) {
        logs.errorStatus();
        return null;
    } else {
        returnArray = returnArray.concat(siteStatus);
    }
    siteStatus = null;

    eventEmitter.emit('saveData', returnArray, iList, time, linkEnteredCount);

    var linkList = returnArray[0];

    iList++;
    iList = link.skipLinks(iList, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith'], params['maxDepth']);
    if (!iList) {
        logs.success();
        return returnArray;
    }

    returnArray = returnArray.push(linkEnteredCount);
    returnArray = [returnArray[0], null];
    returnArray = await getContent(linkList, iList, linkEnteredCount);
    linkEnteredCount--;
    if (linkEnteredCount === 0) {
        save.saveFinalData(returnArray[0], params);
    }
    return returnArray;
}



// --> save here
const save = require("./save.js");

var eventEmitter = new events.EventEmitter();

var dataHandler = async function (returnArray, iteration, time, linkEnteredCount) {
    await save.saveFormData(returnArray, iteration, time, linkEnteredCount, params);
}

eventEmitter.on('saveData', dataHandler);

// get the program execution arguments
const args = process.argv.slice(2);
const params = configStart.configParams(args, init.defaultParams);
if (!params) {
    logs.options();
    return 84;
}
logs.any(params);

if (params['sitemapLink'] === null)
    init.linkList = params['startingUrls'];


write.writeInFile('{\n\t', params['savefile']);

var returnArray = getContent(init.linkList, init.iList, init.linkEnteredCount);

module.exports = { params };