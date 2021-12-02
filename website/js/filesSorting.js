
function unclickAll() {
    document.getElementById('filterAll').checked = false;
    document.getElementById('filterJson').checked = false;
    document.getElementById('filterCsv').checked = false;
}

function showAll(fileList) {
    unclickAll();
    document.getElementById('filterAll').checked = true;
    for (i = 0; i < fileList.length; i++)
        document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
}

function showJson(fileList) {
    unclickAll();
    document.getElementById('filterJson').checked = true;
    for (i = 0; i < fileList.length; i++) {
        if (fileList[i].includes('.json'))
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
        else
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: none");
    }
}

function showCsv(fileList) {
    unclickAll();
    document.getElementById('filterCsv').checked = true;
    for (i = 0; i < fileList.length; i++) {
        if (fileList[i].includes('.csv'))
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
        else
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: none");
    }
}