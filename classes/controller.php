<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	abstract class Controller {
		/* Title of the page. */
		protected $title = TITLE.'';
		
		/* Handle to a database. */
		public $db = NULL;
		
		/* Content Management System switch. */
		public $use_cms = false;
		
		/* Feedback for debugging. */
		public $feedback = 'Nothing to see here.';
		
		/* 
		 * Loads and instantiates a model.
		 * 
		 * @file	string:	The path to a model file.
		 */
		public function model($file = NULL){
			// Model filename defaults to the name of the controller class.
			if($file == NULL) $file = strtolower(get_class($this));
			if(strpos($file, '.php') === false) $file .= '.php';
			
			// Model filename may not contain underscores; change underscores into dashes.
			$file = str_replace('-', '_', $file);
			
			// Ensure the model file exists.
			$file = MODELS.'/'.$file;
			if(!file_exists($file)) die('Model not found. Expected at: '.$file);
			
			// Get the global classes.
			global $classes;
			if($classes !== NULL) foreach(array_keys($classes) as $class) global ${$class};
			
			// Include the model.
			require_once $file;
			
			// Ensure the model class exists.
			$model = ucfirst(str_replace('.php', '', basename($file))).'_model';
			if(!class_exists($model)) die('Unable to locate model class "'.$model.'" in file: '.$file);
			
			// Instantiate the model.
			$this->db = new $model;
			return true;
		}
		/* Alias for model. */
		public function db(){
			return $this->model();
		}
		
		public function view($file = NULL){
			// View filename defaults to the URI path.
			global $uri;
			if($file == NULL) $file = strtolower($uri->path);
			if(is_array($file)) $file = implode('/', $file);
			if(strpos($file, '.php') === false) $file .= '.php';
			
			// Ensure the model file exists.
			$file = VIEWS.'/'.$file;
			if(!file_exists($file)) $this->feedback = 'View not found. Expected at: '.$file;
			
			// Get the global classes.
			global $classes;
			if($classes !== NULL) foreach(array_keys($classes) as $class) global ${$class};
			
			// Get the requested file
			ob_start();
			if(file_exists($file)) include_once $file;
			if($this->use_cms) include_once TEMPLATES.'/cms.php';
			$content = ob_get_clean();
			
			// Aside filename defaults to the name of the controller class.
			$aside = strtolower(get_class($this));
			$aside = str_replace('_', '-', $aside);
			$aside = VIEWS.'/.aside/'.$aside.'.php';
			
			// Build aside nav.
			ob_start();
			if(file_exists($aside)){
				include_once $aside;
			}else{
				include_once VIEWS.'/.aside/.default.php';
			}
			$aside = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
			
			// Attempt to use gzip compression.
			ob_start('ob_gzhandler');
			
			// If the request was AJAX, load only the requested content.
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
				echo $content;
				if(!isset($_SESSION['controller']) || $_SESSION['controller'] !== $uri->class){
					$aside = str_replace('\'', '\\\'', $aside);
					$aside = str_replace('</script>', '<\/script>', $aside);
					#echo '<script>document.getElementById("left-box").innerHTML = unescape(\''.$aside.'\');'.'</script>';
					echo '<script>$("#left-box").html(\''.$aside.'\');</script>';
				}
				echo '<script>document.title = " '.$this->title.'";</script>';
				echo '<script>document.getElementById("header-h2").innerHTML = \''.generate_pathing_links().'\';</script>';
				echo '<script>document.getElementById("feedback").textContent = "'.str_replace('"', '\\"', $this->feedback).'";</script>';
				echo '<script>document.getElementById("top-link").href = "/'.$uri->path.'#top";</script>';
				echo '<script>document.getElementById("benchmark").textContent = "'.$benchmark->calculate('start').'";</script>';
			}else{
				file_exists(TEMPLATES.'/head.php') ? require_once TEMPLATES.'/head.php' : NULL;
				file_exists(TEMPLATES.'/header.php') ? require_once TEMPLATES.'/header.php' : NULL;
				echo '<section class="content">'.$content.'</section>';
				echo '<aside id="left-box" class="left-box">'.$aside.'</aside>';
				file_exists(TEMPLATES.'/footer.php') ? require_once TEMPLATES.'/footer.php' : NULL;
			}
			$_SESSION['controller'] = $uri->class;
		}
	}