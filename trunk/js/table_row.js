// when the dom is ready...
$(function() {

    var i = 0;

    $("td").hover(function() {
    
        $(this).parent().addClass("hover");
        var curCol = $(this).attr("rel");
        $("#"+curCol).addClass("hover");
    
    }, function() {
    
        $(this).parent().removeClass("hover");
        var curCol = $(this).attr("rel");
        $("#"+curCol).removeClass("hover");
    
    });

});