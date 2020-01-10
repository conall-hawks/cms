/**----------------------------------------------------------------------------\
| Reports active setIntervals and setTimeouts, because we don't them running   |
| rampant.                                                                     |
+---------+---------+-----------------+----------------------------------------+
| @param  | boolean | toggle          | Enable/Disable the reporter.           |
| @param  | integer | threshIntervals | Don't report unless there's this many. |
| @param  | integer | threshTimeouts  | Don't report unless there's this many. |
| @return | void    |                 |                                        |
\---------+---------+-----------------+---------------------------------------*/
function reportTimers(toggle, threshIntervals, threshTimeouts){
    if(typeof window.originalSetInterval   !== "function") window.originalSetInterval   = window.setInterval;
    if(typeof window.originalSetTimeout    !== "function") window.originalSetTimeout    = window.setTimeout;
    if(typeof window.originalClearInterval !== "function") window.originalClearInterval = window.clearInterval;
    if(typeof window.originalClearTimeout  !== "function") window.originalClearTimeout  = window.clearTimeout;
    switch(toggle){

        // Disable reporting; cleanup.
        case false:
            window.originalClearInterval(reportTimers.timer);
            reportTimers.timer = false;
            for(var i = 0; i < window.autoClearTimeout.length; i++){
                window.originalClearTimeout(window.autoClearTimeout[i]);
            }

            // Restore original functions.
            window.setInterval   = window.originalSetInterval;
            window.setTimeout    = window.originalSetTimeout;
            window.clearInterval = window.originalClearInterval;
            window.clearTimeout  = window.originalClearTimeout;
            delete window.originalSetInterval;
            delete window.originalSetTimeout;
            delete window.originalClearInterval;
            delete window.originalClearTimeout;
            break;

        // Enable reporting.
        default:

            // Idempotence.
            if(reportTimers.timer) break;

            // Mark intervals by overriding the prototype.
            if(!window.activeIntervals) window.activeIntervals = 0;
            window.setInterval = function(func, delay){
                var intervalId = window.originalSetInterval(func, delay);
                if(intervalId){
                    window.activeIntervals++;

                    // Attempt to get function name.
                    var name = func.name;
                    if(!name){
                        name = func.toString();
                        var indexStart = name.indexOf("function");
                        if(indexStart !== -1){
                            name = name.substr(indexStart + 8).trim();
                            name = name.substr(0, name.indexOf("("));
                        }
                    }
                    if(!name){
                        name = func.toString();
                        var indexStart = name.indexOf("clearInterval(");
                        if(indexStart !== -1){
                            name = name.substr(indexStart + 14);
                            name = name.substr(0, name.indexOf(")"));
                        }
                    }

                    // Report new interval.
                    console.debug("setInterval(" + name + ", " + delay + "); ID: " + intervalId);
                }
                return intervalId;
            }
            window.clearInterval = function(intervalId){
                console.debug("clearInterval(" + intervalId + ")");
                if(intervalId && window.activeIntervals) window.activeIntervals--;
                return originalClearInterval(intervalId);
            }

            // Mark timeouts by overriding the prototype.
            if(!window.activeTimeouts)   window.activeTimeouts   = 0;
            if(!window.autoClearTimeout) window.autoClearTimeout = [];
            window.setTimeout = function(func, delay){
                var timeoutId = window.originalSetTimeout(func, delay);
                if(timeoutId){
                    window.autoClearTimeout[timeoutId] = window.originalSetTimeout(function(){window.activeTimeouts--}, delay);
                    window.activeTimeouts++;

                    // Attempt to get function name.
                    var name = func.name;
                    if(!name){
                        name = func.toString();
                        var indexStart = name.indexOf("function");
                        if(indexStart !== -1){
                            name = name.substr(indexStart + 8).trim();
                            name = name.substr(0, name.indexOf("("));
                        }
                    }
                    if(!name){
                        name = func.toString();
                        var indexStart = name.indexOf("clearTimeout(");
                        if(indexStart !== -1){
                            name = name.substr(indexStart + 13);
                            name = name.substr(0, name.indexOf(")"));
                        }
                    }

                    // Report new timeout.
                    console.debug("setTimeout(" + name + ", " + delay + "); ID: " + timeoutId);
                }
                return timeoutId;
            }
            window.clearTimeout = function(timeoutId){
                if(timeoutId && window.activeTimeouts){
                    console.debug("clearTimeout(" + timeoutId + ")");
                    window.originalClearTimeout(window.autoClearTimeout[timeoutId]);
                    delete window.autoClearTimeout[timeoutId];
                    window.activeTimeouts--;
                }
                return window.originalClearTimeout(timeoutId);
            }

            // Report timers.
            reportTimers.timer = window.originalSetInterval(function(){
                if(!threshIntervals || window.activeIntervals > threshIntervals){
                    console.debug("Active intervals: " + (window.activeIntervals - (threshIntervals || 0) + 1));
                }else if(window.activeIntervals === threshIntervals){
                    console.debug("Active intervals: 1");
                }
                if(!threshTimeouts || window.activeTimeouts > threshTimeouts){
                    console.debug("Active timeouts:  " + (window.activeTimeouts - (threshTimeouts || 0) + 1));
                }else if(window.activeTimeouts === threshTimeouts){
                    console.debug("Active timeouts:  1");
                }
            }, 1000);
    }
}
