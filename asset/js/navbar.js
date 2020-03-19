/*---------------------------------------------------------------------------------------------------------------------\
| Navbar JavaScript.                                                                                                   |
\---------------------------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------\
| Only show the navbar if we need it.                                          |
\-----------------------------------------------------------------------------*/
var navbar = function(){
    var header = document.getElementById("header");
    var navbar = document.getElementById("navbar");
    if(window.pageYOffset > header.offsetHeight){
        navbar.hidden = false;
    }else if(!navbar.hidden){
        navbar.hidden = true;
    }
}

// Navbar event; throttle prevents lag.
window.removeEventListener("scroll", throttle(navbar, 100));
window.addEventListener("scroll", throttle(navbar, 100));

/*-----------------------------------------------------------------------------\
| Detect caps lock.                                                            |
\-----------------------------------------------------------------------------*/
var eventCapsLock = function(event){
    var notice = document.getElementById("navbar-capslock-notice");
    if(!notice) return;
    notice.style.display = "none";
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#navbar input[type=\"password\"]")){
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

/* ########################################################################## */
/* #### Navbar alternative login form. ###################################### */
/* ########################################################################## */
document.addEventListener("keydown", function(event){
    if(event.keyCode === 13) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches !== "function") return;
        if(target.matches("#login-username")){
            setTimeout(function(){
                document.getElementById("navbar-login-username").value = target.value;
            }, 1);
            return;
        }else if(target.matches("#login-password")){
            setTimeout(function(){
                document.getElementById("navbar-login-password").value = target.value;
            }, 1);
            return;
        }else if(target.matches("#login-remember-me")){
            setTimeout(function(){
                var remember = document.getElementById("navbar-login-remember-me");
                if(!remember.checked && target.checked) remember.click();
            }, 1);
            return;
        }else if(target.matches("#navbar-login-username")){
            setTimeout(function(){
                document.getElementById("login-username").value = target.value;
            }, 1);
            return;
        }else if(target.matches("#navbar-login-password")){
            setTimeout(function(){
                document.getElementById("login-password").value = target.value;
            }, 1);
            return;
        }else if(target.matches("#navbar-login-remember-me")){
            setTimeout(function(){
                var remember = document.getElementById("login-remember-me");
                if(!remember.checked && target.checked) remember.click();
            }, 1);
            return;
        }
    }
});

document.addEventListener("click", function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches !== "function") return;
        if(target.matches("#login-remember-me")){
            setTimeout(function(){
                var remember = document.getElementById("navbar-login-remember-me");
                if(remember.checked !== target.checked) remember.click();
            }, 1);
            return;
        }else if(target.matches("#navbar-login-remember-me")){
            setTimeout(function(){
                var remember = document.getElementById("login-remember-me");
                if(remember.checked !== target.checked) remember.click();
            }, 1);
            return;
        }
    }
});

/*-----------------------------------------------------------------------------\
| Glass for browsers which don't support backdrop-filter.                      |
\-----------------------------------------------------------------------------*/
if(!CSS.supports("backdrop-filter", "blur(2px)") && !CSS.supports("-webkit-backdrop-filter", "blur(2px)")){
    function glass(source, target, filter, scroller){

        /* Build iframe. */
        var iframe = document.createElement("iframe");
        iframe.style.border        = "none";
        iframe.style.height        = "100%";
        iframe.style.left          = "0";
        iframe.style.overflow      = "hidden";
        iframe.style.pointerEvents = "none";
        iframe.style.position      = "absolute";
        iframe.style.top           = "0";
        iframe.style.width         = "100%";
        iframe.style.zIndex        = target.style.zIndex - 2;
        target.appendChild(iframe);

        /* Add page elements to iframe; delay by 1 sec to wait for iframe to initialize. */
        setTimeout(function(){

            /* Add head (scripts only). */
            var scripts = source.ownerDocument.head.getElementsByTagName("script");
            for(var i = 0; i < scripts.length; i++){
                var script = document.createElement("script");
                script.appendChild(document.createTextNode(scripts[i].innerHTML));
                if(scripts[i].getAttribute("async")) script.setAttribute("async", scripts[i].getAttribute("async"));
                if(scripts[i].getAttribute("id"))    script.setAttribute("id", scripts[i].getAttribute("id"));
                if(scripts[i].getAttribute("nonce")) script.setAttribute("nonce", scripts[i].getAttribute("nonce"));
                if(scripts[i].getAttribute("src"))   script.setAttribute("src", scripts[i].getAttribute("src"));
                iframe.contentDocument.head.appendChild(script);
            }

            /* Add body. */
            iframe.contentDocument.body.innerHTML = source.innerHTML;

            /* Add a blur to the entire iframe. */
            var style = document.createElement("style");
            style.innerHTML = "html {\
                filter:      " + filter + ";\
                height:      " + source.scrollHeight + "px;\
                overflow-y:  hidden;\
            }";
            iframe.contentDocument.body.appendChild(style);

            /* Remove target from iframe. */
            var headers = iframe.contentDocument.body.getElementsByTagName(target.tagName);
            for(var i = 0; i < headers.length; i++){
                if(headers[i].isEqualNode(target)){
                    headers[i].parentNode.removeChild(headers[i]);
                }
            }

            /* Remove fixed elements. */
            iframe.contentDocument.body.querySelector("#footer").style.display = "none";

            /* Repair target's background we lost due to embedding an iframe. */
            //var div = document.createElement("div");
            //div.style.backgroundColor = window.getComputedStyle(target, null).getPropertyValue("background-color");
            //div.style.height          = "100%";
            //div.style.left            = "0";
            //div.style.position        = "absolute";
            //div.style.top             = "0";
            //div.style.width           = "100%";
            //div.style.zIndex          = target.style.zIndex - 1;
            //target.appendChild(div);

            /* Synchronize iframe scroll with page. */
            iframe.contentWindow.scrollTo(window.scrollX, window.scrollY - target.getBoundingClientRect().top);
        }, 1);
    }
    glass(document.body, document.querySelector("#navbar"), "blur(2px)");

    /* Maintain synchronized iframe scroll with page. */
    window.addEventListener("scroll", function(event){
        document.querySelector("#navbar > iframe").contentWindow.scrollTo(window.scrollX, window.scrollY - document.querySelector("#navbar").getBoundingClientRect().top);
    }, true);
}
