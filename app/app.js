/**
 * Created by marten.reinlaender on 18.01.2017.
 */
CreateTable = function(id, api_url){
    var containingElement  = $("#" + id);

    $.ajax({
       type : 'Get',
        url: api_url,
        success: renderAndAppendItems(containingElement, data),
        error: window.location.replace("\html\error.html")
    });
}

renderAndAppendItems = function (containingElement, data) {

    data.each(function () {
    })
}