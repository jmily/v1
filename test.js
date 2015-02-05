$(function() {

    var json = [
        {
            "date": "2015-01-26",
            "time": 569877,
            "xCoord": 47.5,
            "yCoord": -136.912,
            "sensorId": "3",
            "values": {
                "smoke": 43.65,
                "batery": 23.532
            }
        },
        {
            "date": "2015-01-29",
            "time": 569877,
            "xCoord": 37.5,
            "yCoord": -135.998,
            "sensorId  ": "4",
            "values": {
                "temperature": 20.32,
                "batery": 543252.342,
                "humidity": 43.6
            }
        }
    ];



    $.ajax({
        type : 'POST',
        url : 'test_ajax.php',
        dataType : 'json',
        data:
        {
            json: json
        },
        success : function(data)
        {

        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
});