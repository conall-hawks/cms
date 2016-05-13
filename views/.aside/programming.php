<h2>Sections</h2>
<a class="ajax-link" href="/programming">Languages</a>
<?php 
	if(!function_exists('dirs_recursive')){
		function dirs_recursive($dir){
			global $uri;
			echo '<ul>';
			$files = glob($dir.'/*');
			$files = glob_sort($files);
			foreach($files as $index => $file){
				$file = str_replace('\\', '/', $file);
				$info = pathinfo($file);
				if(strpos($file, '.php') !== false && is_dir(rtrim($file, '.php'))) continue;
				$href = ltrim($file, VIEWS.'/');
				$name = title($info['filename']);
				$label = base64_encode($file);
				if(preg_match('/^.+(\/\.|\/\.\.)$/', $file)) continue;
				if(is_dir($file)){
					echo '
						<li>
							<label for="'.$label.'="><a class="ajax-link" href="/'.$href.'" title="'.$name.'">'.$name.'</a></label>
							<input type="checkbox" for="'.$label.'" '.(strpos($uri->path, $href) !== false ? 'checked' : '').' />
					';
					dirs_recursive($file);
					echo '</li>';
				}elseif(is_file($file)){
					$info['extension'] = strtolower($info['extension']);
					if($info['extension'] == 'url'){
						$url = file_get_contents($file);
						$match_found = preg_match("((\w+:\/\/)[-a-zA-Z0-9:@;?&=\/%\+\.\*!'\(\),\$_\{\}\^~\[\]`#|]+)", $url, $url);
						if($match_found) $href = htmlspecialchars($url[0]);
						echo '<li class="file"><a href="http://anonym.to/?'.$href.'" target="_blank" title="'.$name.'">'.$name.'</a></li>';
					}else{
						$href = str_ireplace('.'.$info['extension'], '', $href);
						echo '<li class="file"><a class="ajax-link" href="/'.$href.'" title="'.$name.'">'.$name.'</a></li>';
					}
				}
			}
			echo '</ul>';
		}
	}
	dirs_recursive(VIEWS.'/programming');
?>
<script>
	setInterval(function(){
		$("#left-box li").find("label > a").each(function(index, elem){
			if(elem.href == window.location.href){
				$(elem).parent().next().attr("checked", "checked");
			}
		});
	}, 500);
</script>