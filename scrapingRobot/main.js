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

    var returnArray = await page.evaluate((linkList, params, iList) => {

        // save found links that contain any string from the -sL flag
        function saveLinksWith(link, wantedLinks) {
            if (wantedLinks == null)
                return true;
            for (var i = 0; i < wantedLinks.length; i++) {
                if (link.includes(wantedLinks[i]))
                    return true;
            }
            return false;
        }

        // not save links that contain any string from the -nL flag
        function notSaveLinksWith(link, unwantedLinks) {
            if (unwantedLinks == null)
                return false;
            for (var i = 0; i < unwantedLinks.length; i++) {
                if (link.includes(unwantedLinks[i]))
                    return true;
            }
            return false;
        }

        // check if the user wants, or not, the link that is beeing saved
        function checkSavingLink(link) {
            if (saveLinksWith(link, params['onlySaveLinksWith']) && !notSaveLinksWith(link, params['notSaveLinksWith'])) {
                return true;
            }
            return false;
        }

        // avoid saving the same link multiple times and get better optimization
        function checkLinkIsSaved(linkList, newLink) {

            for (var i = 0; i < linkList.length; i++) {
                if (newLink === linkList[i][0]) {
                    linkList[i][2]++;
                    return [true, linkList];
                }
            }
            return [false, linkList];
        }

        // get all the new links from the current website and add them the the link lists
        function getNewLinks(linkList) {
            var general = document.querySelectorAll('a');
            var newLinkArray = [];
            var linkArticle = [];

            if (general !== null && general !== undefined) {
                for (var i = 0; i < general.length; i++) {
                    var link = general[i].getAttribute('href');
                    if (link != null && params['domain'].includes("milanuncios") && link.indexOf("?demanda=n&vendedor=part&pagina=") === 0) {
                        link = params['domain'] + "/alquiler-de-pisos-en-barcelona-barcelona/" + link;
                    } else if (link != null && params['domain'].includes("milanuncios") && link.includes(".htm")) {
                        link = params['domain'] + "/alquiler-de-pisos-en-barcelona-barcelona/" + link;
                    } else if (link != null && link.charAt(0) === '/') {
                        link = params['domain'] + link;
                    } else if (link != null && link.includes("https://") === false && link.includes("http://") === false && link[0] != '/' === false && link.includes(":") === false && link.includes("#") === false) {
                        link = params['domain'] + '/' + link;
                    }
                    var checkLinkSaved = checkLinkIsSaved(linkList, link);
                    var isSaved = checkLinkSaved[0];
                    linkList = checkLinkSaved[1];
                    checkLinkSaved = null;

                    if (link != null && !isSaved) {
                        if (checkSavingLink(link)) {
                            linkList.push([link, linkList[iList][1] + 1, 1]);
                            newLinkArray.push(link);
                            if (params['getLinkArticle'])
                                linkArticle.push(general[i].outerHTML);
                        }
                    }
                }
            } else {
                console.log("Error: something unexpected happened when collecting new urls from '" + params['domain'] + "'.");
            }
            var linkResultArray = [linkList, newLinkArray, linkArticle];
            return linkResultArray;
        }

        // save the <meta> selectors
        function getMetaArray() {
            if (!params['getMeta'])
                return [];
            var metaArray = Array.from(document.querySelectorAll(params['querySelector'][0]));
            metaArray = metaArray.map(element => {
                return element.outerHTML;
            });
            if (metaArray === null || metaArray === undefined) {
                console.log("Error: something unexpected happened when getting the <meta> selectors from '" + params['domain'] + "'.")
            }
            return metaArray;
        }

        // get the <title> of the website
        function getTitle() {
            if (!params['getTitle'])
                return '';
            var titleArray = Array.from(document.querySelectorAll(params['querySelector'][1]));
            titleArray = titleArray.map(element => {
                return element.innerHTML;
            });
            if (titleArray === null || titleArray === undefined) {
                console.log("Error: something unexpected happened when getting the <title> selector from '" + params['domain'] + "'.")
            }
            return titleArray[0];
        }

        // get the <link> selectors with an hreflang attribute
        function getLinkTagArrays() {
            var hreflangArray = [];
            var canonicalArray = [];

            if (!params['getHreflang'] && !params['getCanonical'])
                return [hreflangArray, canonicalArray];

            var getLinkTagArray = Array.from(document.querySelectorAll(params['querySelector'][2]));
            getLinkTagArray = getLinkTagArray.map(element => {
                return element.outerHTML;
            });
            if (getLinkTagArray !== null && getLinkTagArray !== undefined) {
                if (getLinkTagArray[0] !== undefined) {
                    for (var i = 0; i < getLinkTagArray.length; i++) {
                        if (getLinkTagArray[i].includes("hreflang") && params['getHreflang'])
                            hreflangArray.push(getLinkTagArray[i]);
                        if (getLinkTagArray[i].includes("canonical") && params['getCanonical'])
                            canonicalArray.push(getLinkTagArray[i]);
                    }
                }
            } else {
                console.log("Error: something unexpected happened when getting the hreflang attribute from '" + params['domain'] + "'.")
            }
            return [hreflangArray, canonicalArray];
        }

        // get all heads' innerHtml from h1 to h6
        function getHeadsArray() {
            var headsArray = [];
            var tempArray = [];

            if (!params['getHeads'])
                return headsArray;
            for (var i = 3; i < 9; i++) {
                tempArray = Array.from(document.querySelectorAll(params['querySelector'][i]));
                headsArray.push(tempArray.map(element => {
                    return element.innerHTML;
                }));
            }
            tempArray = null;
            return headsArray;
        }

        // get all the html the user asked for
        function getHtmlList() {
            var htmlList = [];
            if (params['getOneSelector'] === false) {
                for (var i = 9; i < params['querySelector'].length; i++) {
                    var html = Array.from(document.querySelectorAll(params['querySelector'][i]));
                    htmlList.push(html.map(element => {
                        return element.outerHTML;
                    }));
                }
            } else {
                for (var i = 9; i < params['querySelector'].length; i++) {
                    htmlList[i - 9] = [];
                    var elem = Array.from(document.querySelectorAll(params['querySelector'][i]));
                    if (elem !== undefined && elem !== null) {
                        htmlList[i - 9].push(elem.map(element => {
                            return element.outerHTML;
                        })[0]);
                    }
                }
            }
            return htmlList;
        }

        var linkResultArray = getNewLinks(linkList);
        linkList = linkResultArray[0];
        var newLinkArray = linkResultArray[1];
        var linkArticle = linkResultArray[2];
        linkResultArray = null;
        var linkTagArray = getLinkTagArrays();
        var hreflangArray = linkTagArray[0];
        var canonicalArray = linkTagArray[1];
        linkTagArray = null;
        var metaArray = getMetaArray();
        var title = getTitle();
        var headsArray = getHeadsArray();
        var htmlList = getHtmlList();

        var returnArray = [linkList, htmlList, newLinkArray, metaArray, title, hreflangArray, canonicalArray, headsArray, linkArticle];
        return returnArray;
    }, linkList, params, iList);

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