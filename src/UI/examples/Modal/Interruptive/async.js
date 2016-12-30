il.Async.get = function(url, callback){
    $.ajax({
        type: "GET",
        url: url,
    }).done(function (return_data) {
        if (return_data.success) {
            $("body").append(return_data.content);
            if (callback instanceof Function) {
                //This would be probably the show action of the Modal
                callback();
            }
        }
        else {
            //Some error processing
    }});
}
