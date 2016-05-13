<h2>Sections</h2>
<ul>
	<?php 
		libxml_use_internal_errors(true);
		$articles = new DOMDocument();
		$articles->loadHtmlFile(VIEWS.'/'.$uri->path.'.php');
		$articles = $articles->getElementsByTagName('article');
		foreach($articles as $index => $article){
			$title = $article->getElementsByTagName('h2')[0];
			if($title){
				$title = str_replace(':', '', $title->nodeValue);
				echo '<li><a href="'.$uri->path.'#'.path($title).'">'.$title.'</a></li>';
			}
		}
	?>
</ul>