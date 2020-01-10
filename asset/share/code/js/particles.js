/*-----------------------------------------------------------------------------\
| Create particles on the entire screen and spawn more particles on mousedown. |
+------------------------------------------------------------------------------+
| Requires:                                                                    |
|     particles.js:                                                            |
|         Git: https://github.com/vincentgarreau/particles.js                  |
|         CDN: https://cdn.jsdelivr.net/npm/particles.js@latest/particles.min.js
|     particles.json Configuration File:                                       |
|         Config Creator: https://vincentgarreau.com/particles.js              |
\-----------------------------------------------------------------------------*/
function particlesToggle(){
    if(typeof particlesToggle.particlesOn !== "boolean" || particlesToggle.particlesOn !== true){

        /* Mark particles as on. */
        particlesToggle.particlesOn = true;

        if(typeof particlesStart === "number"){
            clearInterval(window.particlesStart);
            window.particlesStart = false;
        }
        if(typeof particlesEventHandler === "function") document.removeEventListener("mousemove", particlesEventHandler);
        window.particlesStart = setInterval(function(){
            if(typeof window.particlesJS === "function" && document.body){
                clearInterval(window.particlesStart);
                window.particlesStart = false;

                /* Cleanup any previous particles stuff. */
                var cleanup = document.getElementById("particles");
                if(cleanup) cleanup.parentElement.removeChild(cleanup);
                if(typeof pJSDom === "object" && pJSDom.length > 0){
                    for(var i = pJSDom.length - 1; i >= 0; i--) pJSDom[i].pJS.fn.vendors.destroypJS();
                    pJSDom = [];
                }

                /* Create the particle container. */
                var particles = document.createElement("div");
                particles.id = "particles";
                particles.style.height = "100vh";
                particles.style.left = 0;
                particles.style.position = "fixed";
                particles.style.top = 0;
                particles.style.width = "100vw";
                particles.style.zIndex = -1;
                document.body.insertBefore(particles, document.body.firstChild);

                /* Load the particles.js configuration file. */
                particlesJS.load("particles", "/asset/misc/particles.json");

                /* Limit to 40 particles; for performance reasons. */
                if(typeof window.particlesLimit === "number"){
                    clearInterval(window.particlesLimit);
                    window.particlesLimit = false;
                }
                window.particlesLimit = setInterval(function(){
                    for(var i = pJSDom.length - 1; i >= 0; i--){
                        if(pJSDom[i].pJS.particles.array.length > 40){
                            pJSDom[i].pJS.fn.vendors.densityAutoParticles();
                        }
                    }
                }, 250);
            }else{
                /* Inject dependency: particles.js. */
                if(!document.querySelector("script[src*='particles.min.js']")){

                    /* Create script. */
                    var script = document.createElement("script");
                    script.src = "https://cdn.jsdelivr.net/npm/particles.js@latest/particles.min.js";

                    /* Add nonce if applicable. */
                    var nonce = "";
                    var nonces = document.querySelectorAll("[nonce]");
                    for(var i = nonces.length - 1; i >= 0; i--){
                        nonce = nonces[i].getAttribute("nonce");
                        if(nonce){
                            script.setAttribute("nonce", nonce);
                            break;
                        }
                    }

                    /* Inject script. */
                    document.head.insertBefore(script, document.head.firstChild);
                }
            }
        }, 1000);

        /* Reset every 1/2 hour; for performance reasons. Is something leaking? */
        if(typeof window.particlesReset === "number"){
            clearInterval(window.particlesReset);
            window.particlesReset = false;
        }
        window.particlesReset = setInterval(function(){
            for(var i = pJSDom.length - 1; i >= 0; i--) pJSDom[i].pJS.fn.vendors.init();
        }, 1800000);

        /* Make more on mousedown. */
        if(typeof window.particlesListener !== "number" || window.particlesListener !== 1){

            /* Inject dependency: function throttle. */
            if(typeof window.throttle !== "function") window.throttle = function(n,l,t){var a,e,u,r,i=0;t||(t={});var o=function(){i=t.leading===!1?0:Date.now(),a=null,r=n.apply(e,u),a||(e=u=null)},c=function(){var c=Date.now();i||t.leading!==!1||(i=c);var f=l-(c-i);return e=this,u=arguments,0>=f||f>l?(a&&(clearTimeout(a),a=null),i=c,r=n.apply(e,u),a||(e=u=null)):a||t.trailing===!1||(a=setTimeout(o,f)),r};return c.cancel=function(){clearTimeout(a),i=0,a=e=u=null},c};

            particlesEventHandler = throttle(function(event){
                if(event.buttons === 1 || event.buttons === 2) document.getElementById("particles").click();
            }, 35);
            document.addEventListener("mousemove", particlesEventHandler);
            particlesToggle.particlesListener = 1;
        }
    }else{
        /*---------------------------------------------------------------------\
        | Remove the particles.                                                |
        \---------------------------------------------------------------------*/

        /* Mark the particles as off. */
        particlesToggle.particlesOn = false;

        /* Cleanup timers. */
        if(window.particlesStart) clearInterval(window.particlesStart);
        if(window.particlesLimit) clearInterval(window.particlesLimit);
        if(window.particlesReset) clearInterval(window.particlesReset);
        window.particlesStart = false;
        window.particlesLimit = false;
        window.particlesReset = false;

        /* Remove DOM element. */
        var cleanup = document.getElementById("particles");
        if(cleanup) cleanup.parentElement.removeChild(cleanup);

        /* Cleanup memory. */
        if(typeof pJSDom === "object" && pJSDom.length > 0){
            for(var i = pJSDom.length - 1; i >= 0; i--) pJSDom[i].pJS.fn.vendors.destroypJS();
        }
        pJSDom = [];

        /* Remove event handler. */
        document.removeEventListener("mousemove", particlesEventHandler);
        particlesToggle.particlesListener = 0;
    }
}
