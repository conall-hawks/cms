		<!--<div class="google-search" style="border-top: 1px solid #BBB; top: 600px; height: 120px; left: 1px; position: absolute; width: 14.65%; z-index: 1;">
			<h2>Google Search</h2>
			<form method="get" action="https://www.google.com/search" target="_blank">
				<input type="text" name="q" style="border-radius: .25em; color: black; left: 4%; position: relative; top: 20px; width: 89%;" />
				<input type="submit" value="Search" style="color: black; left: 38%; position: relative; top: 30px;" />
				<div style="font-size: 80%; left: 33%; position: relative; text-align: center; top: 10px;"><input type="checkbox" name="sitesearch" value="cenari.us" title="Only show results from Cenari.us" checked /><br />Cenari.us</div>
			</form>
		</div>-->
		
		<aside class="left-box2">
			<h2>Change Color</h2>
			<input id="color-input" type="color" onchange="document.documentElement.style.backgroundColor = this.value" value="#0c0c10" />
			<input id="color-reset" type="reset" onclick="document.getElementById('color-input').value = '#0c0c10'; document.documentElement.style.backgroundColor = null" />
			
			<h2>WoW News</h2>
			<div class="wowhead-newsfeed">
				<script>
					var wowheadStart = setInterval(function(){
						if(typeof $WowheadNewsFeed !== 'undefined'){
							$WowheadNewsFeed.fetch(function(contents){$('#wowhead-newsfeed').html(contents);});
							clearInterval(wowheadStart);
						}
					}, 1000);
				</script>
				<noscript class="orange">JavaScript is required for the WoW news feed.</noscript>
			</div>
			<div class="wowhead-searchbox">
				<h2>WoW Search</h2>
				<iframe sandbox="allow-forms allow-popups allow-scripts" src="https://wow.zamimg.com/widgets/searchbox/searchbox.html"></iframe>
			</div>
		</aside>
		
		<aside class="right-box">
			<ins class="adsbygoogle"
				style="display:block"
				data-ad-client="ca-pub-6772462260567961"
				data-ad-slot="8688553332"
				data-ad-format="auto">
			</ins>
		</aside>
		
		<aside class="right-box2">
			<ins class="adsbygoogle"
				style="display:block"
				data-ad-client="ca-pub-6772462260567961"
				data-ad-slot="8688553332"
				data-ad-format="auto">
			</ins>
		</aside>
		
		<footer class="foot" style="background: black; height: 150px; width: 100vw;">
		</footer>
		
		<footer class="footer">
			<span class="feedback" id="feedback"><?php if(is_object(${$uri->class})) echo ${$uri->class}->feedback; ?></span>
			<?php echo $login->get_status() ? '&bull; <a class="orange" style="z-index: 1" href="/?logout">Logout</a>' : NULL; ?>
			&copy; <span><a class="ajax-link" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>"><?php echo ucfirst($_SERVER['SERVER_NAME']); ?></a> 2016</span>
			&bull; Internet Playground
			&bull; <a class="ajax-link" href="/privacy">Privacy Policy</a>
			&bull; <a href="mailto:admin@<?php echo $_SERVER['SERVER_NAME']; ?>?subject=Comment">Contact Us</a>
			&bull; <a class="scroll-link top-link red" href="/<?php echo $uri->path == 'index' ? '/' : $uri->path; ?>#top" id="top-link">&#9195;Top of Page&#9195;</a>
			&bull; <a class="chat-link orange">Chat</a>
			&bull; 
			<span class="benchmark">Page rendered in <strong id="benchmark"><?php echo $benchmark->calculate('start'); ?></strong> seconds.</span>
		</footer>
		
		<!--Chatroom-->
		<div id="chat" style="display: none;">
			<div class="chat-messages"></div>
			<form name="chat-form">
				<input class="chat-message" name="chatmsg" type="text" />
				<div class="submit-wrap"><input class="chat-submit" name="submitmsg" type="submit" value="Send" /></div>
			</form>
		</div>
		
		<!-- Global script. -->
		<!--<script src="/templates/global.js"></script>-->
		<script><?php include_once TEMPLATES.'/global.js'; ?></script>

		<!-- Dereferrer. -->
		<script async defer src="http://js.anonym.to/anonym/anonymize.js"></script>
		<script>
			var protected_links = 'anonym.to';
			var anonymStart = setInterval(function(){
				if(typeof auto_anonymize !== 'undefined'){
					protected_links = 'anonym.to';
					auto_anonymize();
					setTimeout(auto_anonymize, 6000);
					clearInterval(anonymStart);
				}
			}, 1000);
		</script>

		<!-- Google Adsense. -->
		<script defer src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<script>$('ins').each(function(){(adsbygoogle = window.adsbygoogle || []).push({});});</script>
	</body>
</html>
