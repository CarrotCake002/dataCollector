function changeStatusColor(fileNb, status) {
    if (status === "Active") {
        document.getElementById("fileStatus" + fileNb).style.color = "blue";
    } else if (status === "Stopped") {
        document.getElementById("fileStatus" + fileNb).style.color = "red";
    } else if (status === "Finished") {
        document.getElementById("fileStatus" + fileNb).style.color = "#31d313";
    } else {
        document.getElementById("fileStatus" + fileNb).style.color = "black";
    }
}

function deleteAllStoppedFiles(token, filetype = "") {
    if (!confirm("Are you sure you want to delete all stopped files?"))
        return false;
    return $.ajax({
        method: "POST",
        type: "POST",
        url: 'deleteAllStoppedFiles.php',
        data: {
            'token': token,
            'filetype': filetype
        },
        success: function (response) {
            location.reload();
        },
        error: function () {
            alert("There was an error deleting the files. Please try again later.");
        }
    });
}

function deleteFile(fileNb, token) {
    $.ajax({
        method: "POST",
        type: "POST",
        url: 'deleteFile.php',
        data: {
            'fileNb' : fileNb,
            'token': token
        },
        success: function (response) {
            location.reload();
        },
        error: function () {
            alert("There was an error deleting the file. Please try again later.");
        }
    });
}

function getCurrentFilter() {
    if (document.getElementById('filter_json').checked === true)
        return "json";
    else if (document.getElementById('filter_csv').checked === true)
        return "csv";
}

function unclickAll(fileList) {
    document.getElementById('filterAll').checked = false;
    document.getElementById('filter_json').checked = false;
    document.getElementById('filter_csv').checked = false;
    for (i = 0; i < fileList.length; i++) {
        document.getElementById('rowFile' + fileList[i]).setAttribute("bgcolor", "#FFFFFF");
    }
}

function showAll(fileList, fileSizeList) {
    totalSize = 0;
    pair = false;

    unclickAll(fileList);
    document.getElementById('filterAll').checked = true;
    for (i = 0; i < fileList.length; i++) {
        document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
        if (pair === false) {
            document.getElementById('rowFile' + fileList[i]).setAttribute("bgcolor", "#E3E3E3");
            pair = true;
        } else
            pair = false;
        totalSize += parseFloat(fileSizeList[i]);
    }
    totalSize = totalSize.toFixed(3);
    document.getElementById('totalFileSize').innerHTML = '<b>' + totalSize.toString() + ' kB</b>';
}

function showFiletype(filetype, fileList, fileSizeList) {
    totalSize = 0;
    pair = false;

    unclickAll(fileList);    
    document.getElementById('filter_' + filetype).checked = true;
    for (i = 0; i < fileList.length; i++) {
        if (fileList[i].includes('.' + filetype)) {
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
            totalSize += parseFloat(fileSizeList[i]);
            if (pair === false) {
                document.getElementById('rowFile' + fileList[i]).setAttribute("bgcolor", "#E3E3E3");
                pair = true;
            } else
                pair = false;
        }
        else
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: none");
    }
    totalSize = totalSize.toFixed(3);
    document.getElementById('totalFileSize').innerHTML = '<b>' + totalSize.toString() + ' kB</b>';
}

function generateAllCsvFiles(token, fileList) {
    var csvFilename = "";
    
    for (i = 0; i < fileList.length; i++) {
        if (fileList[i].includes(".json") === true) { // also need to check if the file is Finished, or do I?
            csvFilename = fileList[i].replace(".json", ".csv")
            createCsvFromFile(token, csvFilename, fileList[i]);
        }
    }
    location.reload();
}

function createCsvFromFile(token, filename, dataFile) {
    $.ajax({
        method: "POST",
        type: "POST",
        url: '../files/createCsvFile.php',
        data: {
            'token': token,
            'filename': filename,
            'dataFile': dataFile
        },
        success: function (response) {},
        error: function (err) {}
    });
}