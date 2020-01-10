/*---------------------------------------------------------------------------------------------------------------------\
| Left Aside JavaScript                                                                                                |
\---------------------------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------\
| Active Link Highlighter                                                      |
\-----------------------------------------------------------------------------*/
window.asideLinkHighlighter = function(){

    /* Highlight aside links. */
    var links = document.querySelectorAll("#aside-left a[href^=\"/\"]");
    for(var i = links.length - 1; i >= 0; i--){

        /* Ignore links in headers. */
        if(typeof links[i].matches === "function" && links[i].matches("h1 a")) continue;

        /* Decode URIs. */
        var path = decodeURIComponent(window.location.pathname + window.location.hash);
        var href = decodeURIComponent(links[i].pathname + links[i].hash);

        /* Ignore scroll links. */
        if(links[i].hash) continue;

        /* Reset classes. */
        links[i].classList.remove("active");
        links[i].classList.remove("parent");

        /* Apply class if link path is equal. */
        if(href === path){
            links[i].classList.add("active");
            links[i].classList.remove("parent");
        }

        /* Apply class if link path starts with */
        else if(path.indexOf(href + "/") === 0){

            /* Do not highlight parents in the same list. */
            if(links[i].parentNode.parentNode.matches("ul")
            && links[i].parentNode.parentNode.querySelector("li > a.active")){
                continue;
            }

            links[i].classList.add("active");
            links[i].classList.add("parent");
        }
    }
}

// On DOM load.
window.listen("DOMContentLoaded", asideLinkHighlighter);
