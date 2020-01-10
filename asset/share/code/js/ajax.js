/*-----------------------------------------------------------------------------\
| Requests a new page (or submits a <form>) via AJAX and updates the UI.       |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     <a class="ajax" href="/">AJAX Link</a>                                   |
|     <form class="ajax">Form Stuff></form>                                    |
|                                                                              |
| Result:                                                                      |
|     Clicking the link or submitting a form will update elements with an ID   |
|     of "header", "content", "aside", or "footer".                            |
+---------+-----------+----------+---------------------------------------------|
| @param  | string    | link     | The URL of the requested page.              |
| @param  | string    | scrollY  | How far down the page to scroll.            |
| @param  | string    | method   | HTTP method to use.                         |
| @param  | string    | params   | Parameters for a POST.                      |
| @param  | object    | params   | Form element; use to auto-build POSTs.      |
| @return | boolean   |          |                                             |
\---------+-----------+----------+--------------------------------------------*/
function loadPage(link, scrollY, method, params){

    /* Cancel any currently pending requests. */
    if(typeof loadPage.xhr === "object" && loadPage.xhr.readyState !== loadPage.xhr.DONE){
        if(loadPage.fileUpload && confirm("Cancel file upload?")) return;
        loadPage.fileUpload = false;
        loadPage.xhr.abort();
        delete loadPage.xhr;
    }

    /* Default method. */
    if(typeof method !== "string" || ["GET", "POST"].indexOf(method) < 0) method = "GET";

    /* Construct parameters string from a POSTed <form>. */
    if(method === "POST" && typeof params === "object"){
        var form = new FormData();
        loadPage.fileUpload = false;
        for(var i = 0; i < params.length; i++){

            /* Skip non-<input> elements. */
            if(typeof params[i].matches !== "function" || !params[i].matches("input")) continue;

            /* Input required validation. */
            if(params[i].required && !params[i].value){
                params[i].style.transition = "";
                params[i].style.boxShadow = "var(--box-shadow-dark), 0 0 2px red inset";
                params[i].style.color = "red";
                setTimeout(function(){
                    try{focusInput(params[i])}catch(error){console.error(error)}
                    params[i].style.transition = "1s box-shadow, 1s color";
                    params[i].style.boxShadow = "";
                    params[i].style.color = "";
                }, 10);
                return false;
            }

            /* Input pattern validation. */
            if(params[i].pattern && !RegExp(params[i].pattern).test(params[i].value)){
                params[i].style.transition = "";
                params[i].style.boxShadow = "var(--box-shadow-dark), 0 0 2px red inset";
                params[i].style.color = "red";
                setTimeout(function(){
                    try{focusInput(params[i])}catch(error){console.error(error)}
                    params[i].style.transition = "1s box-shadow, 1s color";
                    params[i].style.boxShadow = "";
                    params[i].style.color = "";
                }, 10);
                return false;
            }

            /* Append file upload. */
            if(params[i].type === "file"){
                if(params[i].multiple){
                    for(var j = 0; j < params[i].files.length; j++){
                        form.append(params[i].name + "_" + j, params[i].files[j]);
                    }
                }else{
                    form.append(params[i].name, params[i].files[0]);
                }
                loadPage.fileUpload = true;
            }

            /* Append parameter. */
            else{
                if(params[i].name) form.append(params[i].name, params[i].value);
            }
        }

        /* Overwrite params argument. */
        params = form;
    }

    /* Prepare a new XMLHTTPRequest. */
    loadPage.xhr = new XMLHttpRequest();
    loadPage.xhr.open(method, link);
    loadPage.xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    /* Feedback in <title>. */
    try{document.title = "Loading... | " + document.title.split(" | ")[1];}catch(error){console.error(error)}
    try{document.getElementById("loading-notify").innerHTML = "&nbsp;- Loading..."}catch(error){console.error(error)}

    /* Callback on completion. */
    loadPage.xhr.onreadystatechange = function(){
        switch(this.readyState){

            /*-----------------------------------------------------------------\
            | Headers are received.                                            |
            \-----------------------------------------------------------------*/
            case this.HEADERS_RECEIVED:

                // Special processing for file downloads.
                var contentDisposition = this.getResponseHeader("Content-Disposition");
                if(typeof contentDisposition === "string" && contentDisposition.indexOf("attachment") !== -1 && this.responseType !== "blob"){
                    loadPage.xhr.responseType = "blob";
                }
                break;

            /*-----------------------------------------------------------------\
            | Finished loading.                                                |
            \-----------------------------------------------------------------*/
            case this.DONE:

                /* If redirected, update address bar. */
                if(this.responseURL && this.responseURL.split("#")[0] !== window.location.href.split("#")[0]){
                    window.history.replaceState({pageYOffset: window.pageYOffset, pathname: target.pathname}, null, window.location.href);
                    window.history.pushState(null, null, this.responseURL);
                }

                /* Special processing for file downloads. */
                var contentDisposition = this.getResponseHeader("Content-Disposition");
                if(typeof contentDisposition === "string" && contentDisposition.indexOf("attachment") !== -1 && this.responseType === "blob"){
                    var a = document.createElement("a");
                    a.download = trim(trim(contentDisposition.split("filename=")[1], "\""), "'");
                    a.href = window.URL.createObjectURL(this.response);
                    a.dispatchEvent(new MouseEvent("click"));
                    loadPage(link, scrollY);
                    return;
                }

                /* Select response. */
                var response = document.createElement("html");
                response.innerHTML = this.responseText;

                /* Response preprocessing; Can be used to preserve some elements. */
                try{
                    var newClock = response.querySelector("#clock");
                    var oldClock = document.getElementById("clock");
                    newClock.parentNode.replaceChild(oldClock, newClock);
                }catch(error){console.error(error)}
                try{
                    var newChat = response.querySelector("label[for=\"chat\"]");
                    var oldChat = document.querySelector("label[for=\"chat\"]");
                    newChat.parentNode.replaceChild(oldChat, newChat);
                }catch(error){console.error(error)}
                try{
                    var logOpen = document.getElementById("log-toggle").checked;
                }catch(error){console.error(error)}

                /* Replace entire page. */
                //document.documentElement.parentNode.replaceChild(response, document.documentElement);

                /* Insert metadata. */
                var meta = response.getElementsByTagName("meta");
                var oldMeta = document.getElementsByTagName("meta");
                for(var i = meta.length - 1; i >= 0; i--){
                    for(var j = oldMeta.length - 1; j >= 0; j--){
                        if(meta[i].name      && meta[i].name      === oldMeta[j].name
                        || meta[i].httpEquiv && meta[i].httpEquiv === oldMeta[j].httpEquiv
                        || meta[i].charset
                        ){
                            if(meta[i].content !== oldMeta[j].content
                            || meta[i].charset !== oldMeta[j].charset
                            ){
                                try{oldMeta[j].parentNode.replaceChild(meta[i], oldMeta[j])}catch(error){console.error(error)}
                            }
                            break;
                        }
                    }
                }
                var base = response.getElementsByTagName("base");
                var oldBase = document.getElementsByTagName("base");
                try{oldBase[0].parentNode.replaceChild(base[0], oldBase[0])}catch(error){console.error(error)}

                /* Insert response. */
                var elements = ["style", "header", "content", "aside-left", "aside-left-2", "aside-right", "aside-right-2", "footer", "navbar"];
                for(var i = elements.length - 1; i >= 0; i--){
                    var replacement = response.querySelector("#" + elements[i]);
                    if(replacement){
                        var incumbent = document.getElementById(elements[i]);

                        /* Ignore unchanged elements. */
                        if(incumbent.innerHTML === replacement.innerHTML) continue;

                        /* Replace element. */
                        replacement = replacement.cloneNode(true);
                        incumbent.parentNode.replaceChild(replacement, incumbent);
                    }
                }

                /* Best-attempt insert malformed response. */
                if(!response.querySelector("#content")){
                    console.warn("Received malformed response.");

                    // Highlight header link.
                    try{headerLinkHighlighter()}catch(error){console.error(error)}

                    var content = document.getElementById("content");
                    if(content) content.innerHTML = "<article><p>" + response.innerHTML + "</p></article>";

                    var aside = document.getElementById("aside-left");
                    if(aside) aside.innerHTML = "";

                    try{document.title = document.title.split(" | ")[0] + " | "}catch(error){console.error(error)}
                    try{document.getElementById("loading-notify").innerHTML = ""}catch(error){console.error(error)}
                }

                /* Get CSP nonce if applicable. */
                var nonce = "";
                var scripts = document.querySelectorAll("#script script[nonce]");
                for(var i = scripts.length - 1; i >= 0; i--){
                    nonce = scripts[i].getAttribute("nonce");
                    if(nonce) break;
                }

                /* Execute any embedded scripts. */
                var content = document.getElementById("content");
                for(var i = elements.length - 1; i >= 0; i--){
                    var section = document.getElementById(elements[i]);
                    if(section){
                        var script = section.getElementsByTagName("script");
                        for(var j = script.length - 1; j >= 0; j--){
                            var replacement = document.createElement("script");
                            replacement.appendChild(document.createTextNode(script[j].innerHTML));
                            if(script[j].getAttribute("id"))    replacement.setAttribute("id", script[j].getAttribute("id"));
                            if(script[j].getAttribute("async")) replacement.setAttribute("async", script[j].getAttribute("async"));
                            if(script[j].getAttribute("src"))   replacement.setAttribute("src", script[j].getAttribute("src"));
                            replacement.setAttribute("nonce", nonce);
                            content.appendChild(replacement);
                        }
                    }
                }

                /* Response post-processing; miscellaneous tasks. */
                loadPage.fileUpload = false;
                try{document.title = response.getElementsByTagName("title")[0].innerHTML}catch(error){document.title = window.location.host}
                try{
                    var log    = document.getElementById("log");
                    var newLog = response.querySelector("#log");
                    if(typeof log === "object" && typeof newLog === "object"){
                        log.innerHTML = newLog.innerHTML;
                        log.scrollTop = log.scrollHeight;
                        if(logOpen) document.getElementById("log-toggle").checked = true;
                    }
                }catch(error){console.error(error)}
                try{autoExpander()}catch(error){console.error(error)}
                try{asideLinkHighlighter("aside-left")}catch(error){console.error(error)}
                try{setTimeout(cssVars, 1000)}catch(error){console.error(error)}
                try{setTimeout(userSelect, 2000)}catch(error){console.error(error)}
                try{
                    var path = new URL(window.location.href);
                    ga("send", "pageview", path.pathname + path.search + path.hash);
                }catch(error){console.error(error)}
                try{adsbygoogle.push({})}catch(error){console.error(error)}
                try{
                    var oldScript = document.querySelectorAll("#script script");
                    for(var i = oldScript.length - 1; i >= 0; i--) {
                        var script = response.querySelector("#" + oldScript[i].id);
                        if(!script) oldScript[i].parentNode.removeChild(oldScript[i]);
                    }

                    var script = response.querySelectorAll("#script script");
                    for(var i = script.length - 1; i >= 0; i--){
                        var replacement = document.createElement("script");
                        replacement.appendChild(document.createTextNode(script[i].innerHTML));
                        if(script[i].getAttribute("id"))    replacement.setAttribute("id", script[i].getAttribute("id"));
                        if(script[i].getAttribute("async")) replacement.setAttribute("async", script[i].getAttribute("async"));
                        if(script[i].getAttribute("src"))   replacement.setAttribute("src", script[i].getAttribute("src"));
                        replacement.setAttribute("nonce", nonce);
                        var oldScript = document.querySelector("#script #" + script[i].id);
                        if(!oldScript){
                            document.getElementById("script").appendChild(replacement);
                        }else if(oldScript.innerHTML !== script[i].innerHTML){
                            oldScript.parentNode.replaceChild(oldScript, replacement);
                        }

                    }
                }catch(error){console.error(error)}
                try{
                    autofocus = document.querySelectorAll("input[autofocus]");
                    focusInput(autofocus[autofocus.length - 1]);
                }catch(error){console.error(error)}
                //try{
                //    var id = document.getElementById(window.location.hash.replace(/^#+/, ""));
                //    if(id && !scrollY) scrollY = id.offsetTop - 58;
                //    window.scrollTo(0, scrollY)}catch(error){console.error(error)}
                //try{if(typeof StyleFix === "object") StyleFix.process()}catch(error){console.error(error)}
                break;

            /*-----------------------------------------------------------------\
            | Unknown state.                                                   |
            \-----------------------------------------------------------------*/
            default:
                // Do nothing.
        }
    };

    /* Callback during progress. */
    loadPage.xhr.onprogress = function(event){
        if(event.lengthComputable){
            try{document.title = "Loading: " + (event.loaded / event.total).toFixed(2) + "% | " + document.title.split(" | ")[1]}catch(error){console.error(error)}
            try{document.getElementById("loading-notify").innerHTML = "&nbsp;- Loading: " + (event.loaded / event.total).toFixed(2) + "%"}catch(error){console.error(error)}
        }
    };

    /* Execute the request. */
    loadPage.xhr.send(params);
}

