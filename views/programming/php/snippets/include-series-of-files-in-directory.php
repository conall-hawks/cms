<?php
	$filepath = str_replace('.php', '\\*.php', __FILE__);			//* in subdirectory of document's name
	$filepath = str_replace('\\', '/', $filepath);				//all backslashes into forward slashes
	foreach (glob($filepath) as $file) {
		require_once($file);
	}
?>