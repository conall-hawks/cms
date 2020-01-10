/*---------------------------------------------------------------------------------------------------------------------\
| Globally-scoped JavaScript.                                                                                          |
\---------------------------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------\
| Collapsible List Auto-expand                                                 |
\-----------------------------------------------------------------------------*/
window.autoExpander = function(){
    var links = document.querySelectorAll(".collapsible a");
    for(var i = links.length - 1; i >= 0; i--){
        if(links[i].pathname === window.location.pathname || window.location.pathname.indexOf(links[i].pathname + "/") === 0){
            var checkbox = document.getElementById(links[i].parentNode.htmlFor);
            if(typeof checkbox === "object" && checkbox){
                checkbox.checked = true;
                checkbox = links[i].parentNode.parentNode.parentNode.querySelector("input[type=\"checkbox\"]");
                if(window.location.pathname.indexOf(document.querySelector("label[for=\"" + checkbox.id + "\"] a").getAttribute("href") + "/") === 0){
                    if(checkbox) checkbox.checked = true;
                }
                break;
            }
        }
    }
}

/*-----------------------------------------------------------------------------\
|                                                                              |
\-----------------------------------------------------------------------------*/
window.listen(["mousedown", "keydown"], function(event){
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(target.form && typeof target.matches === "function" && target.matches("input[type=\"reset\"]")){
            var inputs = target.form.querySelectorAll("[autofocus], input, select, textarea");
            for(var i = 0; i < inputs.length; i++){
                if(!inputs[i].matches("[autofocus], input[type=\"date\"], input[type=\"datetime-local\"], input[type=\"email\"], input[type=\"month\"], input[type=\"number\"], input[type=\"password\"], input[type=\"range\"], input[type=\"search\"], input[type=\"tel\"], input[type=\"text\"], input[type=\"time\"], input[type=\"url\"], input[type=\"week\"], select, textarea")) continue;
                if(inputs[i].offsetParent === null || inputs[i].disabled) continue;
                focusInput(inputs[i]);
            }
            return;
        }
    }
});



//var eventKeydownButton = function(event){
//    if(event.keyCode !== 13) return;
//    for(var target = event.target; target && target !== this; target = target.parentNode){
//        if(typeof target.matches === "function"
//        && !target.matches("input[type=\"button\"]")
//        && !target.matches("input[type=\"checkbox\"]")
//        && !target.matches("input[type=\"color\"]")
//        && !target.matches("input[type=\"image\"]")
//        && !target.matches("input[type=\"radio\"]")
//        && !target.matches("input[type=\"reset\"]")
//        ){
//            target.click();
//            return;
//        }
//    }
//};
//document.removeEventListener("keydown", eventKeydownButton);
//document.addEventListener("keydown", eventKeydownButton);
//


// focus-within polyfill test
/*(function(window, document){
    'use strict';
    var slice = [].slice;
    var removeClass = function(elem){
        elem.classList.remove('focus-within');
    };
    var update = (function(){
        var running, last;
        var action = function(){
            var element = document.activeElement;
            running = false;
            if(last !== element){
                last = element;
                slice.call(document.getElementsByClassName('focus-within')).forEach(removeClass);
                while(element && element.classList){
                    element.classList.add('focus-within');
                    element = element.parentNode;
                }
            }
        };
        return function(){
            if(!running){
                requestAnimationFrame(action);
                running = true;
            }
        };
    })();
    document.addEventListener('focus', update, true);
    document.addEventListener('blur', update, true);
    update();
})(window, document);
*/
