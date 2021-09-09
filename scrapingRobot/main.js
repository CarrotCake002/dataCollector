const puppeteer = require('puppeteer');
const fs = require('fs');

async function getContent(linkList, iList, params) {

    console.log("Iteration " + iList);
    var formattedLink = formatEnteringLink(linkList[iList][0], params['domain']);
    console.log(formattedLink + "\n");

    // open a new browser and page with the new url
    var time = Date.now();
    const browser = await puppeteer.launch({ headless: params['headlessBrowser'], args: [ '--ignore-certificate-errors' ]});
    const page = await browser.newPage();
    await page.setViewport({ width: 1000, height: 926 });
    const response = await page.goto(formattedLink, { waitUntil: 'networkidle0', timeout: 0 });

    // format the next link it's going to enter, to avoid entering an unexistant link and crash it or getting lost in the web
    function formatEnteringLink(link, domain) {
        if (link.includes("https://") || link.includes("http://")) {
            return link;
        } else if (link.includes("https://") === false && link.includes("http://") === false && link[0] != '/') {
            return domain + '/' + link;
        }
        return domain + link;
    }

    // skip all links set as undesired with the initial program arguments
    function skipUndesiredLinks(iList, linkList, unwantedLinks, wantedLinks) {
        var j = 0;
        var h = 0;

        if (wantedLinks === null || wantedLinks === undefined) {
            wantedLinks = ['/'];
        }
        while (linkList[iList] != undefined) {
            var tempLink = linkList[iList][0];

            if (wantedLinks[h] === undefined) {
                iList++;
                j = 0;
                h = 0;
                continue;
            }
            if (tempLink.includes("http://") === false && tempLink.includes("https://") === false) {
                tempLink = params["domain"] + tempLink;
            }
            if (tempLink.includes(wantedLinks[h]) === false) {
                h++;
                continue;
            }
            while (j < unwantedLinks.length) {
                if (tempLink.includes(unwantedLinks[j])) {
                    break;
                }
                j++;
            }
            if (j >= unwantedLinks.length) {
                return iList;
            }
            iList++;
            j = 0;
            h = 0;
        }
        return iList;
    }

    var returnArray = await page.evaluate((linkList, params, iList) => {

        // avoid saving the same link multiple times and get better optimization
        function checkLinkIsSaved(linkList, newLink) {
            var result = [];
            for (var i = 0; i < linkList.length; i++) {
                if (newLink === linkList[i][0]) {
                    linkList[i][2]++;
                    result = [true, linkList];
                    return result;
                }
            }
            result = [false, linkList];
            return result;
        }

        // filter empty links
        function isNeededLink(newLink) {
            if (newLink === '') {
                return false;
            }
            return true;
        }

        // get all the new links from the current website and add them the the link lists
        function getNewLinks(linkList) {
            var general = document.querySelectorAll('a');
            var newLinkArray = [];
            
            if (general !== null && general !== undefined) {
                for (var i = 0; i < general.length; i++) {
                    var link = general[i].getAttribute('href');
                    var checkLinkSaved = checkLinkIsSaved(linkList, link);
                    var isSaved = checkLinkSaved[0];
                    linkList = checkLinkSaved[1];
                    
                    if (link != null && !isSaved && isNeededLink(link)) {
                        linkList.push([link, linkList[iList][1] + 1, 0]);
                        newLinkArray.push(link);
                    }
                }
            } else {
                console.log("Error: something unexpected happened when collecting new urls from '" + params['domain'] + "'.");
            }
            var linkResultArray = [linkList, newLinkArray];
            return linkResultArray;
        }

        // save the default selectors
        function getMetaArray() {
            var metaArray = Array.from(document.querySelectorAll(params['querySelector'][0]));
            metaArray = metaArray.map(element => {
                return element.outerHTML;
            });
            if (metaArray !== null && metaArray !== undefined) {
                if (metaArray[0] !== undefined) {
                    for (var i = 0; i < metaArray.length; i++) {
                        metaArray[i] = [metaArray[i], metaArray[i].length];
                    }
                }
            } else {
                console.log("Error: something unexpected happened when getting the <meta> selectors from '" + params['domain'] + "'.")
            }
            return metaArray;
        }

        // get the title of the website
        function getTitleArray() {
            var titleArray = Array.from(document.querySelectorAll(params['querySelector'][1]));
            titleArray = titleArray.map(element => {
                return element.innerHTML;
            });
            if (titleArray !== null && titleArray !== undefined) {
                if (titleArray[0] !== undefined) {
                    titleArray = [titleArray[0], titleArray[0].length];
                }
            } else {
                console.log("Error: something unexpected happened when getting the <title> selector from '" + params['domain'] + "'.")
            }
            return titleArray;
        }

        // get the <link> selectors with an hreflang attribute
        function getHreflangArray() {
            var hreflangArray = [];
            var getHreflangArray = Array.from(document.querySelectorAll(params['querySelector'][2]));
            getHreflangArray = getHreflangArray.map(element => {
                return element.outerHTML;
            });
            if (getHreflangArray !== null && getHreflangArray !== undefined) {
                if (getHreflangArray[0] !== undefined) {
                    for (var i = 0; i < getHreflangArray.length; i++) {
                        if (getHreflangArray[i].includes("hreflang")) {
                            hreflangArray.push(getHreflangArray[i]);
                        }
                    }
                }
            } else {
                console.log("Error: something unexpected happened when getting the hreflang attribute from '" + params['domain'] + "'.")
            }
            return hreflangArray;
        }

        // get all the html the user asked for
        function getHtmlList() {
            var htmlList = [];
            if (params['getAllSelectors'] === true) {
                for (var i = params['getAllSelectors'].length + 1; i < params['querySelector'].length; i++) {
                    var html = Array.from(document.querySelectorAll(params['querySelector'][i]));
                    htmlList.push(html.map(element => {
                        return element.outerHTML;
                    }));
                }
            } else {
                for (var i = params['getAllSelectors'].length + 1; i < params['querySelector'].length; i++) {
                    htmlList = htmlList.concat(document.querySelector(params['querySelector'][i]).outerHTML);
                }
            }
            return htmlList;
        }

        if (iList != 0) {
            writeInFile(',\n\t');
        }

        var linkResultArray = getNewLinks(linkList);
        linkList = linkResultArray[0];
        var newLinkArray = linkResultArray[1];
        var htmlList = getHtmlList();
        var metaArray = getMetaArray();
        var titleArray = getTitleArray();
        var hreflangArray = getHreflangArray();
        
        var returnArray = [linkList, htmlList, newLinkArray, metaArray, titleArray, hreflangArray];
        return returnArray;
    }, linkList, params, iList);

    browser.close();
    time = Date.now() - time;

    if (returnArray === null || returnArray === undefined) {
        console.log("Error: something unexpected happened when collecting all the data from the website '" + params['domain'] + "'.");
        return returnArray;
    }

    var siteStatus = response.status();
    if (siteStatus === null) {
        console.log("Error: the current url '" + params['domain'] + "' cannot be scraped. Please add the corresponding filters.");
    } else {
        returnArray = returnArray.concat(response.status());
    }

    eventEmitter.emit('saveData', returnArray, iList, time);

    var linkList = returnArray[0];

    iList++;
    iList = skipUndesiredLinks(iList, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith']);

    if (linkList[iList] === undefined || linkList[iList] === null) {
        console.log("Info: the program has sucessfully obtained all the links it could!");
        return returnArray;
    }

    returnArray = getContent(linkList, iList, params);
    return returnArray;
}

function writeInFile(string) {
    fs.writeFile(defaultParams['args'][1] + "/../../savefiles/" + defaultParams['savefile'] + '.json', string, { flag: 'a+' }, (err) => {
        if (err) {
            throw err;
        }
    });
}

// save data that will be removed from the PC's RAM while the program runs
function saveFormData(resultArray, iteration, time) {
    var url = resultArray[0][iteration][0];
    var metaCount = 0;
    var linksCount = 0;
    var hreflangCount = 0;
    var userSelectedCount = 0;

    if (resultArray[0][iteration][0].includes('http://') === false && resultArray[0][iteration][0].includes('https://') === false) {
        url = params['domain'] + url;
    } if (resultArray[3] !== null && resultArray[3] !== undefined) {
        metaCount = resultArray[3].length;
    } if (resultArray[5] !== null && resultArray[5] !== undefined) {
        hreflangCount = resultArray[5].length;
    } if (resultArray[1] !== null && resultArray[1] !== undefined) {
        userSelectedCount = resultArray[1].length;
    } if (resultArray[2] !== null && resultArray[2] !== undefined) {
        linksCount = resultArray[2].length;
    }
    var jsonObj = {
        "Iteration": iteration,
        "url": url,
        "status": resultArray[6],
        "urlDepth": resultArray[0][iteration][1],
        "time": time/1000 + 's',
        "html": {
            "title": resultArray[4][0],
            "titleSize": resultArray[4][1],
            "meta": resultArray[3],
            "hreflang": resultArray[5],
            "userSelected": resultArray[1],
            "metaCount": metaCount,
            "hreflangCount": hreflangCount,
            "userSelectedCount": userSelectedCount,
        },
        "linksCount": linksCount,
        "links": resultArray[2],
    };

    defaultParams['formattedSavefile'] ? jsonObj = JSON.stringify(jsonObj, null, 4) : jsonObj = JSON.stringify(jsonObj);

    writeInFile('"Object": ' + jsonObj);
}

// save data that needs to evolve while the program runs
function saveBrute(array) {
    var jsonObj = null;

    jsonObj = JSON.stringify(array, null, 4);

    fs.writeFile(defaultParams['args'][1] + "/../../savefiles/" + defaultParams['savefile'] + 'BruteData.json', jsonObj, (err) => {
        if (err) {
            throw err;
        }
    });
}

// handle save execution then all content is gathered
var events = require('events');
const { exit } = require('process');
var eventEmitter = new events.EventEmitter();

var dataHandler = function (returnArray, iteration, time) {
    saveBrute(returnArray);
    saveFormData(returnArray, iteration, time);
}

eventEmitter.on('saveData', dataHandler);

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
        -S: define the name of the .json file in which you want to save all the information collected.\n
            The default name for this file will be formData. If a .json file with the same name already exists, the new data will be appended.\n
            If no file with that name exists, it will automatically be created with read and write permission.\n
            No extension will be provided and forbidden characters will display an error message. View example below:\n
            [...] -S "saveFile" --> Will create a file named 'saveFile.json' and save all the data in there\n\n\n
        -f: if this flag is present, the save data file will be formatted and easier to read.\n
            This flag takes no arguments.\n\n\n
        -H: if this flag is present, the program will launch with a headless browser.\n
            Have in mind that a headless browser will make it easier for some websites to detect the bot,\n
            but the bot will consume less resources.\n
            This flag takes no arguments.\n\n\n
        -x: allows you to decide which urls the bot should not enter. The argument after the flag will contain\n
            the keywords that could be found in the urls you want to skip. View example below:\n
            [...] -x "blog item beach"\n
            The bot will    save every link, but will not enter in any link containing the words 'blog', 'item', or 'beach'.\n\n\n
        -i: opposite of the '-x' flag. The '-i' flag will ignore all links that do NOT contain any of the specified keywords.\n
            View example below:\n
            [...] -i "blog item beach"\n
            The bot will save every link, but will skip any link that does not contain any of the words 'blog', 'item', or 'beach'.\n
            The flags '-x' and '-i' can be used together to get a better filter, but the flag '-i' has the highest priority.\n\n
        -s: allows you to choose which html selector you want to get from the website in every url.\n
            This includes any Class or Id. However you will only get the first selector of each website. View example below:\n
            block example: [...] -s "div"   -->     will get the first <div> block.\n
            class example: [...] -s ".class-name"   -->    will get the first element with the class 'class-name'.\n
            id example: [...] -s "#selectorId"   -->    will get the first element with the id 'selectorId'.\n\n\n
        -a: use this flag if you want to get all the chosen selectors in every site instead of only the first one.\n
            This flag takes no arguments.\n`
    );
    exit(0);
}

// check if the domain is correct and asign the variable's value
function configDomain(args) {
    if (args.includes("-D") === false || args[args.indexOf("-D") + 1] === undefined || args[args.indexOf("-D") + 1].includes('http') === false) {
        console.log("Error: you need to set a valid domain of the website you want to scrape.");
        return false;
    }
    var domain = args[args.indexOf("-D") + 1];
    if (domain[domain.length - 1] == '/') {
        domain = domain.slice(0, -1);
    }
    return domain.trim();
}

// get the site name without extensions
function configSiteName(domain) {
    var siteName = domain.slice(domain.indexOf('.') + 1, domain.lastIndexOf('.') - domain.length);
    if (siteName === '') {
        siteName = domain.slice(domain.lastIndexOf('/') + 1, domain.lastIndexOf('.') - domain.length);
    }
    if (siteName === null || siteName === undefined) {
        console.log("Error: an error has occured with the domain. Check the input is correct and try again.");
        return false;
    }
    return siteName;
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
    var avoidLinks = args[args.indexOf("-x") + 1].trim().split(" ");
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
    var selectorArray = args[args.indexOf("-s") + 1].trim().split(" ");
    return defaultParams['querySelector'].concat(selectorArray);
}

// include only links contaning the specified string
function configStrLinkInclude(args, defParams) {
    if (args.includes("-i") === false) {
        return defParams;
    } if (args[args.indexOf("-i") + 1] === undefined) {
        console.log("Error: after -i: missing link inclusion arguments.");
        return false;
    }
    defParams['onlyEnterLinksWith'] = args[args.indexOf("-i") + 1].trim().split(" ");
    return defParams;
}

// apply formatting to the save file if the flag is sent
function configSaveFormat(args) {
    return args.includes("-f") ? true : false;
}

// change the headless property of the browser to true if specified
function configHeadBrowser(args) {
    return args.includes("-H") ? true : false;
}

function configGetAllHtml(args) {
    return args.includes("-a") ? true : false;
}

// check if correct params are input and return their values to the program
function configParams(args, defParams) {
    configHelp(args);
    if (defParams['domain'] = configDomain(args), defParams['domain'] === false) {
        return false;
    } if (defParams['siteName'] = configSiteName(defParams['domain']), defParams['siteName'] === false) {
        return false;
    } if (defParams['savefile'] = configSavefile(args), defParams['savefile'] === false) {
        return false;
    } if (defParams['notEnterLinksWith'] = configStrLinkExclude(args, defParams['siteName']), defParams['notEnterLinksWith'] === false) {
        return false;
    } if (defParams['querySelector'] = configQuerySelector(args), defParams['querySelector'] === false) {
        return false;
    } if (defParams = configStrLinkInclude(args, defParams), defParams === false) {
        return false;
    }
    defParams['getAllSelectors'] = configGetAllHtml(args);
    defParams['headlessBrowser'] = configHeadBrowser(args);
    defParams['formattedSavefile'] = configSaveFormat(args);
    return defParams;
}

async function programEnd() {
    writeInFile('\n}');
}

// set the default values of the parameters
var defaultParams = {
    help: false,
    domain: null,
    notEnterLinksWith: ["mailto:", "javascript:", "tel:", "steam:", "#", "excel", "word", "pdf"],
    onlyEnterLinksWith: null,
    savefile: "default",
    querySelector: ['meta', 'title', 'link'],
    getAllSelectors: false,
    formattedSavefile: false,
    headlessBrowser: false,
    siteName: null,
    args: process.argv
}

// get the program execution arguments
const args = process.argv.slice(2);
const params = configParams(args, defaultParams);
if (!params) {
    console.log("Execute with --help to view all valid options.");
    return 84;
}

// initialize starting arguments
let iList = 0;
var linkList = [["/", 0, 1]];

writeInFile('{\n\t');

var returnArray = getContent(linkList, iList, params);

programEnd();

if (returnArray === null || returnArray === undefined || returnArray === false) {
    console.log("Error: an error has occured and the program closed unexpectedly.");
    return 84;
}