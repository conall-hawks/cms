/*---------------------------------------------------------------------------------------------------------------------\
| Upload JavaScript.                                                                                                   |
\---------------------------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------\
| Form submission event handlers.                                              |
\-----------------------------------------------------------------------------*/
var ajaxEventMousedownUploadForm = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.form && target.form.matches("#upload-form") && target.matches("input[type=\"submit\"]")){
            event.preventDefault();
            if(!ajaxifyForm(target.form)) return;
            window.uploader.addFiles([target.form.upload], {
                "captcha":  target.form.captcha.value,
                "password": target.form.password.value,
                "privacy":  target.form.privacy.value
            });
            target.form.reset();
            return;
        }
    }
};
document.removeEventListener("mousedown", ajaxEventMousedownUploadForm);
document.addEventListener("mousedown", ajaxEventMousedownUploadForm);

/* Disable standard click; using mousedown instead. */
var ajaxEventClickUploadForm = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.form && target.form.matches("#upload-form") && target.matches("input[type=\"submit\"]")){
            event.preventDefault();
            return;
        }
    }
};
document.removeEventListener("click", ajaxEventClickUploadForm);
document.addEventListener("click", ajaxEventClickUploadForm);

/* Event handler for form submit on [Enter] keypress. */
var ajaxEventKeydownUploadForm = function(event){
    if(event.keyCode !== 13 || event.shiftKey || event.altKey || event.ctrlKey) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.form && target.form.matches("#upload-form") && !target.matches("#upload-cancel")){
            event.preventDefault();
            var proxyEvent = document.createEvent("MouseEvents");
            proxyEvent.initMouseEvent("mousedown", true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
            document.getElementById("upload-submit").dispatchEvent(proxyEvent);
            return;
        }
    }
};
document.removeEventListener("keydown", ajaxEventKeydownUploadForm);
document.addEventListener("keydown", ajaxEventKeydownUploadForm);

/*-----------------------------------------------------------------------------\
| Upload cancellation event handlers.                                          |
\-----------------------------------------------------------------------------*/
var eventMousedownUploadCancel = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#upload-cancel")){
            window.uploader.cancelAll();
            return;
        }
    }
};
document.removeEventListener("mousedown", eventMousedownUploadCancel);
document.addEventListener("mousedown", eventMousedownUploadCancel);

/* Disable standard click; using mousedown instead. */
var eventClickUploadCancel = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#upload-cancel")){
            event.preventDefault();
            return;
        }
    }
};
document.removeEventListener("click", eventClickUploadCancel);
document.addEventListener("click", eventClickUploadCancel);

/* Event handler for form submit on [Enter] keypress. */
var eventKeydownUploadCancel = function(event){
    if(event.keyCode !== 13 || event.shiftKey || event.altKey || event.ctrlKey) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#upload-cancel")){
            event.preventDefault();
            window.uploader.cancelAll();
            return;
        }
    }
};
document.removeEventListener("keydown", eventKeydownUploadCancel);
document.addEventListener("keydown", eventKeydownUploadCancel);

/*-----------------------------------------------------------------------------\
| Focus password field on password privacy selection.                          |
\-----------------------------------------------------------------------------*/
var eventMousedownPrivacyPassword = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#privacy-password")){
            target.checked = true;
            focusInput(document.getElementById("upload-password"));
            return;
        }
    }
};
document.removeEventListener("mousedown", eventMousedownPrivacyPassword);
document.addEventListener("mousedown", eventMousedownPrivacyPassword);

/* Disable standard click; using mousedown instead. */
var eventClickPrivacyPassword = function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#privacy-password")){
            event.preventDefault();
            return;
        }
    }
};
document.removeEventListener("click", eventClickPrivacyPassword);
document.addEventListener("click", eventClickPrivacyPassword);

/* Event handler for form submit on [Enter] keypress. */
var eventKeydownPrivacyPassword = function(event){
    if(event.keyCode !== 13 || event.shiftKey || event.altKey || event.ctrlKey) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#privacy-password")){
            target.checked = true;
            focusInput(document.getElementById("upload-password"));
            return;
        }
    }
};
document.removeEventListener("keydown", eventKeydownPrivacyPassword);
document.addEventListener("keydown", eventKeydownPrivacyPassword);

