function copyLinksTable() {
    var copy = document.getElementById('allLinksTable');
    
    window.getSelection().selectAllChildren(copy);
    document.execCommand('Copy');
}

function createCsvFile() {
    $.ajax({
        method: "POST",
        type: "POST",
        url: '../files/createCsvFile.php',
        data: {
            'token': "<?= $session->session_id ?>",
            'filename': "<?= $csvName ?>",
            'dataFile': "<?= basename($_FILES['openFile']['tmp_name']) ?>"
        },
        success: function (response) {
        },
        error: function (err) {
            alert("Error: couldn't create the CSV file.");
        }
    });
}