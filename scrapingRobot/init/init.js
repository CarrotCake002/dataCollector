// set the default values of the program's arguments formatting
var defaultParams = {
    domain: null,
    notEnterLinksWith: ["mailto:", "javascript:", "tel:", "steam:", "#", "excel", "word", "pdf"],
    onlyEnterLinksWith: null,
    notSaveLinksWith: null,
    onlySaveLinksWith: null,
    savefile: "default",
    querySelector: ['meta', 'title', 'link', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
    getOneSelector: false,
    formattedSavefile: false,
    headlessBrowser: false,
    args: process.argv,
    clickItems: null,
    sitemapLink: null,
    startingUrls: null,
    startingUrlsFile: null,
    maxDepth: 999,
    getLinkArticle: false,
    getMeta: false,
    getHeads: false,
    getHreflang: false,
    getCanonical: false,
    getTitle: false,
}

// initialize starting arguments
let iList = 0;
let linkEnteredCount = 0;
var linkList = [];

module.exports = { defaultParams, iList, linkEnteredCount, linkList };