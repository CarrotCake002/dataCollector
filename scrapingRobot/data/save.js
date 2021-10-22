const write = require("./../text/write.js");
const link = require("./../page/link.js");

function buildJsonObj(iteration, time, resultArray, domain) {
    var url = resultArray[0][iteration][0];

    if (url.includes('http://') === false && url.includes('https://') === false) {
        url = params['domain'] + url;
    }
    return (
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
        }
    );
}

// save data that won't be modified and will be deleted in runtime for better optimization
async function saveFormData(resultArray, iteration, time, linkEnteredCount, params) {
    var jsonObj = buildJsonObj(iteration, time, resultArray, params['domain']);

    params['formattedSavefile'] ?
    jsonObj = '"' + linkEnteredCount + '": ' + JSON.stringify(jsonObj, null, 4) + ',\n\t':
    jsonObj = '"' + linkEnteredCount + '":' + JSON.stringify(jsonObj) + ',';

    write.writeInFile(jsonObj, params['savefile']);
}

// this function saves the data that was being updated in runtime and that could not be saved in the main file before
function saveFinalData(linkList, params) {
    var saveData = [];
    var i = 0;

    while (i < linkList.length) {
        if (i > 0)
            i = link.skipLinks(i, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith'], params['maxDepth'])
        if (i === false)
            break;
        saveData.push(linkList[i][2]);
        i++;
    }

    params['formattedSavefile'] ? saveData = JSON.stringify(saveData) : saveData = JSON.stringify(saveData);
    saveData = '"runtime": ' + saveData + '\n}';
    write.writeInFile(saveData, params['savefile']);
    saveData = null;
}

function end(returnArray, linkEnteredCount, params) {
    if (returnArray == null)
        return null;
    if (linkEnteredCount === 1) {
        saveFinalData(returnArray[0], params);
    }
    return returnArray;
}

module.exports = { saveFormData, end };