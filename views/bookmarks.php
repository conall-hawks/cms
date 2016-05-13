<?php defined('BASEPATH') or die('Direct script access denied.'); ?>
<div class="content-box">
	<h2>Bookmarks:</h2>
	<p>This file looks at all *.url files in my bookmarks folder. I make lots of bookmarks.</p>
	<ul>
		<?php 
			/* Case insensitivity for a *.url glob uses brackets: .url or .URL = .[u][U][r][R][l][L] */
			$ext = '.[uU][rR][lL]';
			$files = glob(VIEWS.'/bookmarks/*'.$ext);
			usort($files, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
			foreach($files as $file){
				$url = file_get_contents($file);
				$file = basename($file);
				$file = str_ireplace('.url', '', $file);
				$match_found = preg_match("((\w+:\/\/)[-a-zA-Z0-9:@;?&=\/%\+\.\*!'\(\),\$_\{\}\^~\[\]`#|]+)", $url, $url);
				if($match_found) $url = htmlspecialchars($url[0]);
				echo '<li><a class="contentLink" href="http://anonym.to?'.$url.'" target="_blank">'.$file.'</a></li>';
	}
		?>
	</ul>
</div>