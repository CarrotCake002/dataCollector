const puppeteer = require('puppeteer');
const fs = require('fs');
var events = require('events');
const { exit, config } = require('process');
const { devNull } = require('os');


// not enter links containing any string from the -x flag
function notEnterLink(link, unwantedLinks) {
    var j = 0;

    while (j < unwantedLinks.length) {
        if (link.includes(unwantedLinks[j])) {
            return true;
        }
        j++;
    }
    return false;
}

// only enter links containing any string from the -i flag
function enterLink(link, wantedLinks) {
    var h = 0;

    while (h < wantedLinks.length) {
        if (link.includes(wantedLinks[h])) {
            return true;
        }
        h++;
    }
    return false;
}

// skip all links set as undesired with the initial program arguments
function skipLinks(iList, linkList, unwantedLinks, wantedLinks) {
    while (linkList[iList] !== undefined) {
        var link = linkList[iList][0];
        if (enterLink(link, wantedLinks) && !notEnterLink(link, unwantedLinks) && linkList[iList][1] <= params['maxDepth']) {
            return iList;
        }
        iList++;
    }
    return false;
}

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

// main loop of the program. Recursive function that open/closes browsers and gets all the information from every page
async function getContent(linkList, iList, linkEnteredCount) {

    // format the next link it's going to enter, to avoid entering an unexistant link and crash it or getting lost in the web
    function formatEnteringLink(link, domain) {
        if (link.includes("https://") || link.includes("http://")) {
            return link;
        } else if (link.includes("https://") === false && link.includes("http://") === false && link[0] != '/') {
            return domain + '/' + link;
        }
        return domain + link;
    }

    if (iList === 0 && params['sitemapLink'] !== null && params['sitemapLink'].includes('/sitemap.xml')) {
        linkList = await getSitemapUrls(linkList);
    }
    if (linkList.length < 1)
        return null;

    console.log("Iteration " + iList);
    var formattedLink = formatEnteringLink(linkList[iList][0], params['domain']);
    console.log(formattedLink + "\n");
    linkEnteredCount++;

    // open a new browser and page with the new url
    var time = Date.now();
    var browser = await puppeteer.launch({ headless: params['headlessBrowser'], args: ['--ignore-certificate-errors'] });
    const page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    const response = await page.goto(formattedLink, { waitUntil: 'networkidle0', timeout: 0 });

    // click items the user selected
    if (params['clickItems'] !== null && params['clickItems'] !== undefined && params['clickItems'] !== '') {
        await page.evaluate((clickItems) => {
            for (var i = 0; i < clickItems.length; i++) {
                document.querySelectorAll(clickItems[i]).forEach(item => {
                    item.click();
                })
            }
        }, params['clickItems']);
        await page.waitFor(2000);
    }

    const eval = require("./evaluate.js");

    var returnArray = await eval.evaluate(linkList, params, iList, page);

    browser.close();
    time = Date.now() - time;

    if (returnArray === undefined || returnArray === null) {
        console.log("Error: something unexpected happened when collecting all the data from the website '" + params['domain'] + "'.");
        return returnArray;
    }

    var siteStatus = response.status();
    if (siteStatus === null) {
        console.log("Error: the current url '" + params['domain'] + "' cannot be scraped. Please add the corresponding filters.");
        return null;
    } else {
        returnArray = returnArray.concat(siteStatus);
    }
    siteStatus = null;

    eventEmitter.emit('saveData', returnArray, iList, time, linkEnteredCount);

    var linkList = returnArray[0];

    iList++;
    iList = skipLinks(iList, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith']);
    if (!iList) {
        console.log("Info: the program has sucessfully obtained all the links it could!");
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

// function to write a string in the specified file. If the file doesn't exist it will be created
async function writeInFile(string) {
    fs.writeFile(params['args'][1] + "/../../savefiles/" + params['savefile'] + '.json', string, { flag: 'a+' }, (err) => {
    });
}

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
    writeInFile(saveData);
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

    writeInFile(jsonObj);
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
    console.log("Execute with --help to view all valid options.");
    return 84;
}
console.log(params);

if (params['sitemapLink'] === null)
    init.linkList = params['startingUrls'];

writeInFile('{\n\t');

var returnArray = getContent(init.linkList, init.iList, init.linkEnteredCount);

if (returnArray == null || returnArray === false) {
    console.log("Error: an error has occured and the program closed unexpectedly.");
    return 84;
}