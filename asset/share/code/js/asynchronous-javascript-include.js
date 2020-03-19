/*-----------------------------------------------------------------------------\
| Asynchronously includes JavaScript. Can fall back to a second link.          |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     <script>                                                                 |
|         cssAsync("https://cdn.example.com/js/script.js", "/js/script.js");   |
|     <script>                                                                 |
+---------+-----------+----------+---------------------------------------------|
| @param  | string    | primary  | Primary URL (typically a CDN).              |
| @param  | string    | fallback | Fallback URL (typically a local copy).      |
| @param  | string    | callback | Callback function after <script> loads.     |
| @return | void      |          |                                             |
\---------+-----------+----------+--------------------------------------------*/
function jsAsync(primary, fallback, callback){

    /* Build AJAX request. */
    var xhr = new XMLHttpRequest();
    xhr.open("HEAD", primary);

    /* AJAX state change callback. */
    xhr.onreadystatechange = function(){

        /* AJAX completion. */
        if(this.readyState === this.DONE){

            /* Create <script> to hold script. */
            var script = document.createElement("script");

            /* Use CDN on success. || Cloudflare returns 503 on HEADs. */
            if(this.status === 200 || this.status === 503){
                script.src = primary;
            }else{
                script.src = fallback;
            }

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

            /* Apply callback. */
            script.onload = callback;

            /* Insert into <head>. */
            document.head.insertBefore(script, document.head.firstChild);
        }
    };

    /* Send AJAX request. */
    try{xhr.send()}finally{}
}
