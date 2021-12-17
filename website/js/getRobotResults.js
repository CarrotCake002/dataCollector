function copyLinksTable() {
    var copy = document.getElementById('allLinksTable');
    
    window.getSelection().selectAllChildren(copy);
    document.execCommand('Copy');
}

function createCsvFile(token, filename, dataFile) {
    $.ajax({
        method: "POST",
        type: "POST",
        url: '../files/createCsvFile.php',
        data: {
            'token': token,
            'filename': filename,
            'dataFile': dataFile
        },
        success: function (response) {
        },
        error: function (err) {
            alert("Error: couldn't create the CSV file.");
        }
    });
}