<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>

<!-- Feedback. -->
<script nonce="<?php echo $security->nonce(); ?>">
    console.debug("Begin JavaScript in <head>.");
</script>

<!-- Best-attempt HTTPS. -->
<script nonce="<?php echo $security->nonce(); ?>">
    if(location.protocol !== "https:"){
        var path = "https:" + window.location.href.substring(window.location.protocol.length);
        var xhr = new XMLHttpRequest();
        xhr.open("HEAD", path);
        xhr.onreadystatechange = function(){
            if(this.readyState === this.DONE && this.status === 200){
                location.href = path;
            }
        };
        try{xhr.send();}finally{}
    }
</script>

<!-- Metadata. -->
<?php foreach ($this->meta as &$value) $value = htmlentities($value); ?>
<!-- <base href="<?php echo $uri->path; ?>" /> -->
<base href="/" />
<meta http-equiv="X-UA-Compatible"   content="IE=edge" />
<meta charset="<?php echo $this->meta['charset']; ?>" />
<meta name="application-name"        content="<?php echo $this->meta['application-name']; ?>" />
<meta name="author"                  content="<?php echo $this->meta['author']; ?>" />
<meta name="copyright"               content="<?php echo $this->meta['copyright']; ?>" />
<meta name="description"             content="<?php echo $this->meta['description']; ?>" />
<meta name="keywords"                content="<?php echo $this->meta['keywords']; ?>" />
<meta name="msapplication-TileImage" content="<?php echo $this->meta['favicon']; ?>" />
<meta name="msapplication-TileColor" content="#FFFFFF" />
<meta name="referrer"                content="no-referrer" />
<meta name="robots"                  content="<?php echo $this->meta['robots']; header('X-Robots-Tag: '.$this->meta['robots']); ?>" />
<meta name="viewport"                content="initial-scale=1.0, height=device-height, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width" />
<title><?php echo $this->meta['title']; ?></title>
<link rel="icon"     href="<?php echo $this->meta['favicon']; ?>" sizes="16x16 24x24 32x32 48x48 64x64 128x128 192x192" />
<link rel="manifest" href="<?php echo $this->meta['manifest']; ?>" />

<!-- Open Graph metadata. -->
<meta name="og:description" content="<?php echo $this->meta['og:description']; ?>" />
<meta name="og:image"       content="<?php echo $this->meta['og:image']; ?>" />
<meta name="og:site_name"   content="<?php echo $this->meta['og:site_name']; ?>" />
<meta name="og:title"       content="<?php echo $this->meta['og:title']; ?>" />
<meta name="og:url"         content="<?php echo $this->meta['og:url']; ?>" />

<!-- Twitter metadata. -->
<meta name="twitter:card" content="<?php  echo $this->meta['twitter:card']; ?>" />
<meta name="twitter:site" content="@<?php echo $this->meta['twitter:site']; ?>" />

<!-- DNS prefetching. -->
<link rel="dns-prefetch" href="https://cdnjs.jsdelivr.net" />
<link rel="dns-prefetch" href="https://fonts.googleapis.com" />
<link rel="dns-prefetch" href="https://cdn.polyfill.io" />
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com" />
<link rel="dns-prefetch" href="https://fonts.gstatic.com" />

<?php if(ENVIRONMENT === 'development'){ ?>
    <!-- Development environment; debugging. -->
    <script nonce="<?php echo $security->nonce(); ?>">
        <?php echo file_get_contents(ASSET.'/share/code/js/report-timers.js'); ?>
        //reportTimers(true, 2, 2);
        //reportTimers(true, 0, 0);
    </script>
<?php } ?>

<!-- Asynchronous include functions. -->
<script nonce="<?php echo $security->nonce(); ?>"><?php echo file_get_contents(ASSET.'/share/code/js/asynchronous-css-include.js'); ?></script>
<script nonce="<?php echo $security->nonce(); ?>"><?php echo file_get_contents(ASSET.'/share/code/js/asynchronous-javascript-include.js'); ?></script>
<script nonce="<?php echo $security->nonce(); ?>"><?php echo file_get_contents(ASSET.'/share/code/js/event-listener.js'); ?></script>

