const imports = require("./imports.js");

// call all functions that will play a direct role in getting the data from the page
async function getPageData(linkList, iList, page, response) {
    await imports.mouse.wheelScroll(page, imports.configStart.params);
    await imports.mouse.clickItems(imports.configStart.params['clickItems'], page);
    var returnArray = await imports.eval.evaluate(linkList, imports.configStart.params, iList, page);
    return (imports.open.checkErrors(returnArray, response.status()) ? returnArray : null);
}

// main loop of the program. Recursive function that open/closes browsers and gets all the information from every page
async function getContent(linkList, iList, linkEnteredCount) {

    if (!linkList)
        return 84;
    linkList = await imports.sitemap.check(iList, linkList, imports.configStart.params);

    const formattedLink = imports.link.getFormattedLink(iList, linkList[iList][0], imports.configStart.params['domain']);
    linkEnteredCount++;

    var openPage = await imports.open.startPage(formattedLink);
    const [browser, page, response, startTime, chrome] = [openPage[0], openPage[1], openPage[2], openPage[3], openPage[4]];
    openPage = null;

    if (returnArray = await getPageData(linkList, iList, page, response), returnArray === null)
        return null;
    returnArray = returnArray.concat(response.status());
    linkList = returnArray[0];
    const endTime = imports.open.endPage(browser, startTime, chrome);
    await imports.save.saveFormData(returnArray, iList, endTime, linkEnteredCount, imports.configStart.params);
    if (iList = imports.link.getNext(iList, linkList, imports.configStart.params), iList === true) {
        imports.save.end(returnArray, linkEnteredCount, imports.configStart.params);
        return returnArray;
    }

    returnArray = null;
    returnArray = await getContent(linkList, iList, linkEnteredCount);
    return imports.save.end(returnArray, linkEnteredCount, imports.configStart.params);
}

module.exports = { getContent };