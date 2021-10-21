const logs = require('./../text/logs.js');

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
function skipLinks(iList, linkList, unwantedLinks, wantedLinks, maxDepth) {
    while (linkList[iList] !== undefined) {
        var link = linkList[iList][0];
        if (enterLink(link, wantedLinks) && !notEnterLink(link, unwantedLinks) && linkList[iList][1] <= maxDepth) {
            return iList;
        }
        iList++;
    }
    return false;
}

function getNext(iList, linkList, params) {
    iList++;
    iList = skipLinks(iList, linkList, params['notEnterLinksWith'], params['onlyEnterLinksWith'], params['maxDepth']);
    if (!iList) {
        logs.success();
        return true;
    }
    return iList;
}

// format the next link it's going to enter, to avoid entering an unexistant link and crash it or getting lost in the web
function formatEnteringLink(link, domain) {
    if (link.includes("https://") || link.includes("http://")) {
        return link;
    } else if (link.includes("https://") === false && link.includes("http://") === false && link[0] != '/') {
        return domain + '/' + link;
    }
    return domain + link;
}

function getFormattedLink(iList, url, domain) {
    logs.iteration(iList)
    var formattedLink = formatEnteringLink(url, domain);
    logs.link(formattedLink);
    return formattedLink;
}

module.exports = { skipLinks, getNext, getFormattedLink };