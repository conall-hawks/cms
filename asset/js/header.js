/*-----------------------------------------------------------------------------\
| Active Link Highlighter                                                      |
\-----------------------------------------------------------------------------*/
window.headerLinkHighlighter = function(){

    /* Highlight aside links. */
    var links = document.querySelectorAll("#header a[href^=\"/\"]");
    for(var i = links.length - 1; i >= 0; i--){

        /* Decode URIs. */
        var path = decodeURIComponent(window.location.pathname + window.location.hash);
        var href = decodeURIComponent(links[i].pathname + links[i].hash);

        /* Ignore scroll links. */
        if(links[i].hash) continue;

        /* Target element. */
        if(links[i].parentNode.nodeName === "DIV"){
            target = links[i].parentNode;
        }else{
            target = links[i];
        }

        /* Reset classes. */
        target.classList.remove("active");
        target.classList.remove("parent");

        /* Apply class if link path is equal. */
        if(href === path){
            target.classList.add("active");
            target.classList.remove("parent");
        }

        /* Apply class if link path starts with */
        else if(path.indexOf(href + "/") === 0){
            target.classList.add("active");
            target.classList.add("parent");
        }
    }
}

/*-----------------------------------------------------------------------------\
| Toggle particles!                                                            |
\-----------------------------------------------------------------------------*/
document.addEventListener("click", function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#katamari")){
            particlesToggle();
            return;
        }
    }
});

/*-----------------------------------------------------------------------------\
| Detect caps lock.                                                            |
\-----------------------------------------------------------------------------*/
var eventCapsLock = function(event){
    var notice = document.getElementById("login-capslock-notice");
    if(!notice) return;
    notice.style.display = "none";
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#login input[type=\"password\"]")){
            if(typeof event.getModifierState === "function" && event.getModifierState("CapsLock")){
                notice.style.display = "block";
            }
            return;
        }
    }
}
document.removeEventListener("click", eventCapsLock);
document.addEventListener("click", eventCapsLock);
document.removeEventListener("keyup", eventCapsLock);
document.addEventListener("keyup", eventCapsLock);

/*-----------------------------------------------------------------------------\
| Nifty flickering effect.                                                     |
\-----------------------------------------------------------------------------*/
var flicker = function(loop){
    var elements = document.querySelectorAll("#header td > div a");
    var index = Math.floor(Math.random() * elements.length);
    elements[index].style.animation = ".125s electrify";
    setTimeout(function(){elements[index].style.animation = null}, 125);
    if(loop === true){
        var wait = Math.floor(Math.random() * 8000) + 2000;
        setTimeout(function(){flicker(true)}, wait);
        if(Math.random() >= 0.75) setTimeout(flicker, wait + Math.floor(Math.random() * 50));
    }
}

// On DOM load.
window.listen("DOMContentLoaded", function(){flicker(true)});
