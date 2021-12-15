function copyDetailsTable() {
    var copy = document.getElementById('details_table');

    window.getSelection().selectAllChildren(copy);
    document.execCommand('Copy');
}