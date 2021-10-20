var init = require('./init.js');

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
        -f: define the name of the .json file in which you want to save all the information collected.\n
            The default name for this file will be formData. If a .json file with the same name already exists, the new data will be appended.\n
            If no file with that name exists, it will automatically be created with read and write permission.\n
            No extension will be provided and forbidden characters will display an error message. View example below:\n
            [...] -f "saveFile" --> Will create a file named 'saveFile.json' and save all the data in there\n\n\n
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
        -F: if this flag is present, the save data file will be formatted and easier to read.\n
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
    if (args.includes("-f") === false) {
        return init.defaultParams['savefile'];
    } if (args[args.indexOf("-f") + 1] === undefined) {
        console.log("Error: after '-f': no name was provided for the .json save file.");
        return false;
    } if (args[args.indexOf("-f") + 1].includes(".")) {
        console.log("Error: after '-f': the file name should not contain an extension. It will automatically be a .json file.")
        return false;
    }

    var i = 0;
    var savefile = args[args.indexOf("-f") + 1];
    const forbidChars = `<>:"'\\|?*\n-!&.`;

    while (i < forbidChars.length) {
        if (savefile.includes(forbidChars[i])) {
            console.log("Error: after '-f': the save file name can't contain a forbidden character.");
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
        console.log("Error: after -x: missing link exclusion arguments.");
        return false;
    }
    var avoidLinks = args[args.indexOf("-x") + 1].trim().split(",");
    return init.defaultParams['notEnterLinksWith'].concat(avoidLinks);
}

// use the selector the user asked for
function configQuerySelector(args) {
    if (args.includes("-s") === false) {
        return init.defaultParams['querySelector'];
    } if (args[args.indexOf("-s") + 1] === undefined) {
        console.log("Error: after -s: missing selector argument.");
        return false;
    }
    var selectorArray = args[args.indexOf("-s") + 1].trim().split(",");
    return init.defaultParams['querySelector'].concat(selectorArray);
}

// include only links contaning the specified string
function configStrLinkInclude(args, defaultParams) {
    if (args.includes("-i") === false) {
        return ['/'];
    } if (args[args.indexOf("-i") + 1] === undefined) {
        console.log("Error: after -i: missing link inclusion arguments.");
        return false;
    }
    defaultParams['onlyEnterLinksWith'] = args[args.indexOf("-i") + 1].trim().split(",");
    return defaultParams['onlyEnterLinksWith'];
}

// config the items that need to be clicked in every page
function configClickItems(args) {
    if (args.includes("-c") === false) {
        return null;
    } if (args[args.indexOf("-c") + 1] === undefined) {
        console.log("Error: after -c: missing clickable items.");
        return false;
    }
    init.defaultParams['clickItems'] = args[args.indexOf("-c") + 1].trim().split(",");
    return init.defaultParams['clickItems'];
}

// config the non-clickable links sitemap url
function configSitemapLink(args) {
    if (args.includes("-m") === false) {
        return null;
    } if (args[args.indexOf("-m") + 1] === undefined) {
        console.log("Error: after -m: missing sitemap url.");
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
        console.log("Error: after -d: you need to specify the maximum depth.");
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
        console.log("Error: after -uf: missing filepath.");
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
        console.log(err);
    }
    return data;
}

// config the array of urls the robot will enter first. However the non-clickable sitemap, if set, will have the highest priority
function configStartingUrls(args, startingUrlFile) {
    if (args.includes("-u") === false && args.includes("-uf") === false)
        return [[init.defaultParams['domain'] + '/', 0, 1]];
    else if (args.includes("-u") && args.includes("-uf")) {
        console.log("Error: only one starting url argument can be provided.");
        return false;
    } else if (args.includes("-u") === false && args.includes("-uf"))
        var startingUrls = readStartingUrls(startingUrlFile).trim().split(",");
    else {
        if (args[args.indexOf("-u") + 1] !== undefined)
            var startingUrls = args[args.indexOf("-u") + 1].trim().split(",");
        else {
            console.log("Error: after -u: missing starting url.");
            return false;
        }
    }
    init.defaultParams['startingUrls'] = [];
    for (var i = 0; i < startingUrls.length; i++) {
        init.defaultParams['startingUrls'].push([startingUrls[i], 0, 1]);
    }
    return init.defaultParams['startingUrls'];
}

// configure the links you will want to save when getting new links from a page
function configSaveLinksWith(args) {
    if (!args.includes("-sL"))
        return null;
    if (args[args.indexOf("-sL") + 1] === undefined) {
        console.log("Error: after -sL: missing links to be saved.");
        return false;
    }
    return (args[args.indexOf("-sL") + 1].trim().split(","));
}

// configure the links you will not want to save when getting new links from a page
function configNotSaveLinksWith(args) {
    if (!args.includes("-nL"))
        return null;
    if (args[args.indexOf("-nL") + 1] === undefined) {
        console.log("Error: after -nL: missing links to be saved.");
        return false;
    }
    return (args[args.indexOf("-nL") + 1].trim().split(","));
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

module.exports = { configParams };