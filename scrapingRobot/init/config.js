const fs = require('fs');
const { exit } = require('process');
const init = require('./../init/init.js');
const logs = require('./../text/logs.js');
const write = require('./../text/write.js');

// display the help message and close the program
function configHelp(args) {
    if (args.includes("--help") === false) {
        return false;
    }
    logs.help();
    exit(0);
}

// check if the domain is correct and asign the variable's value
function configDomain(args) {
    if (!args.includes("-D") || args[args.indexOf("-D") + 1] === undefined || !args[args.indexOf("-D") + 1].includes('http')) {
        logs.errorDomain();
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
    if (args.includes("-f") === false) {
        return init.defaultParams['savefile'];
    } if (args[args.indexOf("-f") + 1] === undefined) {
        logs.errorSavefileName();
        return false;
    } if (args[args.indexOf("-f") + 1].includes(".")) {
        logs.errorSavefileExtension();
        return false;
    }

    var i = 0;
    var savefile = args[args.indexOf("-f") + 1];
    const forbidChars = `<>:"'|?*\n-!&`;

    while (i < forbidChars.length) {
        if (savefile.includes(forbidChars[i])) {
            logs.errorSavefileForbidden();
            return false;
        }
        i++;
    }
    return savefile.trim();
}

// exclude the links containing a particular string
function configStrLinkExclude(args) {
    if (args.includes("-x") === false) {
        return init.defaultParams['notEnterLinksWith'];
    } if (args[args.indexOf("-x") + 1] === undefined) {
        logs.errorLinkExclude();
        return false;
    }
    var avoidLinks = args[args.indexOf("-x") + 1].split(",");
    for (var i = 0; i < avoidLinks.length; i++) {
        avoidLinks[i] = avoidLinks[i].trim();
    }
    return init.defaultParams['notEnterLinksWith'].concat(avoidLinks);
}

// use the selector the user asked for
function configQuerySelector(args) {
    if (args.includes("-s") === false) {
        return init.defaultParams['querySelector'];
    } if (args[args.indexOf("-s") + 1] === undefined) {
        logs.errorSelector();
        return false;
    }
    var selectorArray = args[args.indexOf("-s") + 1].split(",");
    for (var i = 0; i < selectorArray.length; i++) {
        selectorArray[i] = selectorArray[i].trim();
    }
    return init.defaultParams['querySelector'].concat(selectorArray);
}

// include only links contaning the specified string
function configStrLinkInclude(args, defaultParams) {
    if (args.includes("-i") === false) {
        return ['/'];
    } if (args[args.indexOf("-i") + 1] === undefined) {
        logs.errorLinkInclude();
        return false;
    }
    defaultParams['onlyEnterLinksWith'] = args[args.indexOf("-i") + 1].split(",");
    for (var i = 0; i < defaultParams['onlyEnterLinksWith'].length; i++) {
        defaultParams['onlyEnterLinksWith'][i] = defaultParams['onlyEnterLinksWith'][i].trim();
    }
    return defaultParams['onlyEnterLinksWith'];
}

// config the items that need to be clicked in every page
function configClickItems(args) {
    if (args.includes("-c") === false) {
        return null;
    } if (args[args.indexOf("-c") + 1] === undefined) {
        logs.errorClickItems();
        return false;
    }
    init.defaultParams['clickItems'] = args[args.indexOf("-c") + 1].split(",");
    for (var i = 0; i < init.defaultParams['clickItems'].length; i++) {
        init.defaultParams['clickItems'][i] = init.defaultParams['clickItems'][i].trim();
    }
    return init.defaultParams['clickItems'];
}

// config the non-clickable links sitemap url
function configSitemapLink(args) {
    if (args.includes("-m") === false) {
        return null;
    } if (args[args.indexOf("-m") + 1] === undefined) {
        logs.errorSitemap();
        return false;
    }
    init.defaultParams['sitemapLink'] = args[args.indexOf("-m") + 1].trim();
    return init.defaultParams['sitemapLink'];
}

// config the max depth the program will inter. The depth input will be entered, but not any higher than that
function configMaxDepth(args) {
    if (args.includes("-d") === false)
        return 999;
    if (args[args.indexOf("-d") + 1] === undefined) {
        logs.errorDepth();
        return false;
    }
    init.defaultParams['maxDepth'] = parseInt(args[args.indexOf("-d") + 1]);
    if (init.defaultParams['maxDepth'] < 0)
        return false;
    return (init.defaultParams['maxDepth']);
}

// config the file containing the starting urls when there are too many to write directly
function configStartingUrlsFile(args) {
    if (args.includes("-uf") === false)
        return null;
    if (args[args.indexOf("-uf") + 1] === undefined) {
        logs.errorStartingUrlsFile();
        return false;
    }
    init.defaultParams["startingUrlsFile"] = args[args.indexOf("-uf") + 1];
    return init.defaultParams['startingUrlsFile'];
}

// read the starting urls from a file when they are sent through a .txt file because there are too many and the command line doesn't accept them
function readStartingUrls(filepath) {
    var data = null;
    try {
        data = fs.readFileSync(filepath, 'utf8');
    } catch (err) {
        logs.any(err);
    }
    return data;
}

// config the array of urls the robot will enter first. However the non-clickable sitemap, if set, will have the highest priority
function configStartingUrls(args, startingUrlFile) {
    if (args.includes("-u") === false && args.includes("-uf") === false)
        return [[init.defaultParams['domain'] + '/', 0, 1]];
    else if (args.includes("-u") && args.includes("-uf")) {
        logs.errorStartingUrlsIncompatibleArgs();
        return false;
    } else if (args.includes("-u") === false && args.includes("-uf"))
        var startingUrls = readStartingUrls(startingUrlFile).split(",");
    else {
        if (args[args.indexOf("-u") + 1] !== undefined)
            var startingUrls = args[args.indexOf("-u") + 1].split(",");
        else {
            logs.errorStartingUrlsMissingArg();
            return false;
        }
    }
    init.defaultParams['startingUrls'] = [];
    for (var i = 0; i < startingUrls.length; i++) {
        init.defaultParams['startingUrls'].push([startingUrls[i].trim(), 0, 1]);
    }
    return init.defaultParams['startingUrls'];
}

// configure the links you will want to save when getting new links from a page
function configSaveLinksWith(args) {
    if (!args.includes("-sL"))
        return null;
    if (args[args.indexOf("-sL") + 1] === undefined) {
        logs.errorSaveLinks();
        return false;
    }
    var links = args[args.indexOf("-sL") + 1].split(",");
    for (var i = 0; i < links.length; i++) {
        links[i] = links[i].trim();
    }
    return links;
}

// configure the links you will not want to save when getting new links from a page
function configNotSaveLinksWith(args) {
    if (!args.includes("-nL"))
        return null;
    if (args[args.indexOf("-nL") + 1] === undefined) {
        logs.errorNotSaveLinks();
        return false;
    }
    var links = args[args.indexOf("-nL") + 1].split(",");
    for (var i = 0; i < links.length; i++) {
        links[i] = links[i].trim();
    }
    return links;
}

// times to scroll, size of scroll, time between scrolls (ms)
function configScrollX(args) {
    if (!args.includes("-sX"))
        return null;
    if (args[args.indexOf("-sX") + 1] === undefined || args[args.indexOf("-sX") + 1] === '') {
        logs.scrollXArgError();
        return false;
    }
    var scroll = args[args.indexOf("-sX") + 1].split(",");
    if (scroll.length > 3) {
        logs.scrollXArgCount();
        return false;
    }
    for (var i = 0; i < scroll.length; i++) {
        scroll[i] = parseInt(scroll[i]);
        if (isNaN(scroll[i])) {
            logs.notANumber();
            return false;
        }
    }
    if (scroll.length === 1)
        scroll.push(500);
    if (scroll.length === 2)
        scroll.push(250);
    return scroll;
}

// times to scroll, size of scroll, time between scrolls (ms)
function configScrollY(args) {
    if (!args.includes("-sY"))
        return null;
    if (args[args.indexOf("-sY") + 1] === undefined || args[args.indexOf("-sY") + 1] === '') {
        logs.scrollYArgError();
        return false;
    }
    var scroll = args[args.indexOf("-sY") + 1].split(",");
    if (scroll.length > 3) {
        logs.scrollYArgCount();
        return false;
    }
    for (var i = 0; i < scroll.length; i++) {
        scroll[i] = parseInt(scroll[i]);
        if (isNaN(scroll[i])) {
            logs.notANumber();
            return false;
        }
    }
    if (scroll.length === 1)
        scroll.push(500);
    if (scroll.length === 2)
        scroll.push(250);
    return scroll;
}

// config if the data file should be formatted. A simple format will be present even if this flag is set as false
function configSaveFormat(args) {
    return args.includes("-F") ? true : false;
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
function configParams(args, defaultParams) {
    configHelp(args);
    if (defaultParams['domain'] = configDomain(args), defaultParams['domain'] === false) {
        return false;
    } if (defaultParams['savefile'] = configSavefile(args), defaultParams['savefile'] === false) {
        return false;
    } if (defaultParams['notEnterLinksWith'] = configStrLinkExclude(args), defaultParams['notEnterLinksWith'] === false) {
        return false;
    } if (defaultParams['querySelector'] = configQuerySelector(args), defaultParams['querySelector'] === false) {
        return false;
    } if (defaultParams['clickItems'] = configClickItems(args), defaultParams['clickItems'] === false) {
        return false;
    } if (defaultParams['sitemapLink'] = configSitemapLink(args), defaultParams['sitemapLink'] === false) {
        return false;
    } if (defaultParams['startingUrlsFile'] = configStartingUrlsFile(args), defaultParams['startingUrlsFile'] === false) {
        return false;
    } if (defaultParams['startingUrls'] = configStartingUrls(args, defaultParams['startingUrlsFile']), defaultParams['startingUrls'] === false) {
        return false;
    } if (defaultParams['maxDepth'] = configMaxDepth(args), defaultParams['maxDepth'] === false) {
        return false;
    } if (defaultParams['onlyEnterLinksWith'] = configStrLinkInclude(args, defaultParams), defaultParams['onlyEnterLinksWith'] === false) {
        return false;
    } if (defaultParams['onlySaveLinksWith'] = configSaveLinksWith(args), defaultParams['onlySaveLinksWith'] === false) {
        return false;
    } if (defaultParams['notSaveLinksWith'] = configNotSaveLinksWith(args), defaultParams['notSaveLinksWith'] === false) {
        return false;
    } if (defaultParams['scrollX'] = configScrollX(args), defaultParams['scrollX'] === false) {
        return false;
    } if (defaultParams['scrollY'] = configScrollY(args), defaultParams['scrollY'] === false) {
        return false;
    }
    defaultParams['getOneSelector'] = configGetAllHtml(args);
    defaultParams['headlessBrowser'] = configHeadBrowser(args);
    defaultParams['formattedSavefile'] = configSaveFormat(args);
    defaultParams['getLinkArticle'] = configGetLinkArticle(args);
    defaultParams['getMeta'] = configGetMeta(args);
    defaultParams['getHeads'] = configGetHeads(args);
    defaultParams['getHreflang'] = configGetHreflang(args);
    defaultParams['getCanonical'] = configGetCanonical(args);
    defaultParams['getTitle'] = configGetTitle(args);
    return defaultParams;
}

// this init function is in config because for some unknown reason it wouldn't work from init.js
function initParams(args) {
    const params = configParams(args, init.defaultParams);

    if (!params) {
        logs.options();
        return false;
    }
    logs.any(params);
    return params;
}

// configure the initial value of linkList to define the starting urls of the program
function setLinkList(params) {
    if (!params)
        return null;
    params['formattedSavefile'] ? write.writeInFile('{\n\t', params['savefile']) : write.writeInFile('{', params['savefile']);
    
    if (params['sitemapLink'] === null)
        return params['startingUrls'];
    return [];
}

const params = initParams(init.args);

module.exports = { setLinkList, params };