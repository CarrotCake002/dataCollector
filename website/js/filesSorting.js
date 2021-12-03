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

function unclickAll() {
    document.getElementById('filterAll').checked = false;
    document.getElementById('filter_json').checked = false;
    document.getElementById('filter_csv').checked = false;
}

function showAll(fileList, fileSizeList) {
    totalSize = 0;

    unclickAll();
    document.getElementById('filterAll').checked = true;
    for (i = 0; i < fileList.length; i++) {
        document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
        totalSize += parseFloat(fileSizeList[i]);
    }
    console.log(totalSize.toString() + ' kB');
    document.getElementById('totalFileSize').value = totalSize.toString() + ' kB';
}

function showFiletype(filetype, fileList, fileSizeList) {
    totalSize = 0;

    unclickAll();    
    document.getElementById('filter_' + filetype).checked = true;
    for (i = 0; i < fileList.length; i++) {
        if (fileList[i].includes('.' + filetype)) {
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
            totalSize += parseFloat(fileSizeList[i]);
        }
        else
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: none");
    }
    console.log(totalSize.toString() + ' kB');
    document.getElementById('totalFileSize').innerHTML = totalSize.toString() + ' kB';
}
