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

module.exports = { skipLinks };