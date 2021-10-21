
function help() {
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
}

function any(arg) {
    console.log(arg);
}

function options() {
    console.log("Execute with --help to view all valid options.");
}

function iteration(iList) {
    console.log("Iteration " + iList);
}

function link(link) {
    console.log(link + "\n");
}

function errorData() {
    console.log("Error: something unexpected happened when collecting all the data from the current website.");
}

function errorStatus() {
    console.log("Error: the current url cannot be scraped. Please add the corresponding filters.");
}

function success() {
    console.log("Info: the program has sucessfully obtained all the links it could!");
}

module.exports = { help, options, iteration, link, errorData, errorStatus, success, any };