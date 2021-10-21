const fs = require('fs');

// function to write a string in the specified file. If the file doesn't exist it will be created
async function writeInFile(rootPath, string, savefile) {
    fs.writeFile(rootPath + "/../../savefiles/" + savefile + '.json', string, { flag: 'a+' }, (err) => {
    });
}

module.exports = { writeInFile };