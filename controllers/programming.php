<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Programming extends Controller {
		public function view($file = NULL){
			parent::view('programming.php');
		}
	}