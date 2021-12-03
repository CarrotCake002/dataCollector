
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