/*-----------------------------------------------------------------------------\
| When links in the "ajax" class are clicked, load the page via AJAX.          |
\-----------------------------------------------------------------------------*/
var ajaxEventMousedownLink = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && (target.matches("a.ajax") || target.matches("a[href^=\"#\"]"))){
            event.preventDefault();
            if(window.location.pathname !== target.pathname){
                window.history.replaceState({pageYOffset: window.pageYOffset, pathname: target.pathname}, null, window.location.href);
                window.history.pushState(null, null, target.href);
                loadPage(target.href);
            }else if(target.hash.length){
                window.history.replaceState({pageYOffset: window.pageYOffset, pathname: target.pathname}, null, window.location.href);
                window.history.pushState(null, null, target.href);
                window.scrollTo(0, document.getElementById(target.hash.replace(/^#+/, "")).offsetTop);
            }
            return;
        }
    }
};
document.removeEventListener("mousedown", ajaxEventMousedownLink);
document.addEventListener("mousedown", ajaxEventMousedownLink);

/* Disable standard click; using mousedown instead. */
var ajaxEventClickLink = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("a.ajax")){
            event.preventDefault();
            return;
        }
    }
};
document.removeEventListener("click", ajaxEventClickLink);
document.addEventListener("click", ajaxEventClickLink);

