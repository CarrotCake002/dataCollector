const puppeteer = require('puppeteer');
var events = require('events');
const { exit, config } = require('process');
const { devNull } = require('os');


const link = require("./link.js");
const sitemap = require("./sitemap.js");
const logs = require("./logs.js");


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
    const click = require("./click.js");
    await click.clickItems(params['clickItems']);

    const eval = require("./evaluate.js");
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
        saveFinalData(returnArray[0]);
    }
    return returnArray;
}

const write = require("./write.js");

// this function saves the data that was being updated in runtime and that could not be saved in the main file before
function saveFinalData(linkList) {
    var saveData = [];
    var i = 0;

    while (i < linkList.length) {
        if (i > 0)
            i = skipLinks(i, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith'])
        if (i === false)
            break;
        saveData.push(linkList[i][2]);
        i++;
    }

    params['formattedSavefile'] ? saveData = JSON.stringify(saveData) : saveData = JSON.stringify(saveData);
    saveData = '"runtime": ' + saveData + '\n}';
    write.writeInFile(params['args'][1], saveData, params['savefile']);
    saveData = null;
}

// save data that won't be modified and will be deleted in runtime for better optimization
async function saveFormData(resultArray, iteration, time, linkEnteredCount) {
    var url = resultArray[0][iteration][0];

    if (resultArray[0][iteration][0].includes('http://') === false && resultArray[0][iteration][0].includes('https://') === false) {
        url = params['domain'] + url;
    }
    var jsonObj =
    {
        "Iteration": iteration,
        "url": url,
        "status": resultArray[9],
        "urlDepth": resultArray[0][iteration][1],
        "time": time / 1000,
        "html": {
            "title": resultArray[4],
            "meta": resultArray[3],
            "hreflang": resultArray[5],
            "canonicals": resultArray[6],
            "heads": resultArray[7],
            "linkArticle": resultArray[8],
            "userSelected": resultArray[1],
        },
        "links": resultArray[2],
    };
    params['formattedSavefile'] ? jsonObj = JSON.stringify(jsonObj, null, 4) : jsonObj = JSON.stringify(jsonObj);

    jsonObj = '"' + linkEnteredCount + '": ' + jsonObj + ',\n\t';

    write.writeInFile(params['args'][1], jsonObj, params['savefile']);
}

var eventEmitter = new events.EventEmitter();

var dataHandler = async function (returnArray, iteration, time, linkEnteredCount) {
    await saveFormData(returnArray, iteration, time, linkEnteredCount);
}

eventEmitter.on('saveData', dataHandler);


const init = require("./init.js");
const configStart = require("./config.js");

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

write.writeInFile(params['args'][1], '{\n\t', params['savefile']);

var returnArray = getContent(init.linkList, init.iList, init.linkEnteredCount);

module.exports = { params };