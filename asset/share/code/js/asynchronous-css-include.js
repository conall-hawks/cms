/*-----------------------------------------------------------------------------\
| Asynchronously includes a CSS stylesheet. Can fall back to a second link.    |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     <script>                                                                 |
|         cssAsync("https://cdn.example.com/css/style.css", "/css/style.css"); |
|     <script>                                                                 |
|     <noscript>                                                               |
|         <link rel="stylesheet" href="/css/style.css" />                      |
|     </noscript>                                                              |
+---------+-----------+----------+---------------------------------------------|
| @param  | {string}  | primary  | Primary URL (typically a CDN).              |
| @param  | {string}  | fallback | Fallback URL (typically a local copy).      |
| @param  | string    | callback | Callback function after <link> loads.       |
| @return | {void}    |          |                                             |
\---------+-----------+----------+--------------------------------------------*/
function cssAsync(primary, fallback, callback){

    /* Build AJAX request. */
    var xhr = new XMLHttpRequest();
    xhr.open("HEAD", primary);

    /* AJAX state change callback. */
    xhr.onreadystatechange = function(){

        /* AJAX completion. */
        if(this.readyState === this.DONE){

            /* Create <link> to hold stylesheet. */
            var link = document.createElement("link");
            link.rel = "stylesheet";

            /* Use CDN on success. || Cloudflare returns 503 on HEADs. */
            if(this.status === 200 || this.status === 503){
                link.href = primary;
            }else{
                link.href = fallback;
            }

            /* Add nonce if applicable. */
            var nonce = "";
            var nonces = document.querySelectorAll("[nonce]");
            for(var i = nonces.length - 1; i >= 0; i--){
                nonce = nonces[i].getAttribute("nonce");
                if(nonce){
                    link.setAttribute("nonce", nonce);
                    break;
                }
            }

            /* Apply callback. */
            link.onload = callback;

            /* Insert into <head>. */
            document.head.insertBefore(link, document.head.firstChild);
        }
    };

    /* Send AJAX request. */
    try{xhr.send()}finally{}
}