<!-- CSS libraries. -->
<script nonce="<?php echo $security->nonce(); ?>">
    cssAsync("https://cdn.jsdelivr.net/npm/normalize.css@latest/normalize.min.css"      , "/asset/misc/normalize.css");
    cssAsync("https://fonts.googleapis.com/css?family=Anonymous+Pro:400,700"            , "/asset/misc/anonymous-pro.css");
    cssAsync("https://fonts.googleapis.com/css?family=Montserrat:400,700"               , "/asset/misc/montserrat.css");
    cssAsync("https://cdn.jsdelivr.net/npm/font-awesome@latest/css/font-awesome.min.css", "/asset/misc/font-awesome.css");
    cssAsync("https://cdn.jsdelivr.net/npm/highlight.js@latest/styles/sunburst.min.css" , "/asset/misc/highlight.css");
</script>

<!-- CSS libraries' non-JavaScript fallback. -->
<noscript>
    <link rel="stylesheet" nonce="<?php echo $security->nonce(); ?>" href="https://cdn.jsdelivr.net/npm/normalize.css@latest/normalize.min.css" />
    <link rel="stylesheet" nonce="<?php echo $security->nonce(); ?>" href="https://fonts.googleapis.com/css?family=Anonymous+Pro:400,700" />
    <link rel="stylesheet" nonce="<?php echo $security->nonce(); ?>" href="https://fonts.googleapis.com/css?family=Montserrat:400,700" />
    <link rel="stylesheet" nonce="<?php echo $security->nonce(); ?>" href="https://cdn.jsdelivr.net/npm/font-awesome@latest/css/font-awesome.min.css" />
    <link rel="stylesheet" nonce="<?php echo $security->nonce(); ?>" href="https://cdn.jsdelivr.net/npm/highlight.js@latest/styles/sunburst.min.css" />
</noscript>

<!-- JavaScript libraries. -->
<!-- <script async nonce="<?php echo $security->nonce(); ?>" src="https://cdn.polyfill.io/v2/polyfill.min.js"></script> -->
<!--[if lt IE 9]><script nonce="<?php echo $security->nonce(); ?>" src="https://cdn.jsdelivr.net/npm/selectivizr2@latest/selectivizr2.min.js"></script><![endif]-->
<!--[if lt IE 9]><script nonce="<?php echo $security->nonce(); ?>" src="https://cdn.jsdelivr.net/npm/ie8@latest/build/ie8.max.min.js"></script><![endif]-->
<!--[if lt IE 9]><script nonce="<?php echo $security->nonce(); ?>" src="https://cdn.jsdelivr.net/npm/html5shiv@latest/dist/html5shiv.min.js"></script><![endif]-->
<!--[if lt IE 9]><script nonce="<?php echo $security->nonce(); ?>" src="https://cdn.jsdelivr.net/flashcanvas/latest/flashcanvas.js"></script><![endif]-->
<script nonce="<?php echo $security->nonce(); ?>">
    jsAsync("https://cdn.jsdelivr.net/npm/es5-shim@latest/es5-sham.min.js", "/asset/misc/es5-sham.js");
    jsAsync("https://cdn.jsdelivr.net/npm/es6-shim@latest/es6-shim.min.js", "/asset/misc/es6-shim.js");
    jsAsync("https://cdn.jsdelivr.net/npm/json3@latest/lib/json3.min.js", "/asset/misc/json3.js");
    jsAsync("https://cdn.jsdelivr.net/npm/dom4@latest/build/dom4.max.min.js", "/asset/misc/dom4.js");
    jsAsync("https://cdn.jsdelivr.net/npm/jquery@latest/dist/jquery.min.js", "/asset/misc/jquery.js");
    jsAsync("https://cdn.jsdelivr.net/npm/calc-polyfill@latest/calc.min.js", "/asset/misc/calc.js");
    jsAsync("https://cdn.jsdelivr.net/npm/html5media@latest/dist/api/1.2.1/html5media.min.js", "/asset/misc/html5media.js");
</script>
<script nonce="<?php echo $security->nonce(); ?>">jsAsync("https://cdn.jsdelivr.net/npm/fine-uploader@latest/fine-uploader/fine-uploader.min.js", "/asset/misc/fine-uploader.js");</script>
<script nonce="<?php echo $security->nonce(); ?>"><?php echo file_get_contents(ASSET.'/share/code/js/focus-text-input.js'); ?></script>

