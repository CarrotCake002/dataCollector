const puppeteer = require('puppeteer');
const fs = require('fs');
var events = require('events');
const { exit, config } = require('process');
const { devNull } = require('os');


// display the help message and close the program
function configHelp(args) {
    if (args.includes("--help") === false) {
        return false;
    }
    console.log(
        `\n
    Welcome to DataCollector!\n
    This program was made by Pol Siles\n
    You can check me up on GitHub: https://github.com/CarrotCake002\n
    Read the description carefully to acknowledge all the options you can use.\n\n\n
        --help: display a console message with all information on the program.\n\n\n
        -D: define the url you want to scrap right after this flag. This flag is mandatory. View example below:\n
            [...] -D "https://example.com"\n\n\n
        -u: Define the first set of urls the program will enter. Bare in mind that filters will not apply to the first url, but they will apply to the rest of urls.\n\n\n
        -S: define the name of the .json file in which you want to save all the information collected.\n
            The default name for this file will be formData. If a .json file with the same name already exists, the new data will be appended.\n
            If no file with that name exists, it will automatically be created with read and write permission.\n
            No extension will be provided and forbidden characters will display an error message. View example below:\n
            [...] -S "saveFile" --> Will create a file named 'saveFile.json' and save all the data in there\n\n\n
        -x: allows you to decide which urls the bot should not enter. The argument after the flag will contain\n
            the keywords that could be found in the urls you want to skip. View example below:\n
            [...] -x "blog item beach"\n
            The bot will    save every link, but will not enter in any link containing the words 'blog', 'item', or 'beach'.\n\n\n
        -i: opposite of the '-x' flag. The '-i' flag will ignore all links that do NOT contain any of the specified keywords.\n
            View example below:\n
            [...] -i "blog item beach"\n
            The bot will save every link, but will skip any link that does not contain any of the words 'blog', 'item', or 'beach'.\n
            The flags '-x' and '-i' can be used together to get a better filter, but the flag '-i' has the highest priority.\n\n\n
        -s: allows you to choose which html selectors you want to get from the website in every url.\n
            This includes any Class or Id. View example below:\n
            block example: [...] -s "div"   -->     will get the first <div> block.\n
            class example: [...] -s ".class-name"   -->    will get the first element with the class 'class-name'.\n
            id example: [...] -s "#selectorId"   -->    will get the first element with the id 'selectorId'.\n\n\n
        -c: define JavaScript elements that you wish to click during navigation. This applies to every url, but if no element is found the robot won't do anything.\n
            You will need to provide the JS path which you can obtain by inspecting the element in any browser.\n
            You can also provide multiple elements to click, separating them with comas.\n\n\n
        -m: with this flag you can specify the sitemap of the website to obtain the highest number of links possible in only one website.\n
            It is currently working only for ShBarcelona, but the idea is to amplify it to any website in the future.\n\n\n
        -f: if this flag is present, the save data file will be formatted and easier to read.\n
            This flag takes no arguments.\n\n\n
        -H: if this flag is present, the program will launch with a headless browser.\n
            Have in mind that a headless browser will make it easier for some websites to detect the bot,\n
            but the bot will consume less resources.\n
            This flag takes no arguments.\n\n\n
        -o: use this flag if you want to get the first selector of each type in every site instead of all selectors.\n
            This flag takes no arguments.\n\n\n
        
    Other direct access flags:\n
        -gArticle: gets the <a> tag from every url found.\n\n
        -gMeta: gets all <meta> tags from every page.\n\n
        -gHeads: gets all <h1>, <h2>, <h3>, <h4>, <h5>, <h6> from every page if they exists.\n\n
        -gHreflang: gets all <link> tags with an hreflang attribute from every page.\n\n
        -gCanonical: gets all <link> tags with a canonical attribute from every page.\n\n
        -gTitle: gets the <title> tag of every page.\n
        `
        
    );
    exit(0);
}

