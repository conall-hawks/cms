<!DOCTYPE html>
<html class="no-js">
	<head>
		<!-- What you doing lookin' at my code? ＼(｀0´)／ -->
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="application-name" content="Cenari.us">
		<meta name="author" content="Cenari.us" />
		<meta name="copyright" content="Cenari.us" />
		<meta name="description" content="The Internet Playground!" />
		<meta name="keywords" content="" />
		<meta name="msapplication-TileImage" content="/images/favicon.png" />
		<meta name="msapplication-TileColor" content="#FFFFFF" />
		<meta name="referrer" content="no-referrer" />
		<meta name="robots" content="follow, index, noarchive" />
		<meta name="viewport" content="initial-scale=1.0, height=device-height, width=device-width" />
		<title><?php echo ucfirst($_SERVER['SERVER_NAME']); ?></title>
		<base href="localhost" />
		
		<!-- DNS prefetching. -->
		<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com" />
		<link rel="dns-prefetch" href="https://ajax.cdnjs.com" />
		<link rel="dns-prefetch" href="http://fonts.googleapis.com" />
		<link rel="dns-prefetch" href="https://google-analytics.com" />
		<link rel="dns-prefetch" href="https://pagead2.googlesyndication.com" />
		<link rel="dns-prefetch" href="https://anonym.to" />
		<link rel="dns-prefetch" href="https://js.anonym.to" />
		<link rel="dns-prefetch" href="https://static.wowhead.com" />
		<link rel="dns-prefetch" href="https://wow.zamimg.com" />
		<link rel="dns-prefetch" href="https://wowcss.zamimg.com" />
		<link rel="dns-prefetch" href="https://wowimg.zamimg.com" />
		<link rel="dns-prefetch" href="https://s.ytimg.com" />
		
		<!-- Universal stylesheets and style related stuff. -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css" rel="stylesheet" />
		<link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" />
		<noscript><style>html { animation: none !important; }</style></noscript>
		<!--<link href="/templates/global.css" rel="stylesheet" />-->
		<style><?php include_once TEMPLATES.'/global.css'; ?></style>
		<link href="/images/favicon.png" rel="icon" sizes="16x16 24x24 32x32 48x48 64x64 128x128 192x192" />
		<link href="/misc/logo-57.png" rel="apple-touch-icon" />
		<link href="/misc/manifest.json" rel="manifest" />
		
		<!-- Using es5/es6-shim, jQuery, Throttle-Debounce, Modernizr, Normalize, Prefix-free, Selectivizr, ie8, html5shiv, ExplorerCanvas, html5media, and ZeroClipboard. -->
		<script async src="https://cdnjs.cloudflare.com/ajax/libs/es5-shim/4.1.13/es5-shim.min.js"></script>
		<script async src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.33.3/es6-shim.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
		<!--[if (gte IE 6)&(lte IE 8)]><script async src="https://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script><![endif]-->
		<!--[if IE 8]><script async src="https://cdnjs.cloudflare.com/ajax/libs/ie8/0.2.9/ie8.js"></script><![endif]-->
		<!--[if lt IE 9]><script async src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script><![endif]-->
		<!--[if lte IE 8]><script async src="https://tinyurl.com/pnusrvd" title="ExplorerCanvas"></script><![endif]-->
		<script async src="https://cdnjs.cloudflare.com/ajax/libs/html5media/1.1.8/html5media.min.js"></script>
		<script async src="https://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.min.js"></script>
		<script async src="https://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.swf"></script>
		<?php $m=strtolower(date('M'));if($m=='dec'||$m=='jan'||$m=='feb')echo '<script async src="https://cdnjs.cloudflare.com/ajax/libs/JQuery-Snowfall/1.7.4/snowfall.jquery.min.js"></script><script>var snow=setInterval(function(){$.snowfall&&($(document).snowfall("clear"),$(document).snowfall({flakeCount:50,maxSpeed:5}),clearInterval(snow))},1e3);</script>';?>

		<!-- Fallbacks for remotely hosted content. -->
		<script>window.jQuery || document.write('<script src="/includes/jquery.js"><\/script>')</script>
		<script>window.$.throttle || document.write('<script src="/includes/throttle-debounce.js"><\/script>')</script>
		<script>window.Modernizr || document.write('<script src="/includes/modernizr.js"><\/script>')</script>
		<script>window.StyleFix || document.write('<script src="/includes/prefixfree.js"><\/script>')</script>
		<script>
			cssFallback('/includes/normalize.css');
			function cssFallback(localCss){
				for(var i = 0; i < document.styleSheets.length; i++) if(document.styleSheets[i].href == localCss) return false;
				document.write('<link href="' + localCss + '" rel="stylesheet" \/>');
			}
		</script>
		
		<!-- Wowhead widgets. -->
		<!--<script async src="https://static.wowhead.com/widgets/newsfeed.js"></script>
		<script async src="https://static.wowhead.com/widgets/power.js"></script>
		<script>var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true }</script>-->
		
		<!-- Syntax Highlighting. -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/sunburst.min.css" rel="stylesheet" />
		<script defer src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/highlight.min.js"></script>
		<script>$(document).ready(function(){$('code').each(function(i, block){hljs.highlightBlock(block);});});</script>
		
		<!-- Google Analytics. -->
		<script>
			window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
			ga('create','UA-58090250-1','auto');ga('send','pageview')
		</script>
		<script defer src="https://www.google-analytics.com/analytics.js"></script>
		
		<!-- Websocket address. -->
		<script>var websocket_ip = "<?php echo $_SERVER['SERVER_NAME']; ?>";</script>
	</head>
	
	<body id="top">
