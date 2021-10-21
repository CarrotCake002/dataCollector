
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

module.exports = { options, iteration, link, errorData, errorStatus, success };