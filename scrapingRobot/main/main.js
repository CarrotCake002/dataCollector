// my imported files
const imports = require("./imports.js");

async function getPageData(linkList, iList, page, response) {
    await imports.click.clickItems(params['clickItems']);
    var returnArray = await imports.eval.evaluate(linkList, params, iList, page);
    return (imports.open.checkErrors(returnArray, response.status()) ? returnArray : null);
}

// main loop of the program. Recursive function that open/closes browsers and gets all the information from every page
async function getContent(linkList, iList, linkEnteredCount) {

    linkList = await imports.sitemap.check(iList, linkList, params);

    const formattedLink = imports.link.getFormattedLink(iList, linkList[iList][0], params['domain']);
    linkEnteredCount++;

    // open a new browser and page with the new url
    var openPage = await imports.open.startPage(params, formattedLink);
    const [browser, page, response, startTime] = [openPage[0], openPage[1], openPage[2], openPage[3]];
    openPage = null;

    if (returnArray = await getPageData(linkList, iList, page, response), returnArray === null)
        return null;

    const endTime = imports.open.endPage(browser, startTime);

    returnArray = returnArray.concat(response.status());

    await imports.save.saveFormData(returnArray, iList, endTime, linkEnteredCount, params);

    var linkList = returnArray[0];

    if (iList = imports.link.getNext(iList, linkList, params), iList === true) {
        imports.save.end(returnArray, linkEnteredCount, params);
        return returnArray;
    }

    returnArray = null;
    returnArray = await getContent(linkList, iList, linkEnteredCount);
    return imports.save.end(returnArray, linkEnteredCount, params);
}

// get the program execution arguments
const params = imports.configStart.initParams(imports.init.args);

if (params['sitemapLink'] === null)
    imports.init.linkList = params['startingUrls'];

imports.write.writeInFile('{\n\t', params['savefile']);

getContent(imports.init.linkList, imports.init.iList, imports.init.linkEnteredCount);

module.exports = { params };