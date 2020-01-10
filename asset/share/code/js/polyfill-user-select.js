/*-----------------------------------------------------------------------------\
| CSS user-select Polyfill                                                     |
|                                                                              |
| Enables support for browsers which do not support CSS' user-select attribute.|
+------------------------------------------------------------------------------+
| Author: Jon Hawks                                                            |
| Version: 1.0                                                                 |
\-----------------------------------------------------------------------------*/
window.userSelect = function(){
    if(
        typeof window.CSS !== "function"            ||
        typeof window.CSS.supports !== "function"   ||
        !window.CSS.supports("(user-select: none)") ||
        navigator.userAgent.indexOf("Edge/") > -1   ||
        navigator.userAgent.indexOf("MSIE ") > -1   ||
        navigator.userAgent.indexOf("Trident/") > -1
    ){
        /* Convert external stylesheets to inline. */
        var linkElements = document.querySelectorAll("link[rel='stylesheet']");
        for(var i = 0; i < linkElements.length; i++){
            if(
                typeof linkElements[i].sheet         !== "object" ||
                linkElements[i].sheet                === null     ||
                typeof linkElements[i].sheet.cssText !== "string"
            ) continue;
            var replacement = document.createElement("style");
            replacement.sheet.cssText = linkElements[i].sheet.cssText;
            linkElements[i].parentNode.replaceChild(replacement, linkElements[i]);
        }

        /* Get all of the inline stylesheets. */
        var styleElements = document.getElementsByTagName("style");

        /* Replace user-select. */
        for(var i in styleElements){
            if(typeof styleElements[i].innerHTML !== "string") continue;
            var replaceText = "-moz-user-select: $1;"    +
                              "-ms-user-select: $1;"     +
                              "-webkit-user-select: $1;" +
                              "user-select: $1;";
            styleElements[i].innerHTML = styleElements[i].innerHTML.replace(/user-select\s*:\s*([^;}]+|(?:.*\)[^;]*))\s*;*}*/g, replaceText);
        }
    }
}

/* Page load events. */
if(window.addEventListener) window.addEventListener("load", userSelect);
else if(window.attachEvent) window.attachEvent("onload", userSelect);
