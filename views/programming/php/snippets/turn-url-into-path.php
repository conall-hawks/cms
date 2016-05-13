<?php 
	/* 
	 * Prepends pathing information and converts to lowercase.
	 * @param $link string: A string to be turned into a URL. 
	 * 
	 * @return string: The string in a a URI-friendly form.
	 */
	if(!function_exists('pathify')){
		function pathify($string){
			$link = str_replace(' ', '-', $string);
			$link = str_replace('.php', '', $link);
			$link = strtolower($link);
			return $link;
		}
	}