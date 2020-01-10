/*-----------------------------------------------------------------------------\
| CSS Variables Polyfill                                                       |
|                                                                              |
| Enables support for browsers which do not support CSS variables.             |
+------------------------------------------------------------------------------+
| Author: Jon Hawks                                                            |
| Version: 1.3                                                                 |
\-----------------------------------------------------------------------------*/
window.cssVars = function(){
    if(
        typeof window.CSS          !== "function"    ||
        typeof window.CSS.supports !== "function"    ||
        !window.CSS.supports("(--property: value)")  ||
        navigator.userAgent.indexOf("Edge/")    > -1 ||
        navigator.userAgent.indexOf("MSIE ")    > -1 ||
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
            //replacement.innerHTML = linkElements[i].sheet.cssText;
            replacement.sheet.cssText = linkElements[i].sheet.cssText;
            linkElements[i].parentNode.replaceChild(replacement, linkElements[i]);
        }

        /* Get all of the inline stylesheets. */
        var styleElements = document.getElementsByTagName("style");

        /* Find CSS variables. */
        var foundVars = {};
        for(var i = styleElements.length - 1; i >= 0; i--){
            if(typeof styleElements[i].innerHTML !== "string") continue;
            foundVars[i] = styleElements[i].innerHTML.match(/((--([\w-]+)\s*):\s*([^;}]+|(?:.*\)[^;]*))\s*;(?![^(]*\)))/g);
        }

        /* Parse CSS variables. */
        if(typeof cssVars.parsedVars !== "object") cssVars.parsedVars = {};
        var parsedVars = cssVars.parsedVars;
        for(var i in foundVars){
            if(typeof foundVars[i] !== "object" || !foundVars[i]) continue;
            for(var j = foundVars[i].length - 1; j >= 0; j--){
                var curVar = foundVars[i][j].split(/:(?![^(]*\))/);
                if(typeof curVar[1] === "string"){
                    parsedVars[curVar[0]] = curVar[1].split(/;(?![^(]*\))/)[0].trim();
                }
            }
        }

        /* Replace CSS variables. */
        for(var i in styleElements){
            if(typeof styleElements[i].innerHTML !== "string") continue;
            for(var j in parsedVars){
                if(typeof parsedVars[j] !== "string") continue;
                var regex = new RegExp("var\\(\\s*" + j + "\\s*\\)", "g");
                styleElements[i].innerHTML = styleElements[i].innerHTML.replace(regex, parsedVars[j]);
            }
        }
    }
}

/* Page load events. */
if(window.addEventListener) window.addEventListener("load", cssVars);
else if(window.attachEvent) window.attachEvent("onload", cssVars);