// not enter links containing any string from the -x flag
function isUnwantedLink(link, unwantedLinks) {
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
function isWantedLink(link, wantedLinks) {
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
        if (isWantedLink(link, wantedLinks) && !isUnwantedLink(link, unwantedLinks) && linkList[iList][1] <= params['maxDepth']) {
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
            if (elem.innerHTML.includes("https") || elem.innerHTML.includes("http"))
                linkList.push([elem.innerHTML, 1, 1]);
            else
                return linkList;
            i++;
        }
    }, linkList);
    browser.close();
    return linkList;
}

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
        await page.waitFor(3000);
    }

    var returnArray = await page.evaluate((linkList, params, iList) => {

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
                    if (link != null && params['domain'].includes("milanuncios") && link.indexOf("?pagina=") === 0) {
                        link = params['domain'] + "/alquiler-de-viviendas-en-barcelona-barcelona/" + link;
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
                        linkList.push([link, linkList[iList][1] + 1, 1]);
                        newLinkArray.push(link);
                        if (params['getLinkArticle'])
                            linkArticle.push(general[i].outerHTML);
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
    //returnArray = null;
    returnArray = await getContent(linkList, iList, linkEnteredCount);
    linkEnteredCount--;
    if (linkEnteredCount === 0) {
        saveFinalData(returnArray[0]);
    }
    return returnArray;
}

async function writeInFile(string) {
    fs.writeFile(defaultParams['args'][1] + "/../../savefiles/" + defaultParams['savefile'] + '.json', string, { flag: 'a+' }, (err) => {
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

    defaultParams['formattedSavefile'] ? saveData = JSON.stringify(saveData) : saveData = JSON.stringify(saveData);
    saveData = '"runtime": ' + saveData + '\n}';
    writeInFile(saveData);
    saveData = null;
}

// save data that won't be modified and will be deleted at runtime for better optimization
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

    defaultParams['formattedSavefile'] ? jsonObj = JSON.stringify(jsonObj, null, 4) : jsonObj = JSON.stringify(jsonObj);

    jsonObj = '"' + linkEnteredCount + '": ' + jsonObj + ',\n\t';

    writeInFile(jsonObj);
}

var eventEmitter = new events.EventEmitter();

var dataHandler = async function (returnArray, iteration, time, linkEnteredCount) {
    await saveFormData(returnArray, iteration, time, linkEnteredCount);
}

eventEmitter.on('saveData', dataHandler);


// check if the domain is correct and asign the variable's value
function configDomain(args) {
    if (!args.includes("-D") || args[args.indexOf("-D") + 1] === undefined || !args[args.indexOf("-D") + 1].includes('http')) {
        console.log("Error: you need to set a valid domain of the website you want to scrape.");
        return false;
    }
    var domain = args[args.indexOf("-D") + 1];
    if (domain[domain.length - 1] == '/') {
        domain = domain.slice(0, -1);
    }
    return domain.trim();
}

//check savefile's name and return it or return default if no name specified
function configSavefile(args) {
    if (args.includes("-S") === false) {
        return defaultParams['savefile'];
    } if (args[args.indexOf("-S") + 1] === undefined) {
        console.log("Error: after '-S': no name was provided for the .json save file.");
        return false;
    } if (args[args.indexOf("-S") + 1].includes(".")) {
        console.log("Error: after '-S': the file name should not contain an extension. It will automatically be a .json file.")
        return false;
    }

    var i = 0;
    var savefile = args[args.indexOf("-S") + 1];
    const forbidChars = `<>:"/\\|?*\n-!&`;

    while (i < forbidChars.length) {
        if (savefile.includes(forbidChars[i])) {
            console.log("Error: after '-S': the save file name can't contain a forbidden character.");
            return false;
        }
        i++;
    }
    return savefile.trim();
}

// exclude the links containing a particular string
function configStrLinkExclude(args) {
    if (args.includes("-x") === false) {
        return defaultParams['notEnterLinksWith'];
    } if (args[args.indexOf("-x") + 1] === undefined) {
        console.log("Error: after -x: missing link exclusion arguments.");
        return false;
    }
    var avoidLinks = args[args.indexOf("-x") + 1].trim().split(",");
    return defaultParams['notEnterLinksWith'].concat(avoidLinks);
}

// use the selector the user asked for
function configQuerySelector(args) {
    if (args.includes("-s") === false) {
        return defaultParams['querySelector'];
    } if (args[args.indexOf("-s") + 1] === undefined) {
        console.log("Error: after -s: missing selector argument.");
        return false;
    }
    var selectorArray = args[args.indexOf("-s") + 1].trim().split(",");
    return defaultParams['querySelector'].concat(selectorArray);
}

// include only links contaning the specified string
function configStrLinkInclude(args, defParams) {
    if (args.includes("-i") === false) {
        return ['/'];
    } if (args[args.indexOf("-i") + 1] === undefined) {
        console.log("Error: after -i: missing link inclusion arguments.");
        return false;
    }
    defParams['onlyEnterLinksWith'] = args[args.indexOf("-i") + 1].trim().split(",");
    return defParams['onlyEnterLinksWith'];
}

// config the items that need to be clicked in every page
function configClickItems(args) {
    if (args.includes("-c") === false) {
        return null;
    } if (args[args.indexOf("-c") + 1] === undefined) {
        console.log("Error: after -c: missing clickable items.");
        return false;
    }
    defaultParams['clickItems'] = args[args.indexOf("-c") + 1].trim().split(",");
    return defaultParams['clickItems'];
}

// config the non-clickable links sitemap url
function configSitemapLink(args) {
    if (args.includes("-m") === false) {
        return null;
    } if (args[args.indexOf("-m") + 1] === undefined) {
        console.log("Error: after -m: missing sitemap url.");
        return false;
    }
    defaultParams['sitemapLink'] = args[args.indexOf("-m") + 1].trim();
    return defaultParams['sitemapLink'];
}

// config the max depth the program will inter. The depth input will be entered, but not any higher than that
function configMaxDepth(args) {
    if (args.includes("-d") === false)
        return 999;
    if (args[args.indexOf("-d") + 1] === undefined) {
        console.log("Error: after -d: you need to specify the maximum depth.");
        return false;
    }
    defaultParams['maxDepth'] = parseInt(args[args.indexOf("-d") + 1]);
    return(defaultParams['maxDepth']);
}

// config the array of urls the robot will enter first. However the non-clickable sitemap, if set, will always have the highest priority
function configStartingUrls(args) {
    if (args.includes("-u") === false)
        return [[defaultParams['domain'] + '/', 0, 1]];
    if (args[args.indexOf("-u") + 1] === undefined) {
        console.log("Error: after -u: missing starting url.");
        return false;
    }
    defaultParams['startingUrls'] = [];
    var startingUrls = args[args.indexOf("-u") + 1].trim().split(",");
    for (var i = 0; i < startingUrls.length; i++) {
        defaultParams['startingUrls'].push([startingUrls[i], 0, 1]);
    }
    return defaultParams['startingUrls'];
}

// config if the data file should be formatted. A simple format will be present even if this flag is set as false
function configSaveFormat(args) {
    return args.includes("-f") ? true : false;
}

// config if the program should open a chromium browser, or instead launch headless
function configHeadBrowser(args) {
    return args.includes("-H") ? true : false;
}

// config if the program should get only the first of every custom selector instead of all of them
function configGetAllHtml(args) {
    return args.includes("-o") ? true : false;
}

// config if the program should get the <a> tag for every found url in each page
function configGetLinkArticle(args) {
    return args.includes("-gArticle") ? true : false;
}

// config if the program should get all the <meta> tags for each page
function configGetMeta(args) {
    return args.includes("-gMeta") ? true : false;
}

// config if the program should get all the heads (h1, h2, h3, h4, h5, h6) for each page
function configGetHeads(args) {
    return args.includes("-gHeads") ? true : false;
}

// config if the program should get all <link> tags with a hreflang attribute for each page
function configGetHreflang(args) {
    return args.includes("-gHreflang") ? true : false;
}

// config if the program should get all <link> tags with a canonical attribute for each page
function configGetCanonical(args) {
    return args.includes("-gCanonical") ? true : false;
}

// config if the program should get the <title> tag for each page
function configGetTitle(args) {
    return args.includes("-gTitle") ? true : false;
}

// check if correct params are input and formatting them for the program to read them
function configParams(args, defParams) {
    configHelp(args);
    if (defParams['domain'] = configDomain(args), defParams['domain'] === false) {
        return false;
    } if (defParams['savefile'] = configSavefile(args), defParams['savefile'] === false) {
        return false;
    } if (defParams['notEnterLinksWith'] = configStrLinkExclude(args), defParams['notEnterLinksWith'] === false) {
        return false;
    } if (defParams['querySelector'] = configQuerySelector(args), defParams['querySelector'] === false) {
        return false;
    } if (defParams['clickItems'] = configClickItems(args), defParams['clickItems'] === false) {
        return false;
    } if (defParams['sitemapLink'] = configSitemapLink(args), defParams['sitemapLink'] === false) {
        return false;
    } if (defParams['startingUrls'] = configStartingUrls(args), defParams['startingUrls'] === false) {
        return false;
    } if (defParams['maxDepth'] = configMaxDepth(args), defParams['maxDepth'] === false) {
        return false;
    } if (defParams['onlyEnterLinksWith'] = configStrLinkInclude(args, defParams), defParams['onlyEnterLinksWith'] === false) {
        return false;
    }
    defParams['getOneSelector'] = configGetAllHtml(args);
    defParams['headlessBrowser'] = configHeadBrowser(args);
    defParams['formattedSavefile'] = configSaveFormat(args);
    defParams['getLinkArticle'] = configGetLinkArticle(args);
    defParams['getMeta'] = configGetMeta(args);
    defParams['getHeads'] = configGetHeads(args);
    defParams['getHreflang'] = configGetHreflang(args);
    defParams['getCanonical'] = configGetCanonical(args);
    defParams['getTitle'] = configGetTitle(args);
    return defParams;
}

// set the default values of the program's arguments formatting
var defaultParams = {
    domain: null,
    notEnterLinksWith: ["mailto:", "javascript:", "tel:", "steam:", "#", "excel", "word", "pdf"],
    onlyEnterLinksWith: null,
    savefile: "default",
    querySelector: ['meta', 'title', 'link', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
    getOneSelector: false,
    formattedSavefile: false,
    headlessBrowser: false,
    args: process.argv,
    clickItems: null,
    sitemapLink: null,
    startingUrls: null,
    maxDepth: 999,
    getLinkArticle: false,
    getMeta: false,
    getHeads: false,
    getHreflang: false,
    getCanonical: false,
    getTitle: false,

}

// get the program execution arguments
const args = process.argv.slice(2);
const params = configParams(args, defaultParams);
if (!params) {
    console.log("Execute with --help to view all valid options.");
    return 84;
}
console.log(params);

// initialize starting arguments
let iList = 0;
let linkEnteredCount = 0;
var linkList = [];


if (params['sitemapLink'] === null)
    linkList = params['startingUrls'];

writeInFile('{\n\t');

var returnArray = getContent(linkList, iList, linkEnteredCount);

if (returnArray === undefined || returnArray === null || returnArray === false) {
    console.log("Error: an error has occured and the program closed unexpectedly.");
    return 84;
}