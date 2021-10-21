// external libraries imports
var events = require('events');
const { exit, config } = require('process');
const { devNull } = require('os');

// my imported files
const configStart = require("./../init/config.js");
const sitemap = require("./../page/sitemap.js");
const eval = require("./../page/evaluate.js");
const click = require("./../page/click.js");
const write = require("./../text/write.js");
const logs = require("./../text/logs.js");
const link = require("./../page/link.js");
const open = require("./../page/open.js");
const init = require("./../init/init.js");
const save = require("./../data/save.js");


// main loop of the program. Recursive function that open/closes browsers and gets all the information from every page
async function getContent(linkList, iList, linkEnteredCount) {

    linkList = await sitemap.check(iList, linkList, params);

    // --> formatting link here
    const formattedLink = link.getFormattedLink(iList, linkList[iList][0], params['domain']);
    linkEnteredCount++;

    // open a new browser and page with the new url
    var time = Date.now();
    var openPage = await open.page(params, formattedLink)
    const browser = openPage[0];
    const page = openPage[1];
    const response = openPage[2];
    openPage = null; 

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