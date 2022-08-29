$(function() {
    let title =
        $("title")
            .last()
            .text();

    if (title.length > 0)
        $("#nav-subtitle").html(title);

    window.document.title = title;
});

$(function() {
    fnDetachHeader = function()
    {
        $("#app-header").addClass("fixed-top");
        $("#app-body").css("padding-top", $("#app-header").height() + "px");
    };
});