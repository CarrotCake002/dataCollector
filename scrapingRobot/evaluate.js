async function evaluate(linkList, params, iList, page) {

    var returnArray = await page.evaluate((linkList, params, iList) => {

        // save found links that contain any string from the -sL flag
        function saveLinksWith(link, wantedLinks) {
            if (wantedLinks == null)
                return true;
            for (var i = 0; i < wantedLinks.length; i++) {
                if (link.includes(wantedLinks[i]))
                    return true;
            }
            return false;
        }

        // not save links that contain any string from the -nL flag
        function notSaveLinksWith(link, unwantedLinks) {
            if (unwantedLinks == null)
                return false;
            for (var i = 0; i < unwantedLinks.length; i++) {
                if (link.includes(unwantedLinks[i]))
                    return true;
            }
            return false;
        }

        // check if the user wants, or not, the link that is beeing saved
        function checkSavingLink(link) {
            if (saveLinksWith(link, params['onlySaveLinksWith']) && !notSaveLinksWith(link, params['notSaveLinksWith'])) {
                return true;
            }
            return false;
        }

        // avoid saving the same link multiple times and get better optimization
        function checkLinkIsSaved(linkList, newLink) {

            for (var i = 0; i < linkList.length; i++) {
                if (newLink === linkList[i][0]) {
                    linkList[i][2]++;
                    return [true, linkList];
                }
            }
            return [false, linkList];
        }

        // get all the new links from the current website and add them the the link lists
        function getNewLinks(linkList) {
            var general = document.querySelectorAll('a');
            var newLinkArray = [];
            var linkArticle = [];

            if (general !== null && general !== undefined) {
                for (var i = 0; i < general.length; i++) {
                    var link = general[i].getAttribute('href');
                    if (link != null && params['domain'].includes("milanuncios") && link.indexOf("?demanda=n&vendedor=part&pagina=") === 0) {
                        link = params['domain'] + "/alquiler-de-pisos-en-barcelona-barcelona/" + link;
                    } else if (link != null && params['domain'].includes("milanuncios") && link.includes(".htm")) {
                        link = params['domain'] + "/alquiler-de-pisos-en-barcelona-barcelona/" + link;
                    } else if (link != null && link.charAt(0) === '/') {
                        link = params['domain'] + link;
                    } else if (link != null && link.includes("https://") === false && link.includes("http://") === false && link[0] != '/' === false && link.includes(":") === false && link.includes("#") === false) {
                        link = params['domain'] + '/' + link;
                    }
                    var checkLinkSaved = checkLinkIsSaved(linkList, link);
                    var isSaved = checkLinkSaved[0];
                    linkList = checkLinkSaved[1];
                    checkLinkSaved = null;

                    if (link != null && !isSaved) {
                        if (checkSavingLink(link)) {
                            linkList.push([link, linkList[iList][1] + 1, 1]);
                            newLinkArray.push(link);
                            if (params['getLinkArticle'])
                                linkArticle.push(general[i].outerHTML);
                        }
                    }
                }
            } else {
                console.log("Error: something unexpected happened when collecting new urls from '" + params['domain'] + "'.");
            }
            var linkResultArray = [linkList, newLinkArray, linkArticle];
            return linkResultArray;
        }

        // save the <meta> selectors
        function getMetaArray() {
            if (!params['getMeta'])
                return [];
            var metaArray = Array.from(document.querySelectorAll(params['querySelector'][0]));
            metaArray = metaArray.map(element => {
                return element.outerHTML;
            });
            if (metaArray === null || metaArray === undefined) {
                console.log("Error: something unexpected happened when getting the <meta> selectors from '" + params['domain'] + "'.")
            }
            return metaArray;
        }

        // get the <title> of the website
        function getTitle() {
            if (!params['getTitle'])
                return '';
            var titleArray = Array.from(document.querySelectorAll(params['querySelector'][1]));
            titleArray = titleArray.map(element => {
                return element.innerHTML;
            });
            if (titleArray === null || titleArray === undefined) {
                console.log("Error: something unexpected happened when getting the <title> selector from '" + params['domain'] + "'.")
            }
            return titleArray[0];
        }

        // get the <link> selectors with an hreflang attribute
        function getLinkTagArrays() {
            var hreflangArray = [];
            var canonicalArray = [];

            if (!params['getHreflang'] && !params['getCanonical'])
                return [hreflangArray, canonicalArray];

            var getLinkTagArray = Array.from(document.querySelectorAll(params['querySelector'][2]));
            getLinkTagArray = getLinkTagArray.map(element => {
                return element.outerHTML;
            });
            if (getLinkTagArray !== null && getLinkTagArray !== undefined) {
                if (getLinkTagArray[0] !== undefined) {
                    for (var i = 0; i < getLinkTagArray.length; i++) {
                        if (getLinkTagArray[i].includes("hreflang") && params['getHreflang'])
                            hreflangArray.push(getLinkTagArray[i]);
                        if (getLinkTagArray[i].includes("canonical") && params['getCanonical'])
                            canonicalArray.push(getLinkTagArray[i]);
                    }
                }
            } else {
                console.log("Error: something unexpected happened when getting the hreflang attribute from '" + params['domain'] + "'.")
            }
            return [hreflangArray, canonicalArray];
        }

        // get all heads' innerHtml from h1 to h6
        function getHeadsArray() {
            var headsArray = [];
            var tempArray = [];

            if (!params['getHeads'])
                return headsArray;
            for (var i = 3; i < 9; i++) {
                tempArray = Array.from(document.querySelectorAll(params['querySelector'][i]));
                headsArray.push(tempArray.map(element => {
                    return element.innerHTML;
                }));
            }
            tempArray = null;
            return headsArray;
        }

        // get all the html the user asked for
        function getHtmlList() {
            var htmlList = [];
            if (params['getOneSelector'] === false) {
                for (var i = 9; i < params['querySelector'].length; i++) {
                    var html = Array.from(document.querySelectorAll(params['querySelector'][i]));
                    htmlList.push(html.map(element => {
                        return element.outerHTML;
                    }));
                }
            } else {
                for (var i = 9; i < params['querySelector'].length; i++) {
                    htmlList[i - 9] = [];
                    var elem = Array.from(document.querySelectorAll(params['querySelector'][i]));
                    if (elem !== undefined && elem !== null) {
                        htmlList[i - 9].push(elem.map(element => {
                            return element.outerHTML;
                        })[0]);
                    }
                }
            }
            return htmlList;
        }

        var linkResultArray = getNewLinks(linkList);
        linkList = linkResultArray[0];
        var newLinkArray = linkResultArray[1];
        var linkArticle = linkResultArray[2];
        linkResultArray = null;
        var linkTagArray = getLinkTagArrays();
        var hreflangArray = linkTagArray[0];
        var canonicalArray = linkTagArray[1];
        linkTagArray = null;
        var metaArray = getMetaArray();
        var title = getTitle();
        var headsArray = getHeadsArray();
        var htmlList = getHtmlList();

        var returnArray = [linkList, htmlList, newLinkArray, metaArray, title, hreflangArray, canonicalArray, headsArray, linkArticle];
        return returnArray;
    }, linkList, params, iList);

    return returnArray;
}

module.exports = { evaluate };