const fs = require('fs');

// function to write a string in the specified file. If the file doesn't exist it will be created
async function writeInFile(string, savefile) {
    fs.writeFile('./../savefiles/' + savefile + '.json', string, { flag: 'a+' }, (err) => {
    });
}

module.exports = { writeInFile };