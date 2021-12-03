
function unclickAll() {
    document.getElementById('filterAll').checked = false;
    document.getElementById('filter_json').checked = false;
    document.getElementById('filter_csv').checked = false;
}

function showAll(fileList) {
    unclickAll();
    document.getElementById('filterAll').checked = true;
    for (i = 0; i < fileList.length; i++)
        document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
}

function showFiletype(filetype, fileList) {
    unclickAll();
    document.getElementById('filter_' + filetype).checked = true;
    for (i = 0; i < fileList.length; i++) {
        if (fileList[i].includes('.' + filetype))
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: table-row;");
        else
            document.getElementById('rowFile' + fileList[i]).setAttribute("style", "display: none");
    }
}
