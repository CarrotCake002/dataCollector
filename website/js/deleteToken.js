function stopRobot(filename) {
    $.ajax({
        method: "GET",
        type: "GET",
        url: '../files/stopRobot.php',
        data: {
            'filename': filename,
        },
        success: function (response) {
        },
        error: function (err) {
            alert("Error: couldn't create the CSV file.");
        }
    });
}