<!-- Prefix Free. -->
<script nonce="<?php echo $security->nonce(); ?>">
    // Temporarily disabled; causes double page load.
    //jsAsync("https://cdn.jsdelivr.net/npm/prefixfree@latest/prefixfree.min.js", "/asset/misc/prefix-free.js", function(){StyleFix.process()});
</script>

<!-- Syntax Highlighting. -->
<script nonce="<?php echo $security->nonce(); ?>">
    jsAsync("https://cdn.jsdelivr.net/highlight.js/latest/highlight.min.js", "/asset/misc/highlight.js", function(){
        window.hljsLanguagesLoading = 5;
        jsAsync("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/languages/dos.min.js"       , "/asset/misc/highlight-dos.js"       , function(){window.hljsLanguagesLoading--});
        jsAsync("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/languages/pgsql.min.js"     , "/asset/misc/highlight-pgsql.js"     , function(){window.hljsLanguagesLoading--});
        jsAsync("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/languages/powershell.min.js", "/asset/misc/highlight-powershell.js", function(){window.hljsLanguagesLoading--});
        jsAsync("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/languages/vim.min.js"       , "/asset/misc/highlight-vim.js"       , function(){window.hljsLanguagesLoading--});
        jsAsync("https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/languages/x86asm.min.js"    , "/asset/misc/highlight-x86asm.js"    , function(){window.hljsLanguagesLoading--});
        if(typeof window.startHljsLanguages !== "number") window.startHljsLanguages = setInterval(function(){
            if(window.hljsLanguagesLoading < 1){
                clearInterval(window.startHljsLanguages);
                setTimeout(function(){
                    var code = document.getElementsByTagName("code");
                    for(var i = code.length - 1; i >= 0; i--) window.hljs.highlightBlock(code[i]);
                }, 1);
            }
        }, 100);
    });
</script>

<!-- Particles! -->
<script nonce="<?php echo $security->nonce(); ?>">
    <?php require(ASSET.'/share/code/js/particles.js'); ?>
    jsAsync("https://cdn.jsdelivr.net/npm/particles.js@latest/particles.min.js", "/asset/misc/particles.js", function(){
        if(<?php echo !empty($_SESSION['particles']) ? 'true' : 'false'; ?>) particlesToggle();
    });
</script>

<!-- Google Adsense. -->
<script async defer nonce="<?php echo $security->nonce(); ?>" src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script nonce="<?php echo $security->nonce(); ?>">
    if(typeof window.startAdsense !== "number") window.startAdsense = setInterval(function(){
        if(typeof window.adsbygoogle === "object"){
            clearInterval(window.startAdsense);
            window.adsbygoogle.push({});
        }
    }, 100);
</script>

<!-- Google Analytics. -->
<script async defer nonce="<?php echo $security->nonce(); ?>" src="https://www.google-analytics.com/analytics.js"></script>
<script nonce="<?php echo $security->nonce(); ?>">
    window.ga=function(){ga.q.push(arguments)};
    ga.q=[];
    ga.l=+Number(new Date);
    ga("create","<?php echo ANALYTICS_KEY; ?>","auto");
    ga("send","pageview");
</script>

<!-- Function throttle. -->
<script nonce="<?php echo $security->nonce(); ?>">function throttle(n,l,t){var a,e,u,r,i=0;t||(t={});var o=function(){i=t.leading===!1?0:Date.now(),a=null,r=n.apply(e,u),a||(e=u=null)},c=function(){var c=Date.now();i||t.leading!==!1||(i=c);var f=l-(c-i);return e=this,u=arguments,0>=f||f>l?(a&&(clearTimeout(a),a=null),i=c,r=n.apply(e,u),a||(e=u=null)):a||t.trailing===!1||(a=setTimeout(o,f)),r};return c.cancel=function(){clearTimeout(a),i=0,a=e=u=null},c};</script>

<!-- Websocket address. -->
<script>window.websocketAddress = "<?php echo $_SERVER['SERVER_NAME']; ?>";</script>
