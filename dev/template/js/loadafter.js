$(function() {
    // loadafter.js - a library for loading stuff "after"

    $("*[la]").each(function() {
        var container = $(this);
        var endpoint = container.attr("la");

        // Make the container have a spinner
        container.html("<i class=\"fa fa-spinner fa-pulse fa-fw\"></i>");

        $.get(endpoint, function(data) {
            container.html(data);
        });
    });
});