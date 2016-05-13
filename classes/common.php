<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	/* 
	 * Grabs a class file and instantiates the class. If the requested class 
	 * does not exist it is instantiated and set in a global variable. If it 
	 * has previously been instantiated the variable is returned.
	 * 
	 * @param	class			string:	The name of the class being requested.
	 * @param	directory		string:	The directory where the class is located.
	 * @param	arguments		string:	Arguments for the class' constructor.
	 * @return	classes[class]	array:	The requested class.
	 */
	if(!function_exists('load_class')){
		function &load_class($class, $directory = CLASSES, $parameters = NULL){
			// Keep track of loaded classes.
			global $classes;
			
			// If the class already exists, return that instance.
			if(isset($classes[$class])) return $classes[$class];
			
			// Ensure the class file exists, then include it.
			$file = $directory.'/'.$class.'.php';
			if(!file_exists($file)){
				#die('Unable to locate the "'.$class.'" class file. Expected at: "'.$file.'".');
				class _Anonymous extends Controller{
					public $feedback = 'Could not find class file: defaulted to an anonymous controller.';
				};
				$class = '_Anonymous';
			}else{
				require_once $file;
			}
			
			// Ensure the class exists.
			if(!class_exists($class)) die('Unable to locate class "'.ucfirst($class).'" in file: '.$file);
			
			// Add the class to a list of loaded classes. Include parameters if there are any.
			$classes[$class] = isset($parameters) ? new $class($parameters) : new $class();
			return $classes[$class];
		}
	}
	
	/* 
	 * Orders an array based on user-defined strings.
	 * 
	 * @param	$array	array:	The array that will be reordered. 
	 * @param	$order	array:	The items to reorder, listed first-to-last.
	 * @return	$array	array:	The $array reordered according to $order.
	 */
	if(!function_exists('sortify')){
		function sortify($array, $order){
			foreach($order as $oldkey => $needle){
				foreach($array as $newkey => $haystack){
					// In case we're sorting globs.
					$haystack = pathinfo($haystack, PATHINFO_FILENAME);
					if($haystack == $needle){
						$temp = $array[$oldkey];
						$array[$oldkey] = $array[$newkey];
						$array[$newkey] = $temp;
					}
				}
			}
			return $array;
		}
	}
	
	/* 
	 * Prepends pathing information and converts to lowercase.
	 * 
	 * @param $link string: A string to be turned into a URL.
	 * @return string: The string in a a URI-friendly form.
	 */
	if(!function_exists('path')){
		function path($link){
			$link = str_replace(' ', '-', $link);
			$link = str_replace('_', '-', $link);
			$link = str_replace('.php', '', $link);
			$link = ltrim($link, 'views/');
			$link = strtolower($link);
			return $link;
		}
	}
	
	/* Glob sort. */
	if(!function_exists('glob_sort')) eval('?><?php '.file_get_contents(VIEWS.'/programming/php/snippets/glob-sort.php').'?>');
	
	/* Title case function. */
	if(!function_exists('title')) eval('?><?php '.file_get_contents(VIEWS.'/programming/php/snippets/title-case.php').'?>');
	
	/* URL to link function. */
	if(!function_exists('linkify')) eval('?><?php '.file_get_contents(VIEWS.'/programming/php/snippets/url-to-link.php').'?>');
	
	
	/* 
	 * Generates pathing links.
	 */
	if(!function_exists('generate_pathing_links')){
		function generate_pathing_links(){
			global $uri;
			$paths = explode('/', $uri->path);
			$paths_len = count($paths);
			$href = $output = '';
			for($i = 0; $i < $paths_len; $i++){
				$href .= '/'.$paths[$i];
				if($i == $paths_len - 1){
					$output .= '/'.$paths[$i];
				}else{
					$output .= '/<a class="ajax-link" href="'.$href.'">'.$paths[$i].'</a>';
				}
			}
			return $output;
		}
	}
	