/*-----------------------------------------------------------------------------\
| Fine Uploader initializer.                                                   |
\-----------------------------------------------------------------------------*/
if(window.startUploader !== "number") window.startUploader = setInterval(function(){
    if(typeof window.qq === "function"){
        clearInterval(startUploader);

        window.formatTime = function(sec){
            var sec     = parseInt(sec, 10);
            var hours   = Math.floor(sec / 3600);
            var minutes = Math.floor((sec - (hours * 3600)) / 60);
            var seconds = sec - (hours * 3600) - (minutes * 60);
            if(hours   < 10) hours   = "0"+hours;
            if(minutes < 10) minutes = "0"+minutes;
            if(seconds < 10) seconds = "0"+seconds;
            return hours + ":" + minutes + ":" + seconds;
        }

        window.calculateBandwidth = function(currentBytes, totalBytes){
            if(typeof window.calculateBandwidth.log !== "object") window.calculateBandwidth.log = [];

            window.calculateBandwidth.log.push({
                currentBytes: currentBytes,
                currentTime: new Date().getTime()
            });
            var minSamples = 2;
            var maxSamples = 25;
            if(window.calculateBandwidth.log.length > maxSamples) window.calculateBandwidth.log.shift();
            if(window.calculateBandwidth.log.length >= minSamples){
                var firstSample    = window.calculateBandwidth.log[0];
                var lastSample     = window.calculateBandwidth.log[window.calculateBandwidth.log.length - 1];
                var progressBytes  = lastSample.currentBytes - firstSample.currentBytes;
                var progressTimeMS = lastSample.currentTime - firstSample.currentTime;
                var bytesPerSecond = progressBytes / (progressTimeMS / 1000);
                var timeRemaining  = ((totalBytes - currentBytes) / progressBytes).toFixed(0);
                if(bytesPerSecond > 0) return {"bytesPerSecond": bytesPerSecond, "timeRemaining": timeRemaining};
            }
            return false;
        };

        window.uploader = new qq.FineUploaderBasic({
            debug:   true,
            element: document.getElementById("upload-file"),
            request: {
                endpoint: "/upload"
            },
            chunking: {
                concurrent: {
                    enabled: false
                },
                enabled: true,
                partSize: 1048576
            },
            callbacks: {
                onProgress: function(id, name, uploadedBytes, totalBytes){
                    var elementPercent       = document.getElementById("upload-progress-percent");
                    var elementBar           = document.getElementById("upload-progress-bar");
                    var elementBandwidth     = document.getElementById("upload-progress-bandwidth");
                    var elementTimeRemaining = document.getElementById("upload-progress-time-remaining");
                    var elementStatus        = document.getElementById("upload-progress-status");
                    var elementSubmit        = document.getElementById("upload-submit");
                    var percent   = (uploadedBytes / totalBytes * 100).toFixed(2);
                    var bandwidth = window.calculateBandwidth(uploadedBytes, totalBytes);
                    elementPercent.textContent       = percent + "%";
                    elementBar.style.width           = "calc(" + percent + "% - 8px)";
                    elementBandwidth.textContent     = formatBytes(bandwidth.bytesPerSecond) + "/second";
                    elementTimeRemaining.textContent = formatTime(bandwidth.timeRemaining || 0);
                    elementStatus.textContent = "Uploaded " + formatBytes(uploadedBytes) + " of " + formatBytes(totalBytes) + ".";
                    elementSubmit.classList.add("active");
                    elementSubmit.disabled = true;
                    document.removeEventListener("keydown", ajaxEventKeydownUploadForm);
                    if(Number(percent).toFixed(0) === 100) elementStatus.textContent = "Processing " + formatBytes(uploadedBytes) + ".";
                },
                onComplete: function(id, name, responseJSON, xhr){
                    var elementBandwidth             = document.getElementById("upload-progress-bandwidth");
                    var elementStatus                = document.getElementById("upload-progress-status");
                    elementBandwidth.textContent     = formatBytes(0) + "/second";
                    elementStatus.textContent        = "Adding to database...";
                    document.addEventListener("keydown", ajaxEventKeydownUploadForm);
                    loadPage(location.pathname, window.scrollY);
                },
                onCancel: function(id, name, responseJSON, xhr){
                    var elementBandwidth             = document.getElementById("upload-progress-bandwidth");
                    var elementTimeRemaining         = document.getElementById("upload-progress-time-remaining");
                    var elementStatus                = document.getElementById("upload-progress-status");
                    elementBandwidth.textContent     = formatBytes(0) + "/second";
                    elementTimeRemaining.textContent = formatTime(0);
                    elementStatus.textContent        = "Canceling...";
                    document.addEventListener("keydown", ajaxEventKeydownUploadForm);
                    loadPage(location.pathname, window.scrollY);
                },
                onError: function(id, name, errorReason, xhr){
                    var elementBandwidth             = document.getElementById("upload-progress-bandwidth");
                    var elementTimeRemaining         = document.getElementById("upload-progress-time-remaining");
                    var elementStatus                = document.getElementById("upload-progress-status");
                    elementBandwidth.textContent     = formatBytes(0) + "/second";
                    elementTimeRemaining.textContent = formatTime(0);
                    elementStatus.textContent        = "Error: " + errorReason;
                    document.addEventListener("keydown", ajaxEventKeydownUploadForm);
                    if(confirm("Error: " + errorReason + ".\n\nReload the page?")){
                        loadPage(location.pathname, window.scrollY);
                    }
                }
            },
            retry: {
                enableAuto: false
            }
        });
    }
}, 100);
