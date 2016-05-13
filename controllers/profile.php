<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Profile extends Controller{
		public function home(){
			$this->view('profile/home');
		}
		
		public function settings(){
			global $login;
			if(isset($_POST['pass_reset'])){
				$login->change_pass($login->get_username(), $_POST['old_pass'], $_POST['new_pass'], $_POST['new_pass_repeat']);
			}
			
			$this->view('profile/settings');
		}
	}
	