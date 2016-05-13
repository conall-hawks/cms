<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Security extends Controller {
		public $use_cms = false;
		public $csrf_token = '';
		public $feedback = '';
		
		/* Do it baby! */
		public function __construct(){
			// Enable a stronger session ID hashing algorithm.
			#if(ini_get('session.hash_function') != 'sha512') ini_set('session.hash_function', 'sha512');
			
			// Generate a CSRF token.
			$this->set_csrf();
		}
		
		/* Generates a new CSRF token. */
		public function set_csrf(){
			if(phpversion() >= 7){
				$this->csrf_token = hash('sha512', random_bytes(64));
			}else{
				$this->csrf_token = hash('sha512', rand());
			}
		}
		
		/* Perform a simple XSS scrub on a string. */
		public function cleanse($string){
			$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
			return $string;
		}
		
		/* Placeholder for a list of banned IPs. */
		public function ip_banned(){
			return false;
		}
	}