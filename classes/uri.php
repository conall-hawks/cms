<?php 
	defined('BASEPATH') OR die('Direct script access denied.');
	
	class URI{
		/* @var	string:	Current URI path. */
		public $path = '';
		
		/* @var	string:	Current class name. */
		public $class = '';
		
		/* @var	string:	Current method name. */
		public $method = 'view';
		
		/* @var	string:	Arguments to be passed to the method. */
		public $arguments = NULL;
		
		/*
		 * The only way to access this class is via instantiation.
		 */
		public function __construct(){
			$this->set_route();
		}
		
		/* 
		 * Determine what should be served based on the URI request, as well as 
		 * any "routes" that have been set in the routing config file.
		 */
		private function set_route(){
			// Get the URL.
			$scheme = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
			$url = $scheme.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			
			// Sanitize and validate the URL.
			$url = filter_var($url, FILTER_SANITIZE_URL);
			if(filter_var($url, FILTER_VALIDATE_URL) === false) die('"'.$url.'" is not a valid URL.');
			
			// Parse the URL.
			$uri = parse_url($url);
			$path = isset($uri['path']) ? $uri['path'] : '';
			
			//Remove .php and .htm/.html from the pathing
			$path = str_replace(array('.php', '.htm', '.html'), '', $path);
			
			// If there is no path or the path is "/" then assume we are at "/index".
			$path == '' || $path == '/' ? $path = '/index' : null;
			
			// URL cleaning; remove leading and trailing slashes.
			$path = trim($path, '/');
			
			// Set the $path for convenient access to the URI, basically a cleaned up version of $_SERVER['REQUEST_URI'].
			$this->path = $path;
			
			// Load the routes configuration to see if the route is mapped.
			$route = array();
			$file = 'config/routes.php';
			file_exists($file) ? require_once($file) : die('Routes configuration file expected at "'.$file.'".');
			
			// Search for a literal match.
			if(isset($route[$path])){
				$path = $route[$path];
			}
			
			// Search for a wildcard match.
			foreach($route as $key => $value){
				// Convert wildcards to regex.
				$regex = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $key);
				
				// Did we find a match?
				if(preg_match('#^'.$regex.'$#', $path)){
					if(strpos($value, '$') !== false){
						// Replace the path.
						$path = preg_replace('#^'.$regex.'$#', $value, $path);
						break;
					}else{
						//die('There was an error processing wildcards for route "$route['.$key.'] = '.$value.'".');
						$path = preg_replace('#^'.$regex.'$#', $value, $path);
						break;
					}
				}
			}
			
			$this->set_request(explode('/', $path));
			return;
		}
		
		/* 
		 * Set the request; assigns a class to be used. Also assigns a method 
		 * to be executed, along with any arguments.
		 * For example: http://localhost/[class]/[method]/[arguments]
		 * 
		 * @param $request array: The URI path.
		 */
		private function set_request($request){
			if(isset($request[0])) $this->class = str_replace('-', '_', $request[0]);
			if(isset($request[1])) $this->method = str_replace('-', '_', $request[1]);
			
			// If there is more than one argument, pass them as an array.
			if(isset($request[3])) $request[2] = array_slice($request, 2);
			if(isset($request[2])) $this->arguments = $request[2];
		}
	}