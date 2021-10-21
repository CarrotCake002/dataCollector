// external libraries imports
var events = require('events');

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

    // --> page errors here
    if (open.checkErrors(returnArray, response.status()) === false)
        return null;

    returnArray = returnArray.concat(response.status());

    await save.saveFormData(returnArray, iList, time, linkEnteredCount, params);

    var linkList = returnArray[0];

    iList++;
    iList = link.skipLinks(iList, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith'], params['maxDepth']);
    if (!iList) {
        logs.success();
        return returnArray;
    }

    returnArray = null;
    returnArray = await getContent(linkList, iList, linkEnteredCount);
    if (returnArray == null)
        return null;
    linkEnteredCount--;
    if (linkEnteredCount === 0) {
        save.saveFinalData(returnArray[0], params);
    }
    return returnArray;
}

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


getContent(init.linkList, init.iList, init.linkEnteredCount);


module.exports = { params };