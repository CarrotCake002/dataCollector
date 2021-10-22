// my imported files
const imports = require("./imports.js");

// main loop of the program. Recursive function that open/closes browsers and gets all the information from every page
async function getContent(linkList, iList, linkEnteredCount) {

    linkList = await imports.sitemap.check(iList, linkList, params);

    const formattedLink = imports.link.getFormattedLink(iList, linkList[iList][0], params['domain']);
    linkEnteredCount++;

    // open a new browser and page with the new url
    var time = Date.now();
    var openPage = await imports.open.page(params, formattedLink)
    const [browser, page, response] = [openPage[0], openPage[1], openPage[2]];
    openPage = null;

    await imports.click.clickItems(params['clickItems']);
    var returnArray = await imports.eval.evaluate(linkList, params, iList, page);

    browser.close();
    time = Date.now() - time;

    if (imports.open.checkErrors(returnArray, response.status()) === false)
        return null;

    returnArray = returnArray.concat(response.status());

    await imports.save.saveFormData(returnArray, iList, time, linkEnteredCount, params);

    var linkList = returnArray[0];

    if (iList = imports.link.getNext(iList, linkList, params), iList === true)
        return returnArray;

    returnArray = null;
    returnArray = await getContent(linkList, iList, linkEnteredCount);
    linkEnteredCount--;
    return imports.save.end(returnArray, linkEnteredCount, params);
}

// get the program execution arguments
const args = process.argv.slice(2);
const params = imports.configStart.configParams(args, imports.init.defaultParams);
if (!params) {
    imports.logs.options();
    return 84;
}
imports.logs.any(params);
if (params['sitemapLink'] === null)
    imports.init.linkList = params['startingUrls'];


imports.write.writeInFile('{\n\t', params['savefile']);


getContent(imports.init.linkList, imports.init.iList, imports.init.linkEnteredCount);


module.exports = { params };