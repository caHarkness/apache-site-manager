$(function() {
    // navto.js - a library for handling links that aren't "a hrefs"

    navtoInit = function()
    {
        navtoPause = false;

        $("*[navto]").each(function() {
            var container = $(this);
            var endpoint = container.attr("navto");

            container.addClass("clickable");
            
            container.on("click", function(e) {
                if(e.target !== e.currentTarget)
                {
                    if (e.target.tagName == "A")
                        return;
                }

                if (navtoPause)
                    return;

                // navtoPause = true;

                container
                    .hide()
                    .fadeIn(500);

                setTimeout(function() {
                    window.location = endpoint;
                }, 750);
                
                e.stopPropagation();
            });
        });
    };
});