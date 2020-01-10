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