/*-----------------------------------------------------------------------------\
| When forms in the "ajax" class are submitted, send the form and load the     |
| page via AJAX.                                                               |
\-----------------------------------------------------------------------------*/
var ajaxEventMousedownForm = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.form && target.form.matches("form.ajax") && target.matches("input[type=\"submit\"]")){
            event.preventDefault();
            if(target.form.action) href = target.form.action;
            else href = window.location.href + window.location.hash;
            loadPage(href, window.pageYOffset, "POST", target.form);
            return;
        }
    }
};
document.removeEventListener("mousedown", ajaxEventMousedownForm);
document.addEventListener("mousedown", ajaxEventMousedownForm);

/* Disable standard click; using mousedown instead. */
var ajaxEventClickForm = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.form && target.form.matches("form.ajax") && target.matches("input[type=\"submit\"]")){
            event.preventDefault();
            return;
        }
    }
};
document.removeEventListener("click", ajaxEventClickForm);
document.addEventListener("click", ajaxEventClickForm);

/* Event handler for form submit on [Enter] keypress. */
var ajaxEventKeydownForm = function(event){
    if(event.keyCode !== 13 || event.shiftKey || event.altKey || event.ctrlKey) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        // Ignore keypress on non-submit buttons.
        if(typeof target.matches === "function"
        && (target.matches("input[type=\"button\"]")
        ||  target.matches("input[type=\"checkbox\"]")
        ||  target.matches("input[type=\"color\"]")
        ||  target.matches("input[type=\"image\"]")
        ||  target.matches("input[type=\"radio\"]")
        ||  target.matches("input[type=\"reset\"]"))
        ){
            target.click();
            return;
        // Resolve labels; ignore keypress on non-submit button labels.
        }else if(target.matches("label")){
            var htmlFor = document.getElementById(target.htmlFor);
            if(typeof htmlFor.matches === "function"
            && (htmlFor.matches("input[type=\"button\"]")
            ||  htmlFor.matches("input[type=\"checkbox\"]")
            ||  htmlFor.matches("input[type=\"color\"]")
            ||  htmlFor.matches("input[type=\"image\"]")
            ||  htmlFor.matches("input[type=\"radio\"]")
            ||  htmlFor.matches("input[type=\"reset\"]"))
            ){
                target.click();
                return;
            }
        // Only process AJAX forms.
        }else if(target.form
        && typeof target.form.matches === "function"
        && target.form.matches("form.ajax")
        ){
            event.preventDefault();
            if(target.form.action) href = target.form.action;
            else href = window.location.href + window.location.hash;
            loadPage(href, window.pageYOffset, "POST", target.form);
            return;
        }
    }
};
document.removeEventListener("keydown", ajaxEventKeydownForm);
document.addEventListener("keydown", ajaxEventKeydownForm);

/*-----------------------------------------------------------------------------\
| When the back button is clicked, load the page via AJAX.                     |
\-----------------------------------------------------------------------------*/
window.onpopstate = function(event){
    if(!event.state){
        loadPage(window.location.pathname + window.location.hash, window.pageYOffset);
    }

    /* Location changed, load new page. */
    else if(event.state.pathname !== window.location.pathname){
        loadPage(window.location.pathname + window.location.hash, event.state.pageYOffset);
    }

    /* Only breadcrumb was changed, scroll to element. */
    else if(window.location.hash.length){
        try{window.scrollTo(0, document.getElementById(window.location.hash.replace(/^#+/, "")).offsetTop - 58)}catch(error){console.error(error)}
    }
}
