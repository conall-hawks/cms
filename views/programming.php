<?php defined('BASEPATH') or die('Direct script access denied.'); ?>
<?php 
	$path = glob(VIEWS.'/'.$uri->path.'*')[0];
	$href = ltrim($file, VIEWS.'/');
	$info = pathinfo($path);
	if($uri->path == 'programming') $info['filename'] = 'Languages';
	$href = ltrim($path, VIEWS.'/');
	$description = $info['dirname'].'/.descriptions'.'/'.$info['filename'].'.php';
	if(file_exists($description)) include_once $description;
	if(is_dir($path)){
		echo '<article class="content-box">';
		echo '<h2><a class="ajax-link" href="'.$href.'">'.title($info['filename']).'</a></h2>';
		echo '<table class="navtable"><tr>';
		$files = glob_sort(glob($path.'/*'));
		$index = 0;
		foreach($files as $index => $file){
			if($index > 0 && $index % 8 == 0) echo '</tr><tr>';
			$href = ltrim($file, VIEWS.'/');
			$info = pathinfo($file);
			if(isset($info['extension'])) $href = rtrim(rtrim($href, $info['extension']), '.');
			$name = title($info['filename']);
			if(is_dir($file)){
				echo '<td><a class="ajax-link" href="/'.$href.'" title="'.$name.'"><div class="dir icon"></div>'.$name.'</a></td>';
			}elseif(is_file($file)){
				$info['extension'] = strtolower($info['extension']);
				if($info['extension'] == 'url'){
					$url = file_get_contents($file);
					$match_found = preg_match("((\w+:\/\/)[-a-zA-Z0-9:@;?&=\/%\+\.\*!'\(\),\$_\{\}\^~\[\]`#|]+)", $url, $url);
					if($match_found) $href = htmlspecialchars($url[0]);
					echo '<td><a href="http://anonym.to/?'.$href.'" target="_blank" title="'.$name.'"><div class="url icon"></div>'.$name.'</a></td>';
				}else{
					$href = str_ireplace('.'.$info['extension'], '', $href);
					echo '<td><a class="ajax-link" href="/'.$href.'" title="'.$name.'"><div class="'.$info['extension'].' icon"></div>'.$name.'</a></td>';
				}
			}else{
				echo '<td><a class="ajax-link" href="/'.$href.'" title="'.$name.'"><div class="'.$info['extension'].' icon"></div>'.$name.'</a></td>';
			}
		}
		if($index < 7) for($index; $index < 7; $index++) echo '<td class="empty"></td>';
		echo '</tr></table>';
		echo '</article>';
	}elseif(is_file($path)){
		echo '<article class="content-box">';
		echo '<h2><a class="ajax-link" href="'.$href.'">'.title($info['filename']).'</a></h2>';
		$code_files = array('asm', 'bat', 'cs', 'cpp', 'html', 'js', 'php', 'sh', 'sql', 'txt', 'vb');
		// put a switch here eventually
		if(in_array($info['extension'], $code_files)){
			echo '<code class="'.$info['extension'].'">'.htmlspecialchars(file_get_contents($path)).'</code>';
		}elseif($info['extension'] == 'doc'){
			echo '<p>Google docs embed placeholder</p>';
		}else{
			
		}
		echo '</article>';
	}else{
		include_once VIEWS.'/'.$uri->path.'.php';
	}
?>

<article class="content-box">
	<h2>Copyleft &#175;\_(&#12484;)_/&#175;?</h2>
	<p>
		All code snippets are authored by me unless otherwise noted. All code snippets are unlicensed unless 
		otherwise noted. Please feel free to steal, share, or use them for whatever (including world domination... preferably world domination).
	</p>
</article>