
function unclickAll() {
    document.getElementById('filterAll').checked = false;
    document.getElementById('filterJson').checked = false;
    document.getElementById('filterCsv').checked = false;
}

function showAll() {
    unclickAll();
    document.getElementById('filterAll').checked = true;
}

function showJson () {
    unclickAll();
    document.getElementById('filterJson').checked = true;
}

function showCsv() {
    unclickAll();
    document.getElementById('filterCsv').checked = true;
}