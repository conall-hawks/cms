<?php 
	defined('BASEPATH') OR die('Direct script access denied.');
	
	class Login extends Controller {
		
		/* User variables. */
		private $status = false;
		private $user_id = NULL;
		private $user_name = '';
		
		/* @var string: The key for cookies, change to reset all cookies. */
		private $cookie_key = COOKIE_KEY;
		
		/* Start the session and begin. */
		public function __construct(){
			session_start();
			$this->model();
			#$this->db->rebuild();
			#$this->db->reset();
			$this->run();
		}
		
		/* Handle flow based on some stuff */
		private function run(){
			//Pre-login methods
			if(!$this->status){
				if(isset($_POST['login'], $_POST['user_name'], $_POST['user_pass'])){
					// User has posted a login form.
					if($this->login($_POST['user_name'], $_POST['user_pass'])){
						// Prevent logging out if the url contains '?logout' during login.
						$no_logout = true;
					}else{
						$this->set_guest();
					}
				}elseif(isset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['login_status'])){
					// User is already logged in.
					$this->login_session($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['login_status']);
				}else{
					$this->set_guest();
				}
			}
			// Post-login methods
			if($this->status){
				if(isset($_GET['logout']) && !isset($no_logout)){
					/* User wants to log out */
					$this->logout();
				}
			}
		}
		
		/*
		 * Login with session data; the user is already logged in.
		 * @return bool: Success status of login.
		 */
		private function login_session($id, $name, $status = 1){
			session_regenerate_id(true);
			/* The user's IP has changed!? Make them re-login. */
			if(isset($_SESSION['user_ip']) && $_SESSION['user_ip'] != $_SERVER['REMOTE_ADDR']){
				$this->feedback = 'Possible hijacking attempt. Please re-login to continue.';
				return false;
			}
			$this->user_id = $id;
			$this->user_name = $name;
			$this->status = $status;
			$this->feedback = 'Logged in with session data.';
			return true;
		}
		
		/*
		 * User has posted a login form. Login with POST data; standard login.
		 * @return bool: Success status of login.
		 */
		private function login($user, $pass, $remember_me = null){
			//session_regenerate_id(true);
			$user = $this->db->get_user($user);
			if(!isset($user['ID'])){
				$this->feedback = 'User does not exist.';
			}elseif($user['FailedLogins'] > 99){
				$this->feedback = 'Too many failed logins; locked account.';
			}elseif(($user['FailedLogins'] > 4) && ($user['FailedLoginTimestamp'] > (time() - 30))){
				$this->feedback = 'Bruteforce protection.';
			}elseif(!password_verify($pass, $user['PassHash'])){
				$this->feedback = 'Incorrect password.';
				$user = $this->db->failed_login($user['ID']);
			}elseif($user['Active'] != 1){
				$this->feedback = 'Inactive account (Awaiting verification email)';
			}else{
				// Everything checks out, the user is now logged in.
				$this->user_id = $_SESSION['user_id'] = $user['ID'];
				$this->user_name = $_SESSION['user_name'] = $user['Name'];
				$this->status = $_SESSION['login_status'] = true;
				
				// Reset login counter.
				$this->db->reset_failed($user['ID']);
				
				// Check if the password needs a stronger hash.
				$this->db->rehash_pass($user['ID']);
				
				// Make a cookie if the user wants us to remember them.
				if($remember_me) $this->newCookie();
				
				$this->feedback = 'Logged-in with POST data.';
				return true;
			}
			return false;
		}
		
		/*
		 * Logs the user out, destroys the user's data, and turns on guest mode.
		 * @return bool: Success status of logout.
		 */
		private function logout(){
			$this->user_id = NULL;
			$this->user_name = '';
			$this->status = false;
			$this->deleteCookie();
			$_SESSION = array();
			session_destroy();
			session_start();
			session_regenerate_id(true);
			$this->set_guest();
			$this->feedback = 'Logged out successfully; session destroyed.';
			return true;
		}
		
		/*
		 * Logs the user out, destroys the user's data, and turns on guest mode.
		 * @return bool: Success status of logout.
		 */
		public function change_pass($name, $old_pass, $new_pass, $new_pass_repeat){
			if($this->login($name, $old_pass)){
				if($new_pass === $new_pass_repeat){
					$this->db->change_pass($name, $new_pass);
					return true;
				}else{
					$this->feedback = 'New passwords do not match.';
				}
			}else{
				$this->feedback = 'Wrong password entered.';
			}
			return false;
		}
		
		/*
		 * Create a new cookie used for automatic login.
		 * @return bool: Success status of cookie creation.
		 */
		private function newCookie(){
			$new_token = hash('sha512', mt_rand());
			$sql = $this->db->prepare('UPDATE users SET rememberme_token = :rememberme_token WHERE id = :id');
			$sql->execute(array(':rememberme_token' => $new_token, ':id' => $_SESSION['user_id']));
			
			/* Generate the new cookie */
			$cookie_prefix = $_SESSION['user_id'] . ':' . $new_token;
			$cookie_hash = hash('sha512', $cookie_prefix . $this->cookie_key);
			$cookie = $cookie_prefix . ':' . $cookie_hash;
			setcookie('rememberme', $cookie, time() + 604800, '/', '.127.0.0.1');
			if(isset($_COOKIE['remember_me'])) return true;
		}
		
		/*
		 * Remove an old cookie used for automatic login.
		 * @return bool: Success status of cookie deletion.
		 */
		private function deleteCookie($id = NULL){
			if($id === NULL && isset($_SESSION['user_id'])) $id = $_SESSION['user_id'];
			$new_token = hash('sha512', mt_rand());
			$this->db->forget_user($id);
			
			/* Invalidate old cookie by making it 10 years old */
			setcookie('rememberme', false, time() - 13140000, '/', '.127.0.0.1');
			if(isset($_COOKIE['remember_me']) && $_COOKIE['remember_me'] == false) return true;
			return false;
		}
		
		/**
		 * Get the login status of the user.
		 * @return bool: Login status of the user.
		 */
		public function getStatus(){
			return $this->status;
		}
		public function get_status(){
			return $this->status;
		}
		
		/**
		 * Tell me if the user has root privileges.
		 * @return bool: User is/isn't root.
		 */
		public function isRoot(){
			/* TEMPORARY */
			if(!isset($_SESSION['login_status']) || $_SESSION['login_status'] != 1 || $this->status != 1) return 0;
			if($this->get_username() == 'Admin' || $this->get_username() == 'PasT') return 1;
			return 0;
		}
		/* Alias for isRoot. */
		public function isAdmin(){
			return $this->isRoot();
		}
		public function is_admin(){
			return $this->isRoot();
		}
		
		public function is_user(){
			return $this->status;
		}
		
		public function get_privilege_level($user_id = null){
			return $this->isRoot();
		}
		
		/**
		 * Get the ID of the user.
		 * @return int: ID of the user.
		 */
		public function getId(){
			return $this->user_id;
		}
		public function get_userid(){
			return $this->user_id;
		}
		
		/*
		 * Get the name of the user.
		 * @return string: Name of the user.
		 */
		public function getUserName(){
			return $this->user_name;
		}
		public function get_username(){
			return $this->user_name;
		}
		
		private function setUserName($name){
			if(!$this->status) return false;
			if(empty($name) || !preg_match("/[a-zA-Z][a-zA-Z0-9.\-_]{5,31}/", $name) || $name == $_SESSION['user_name']){
				/* Invalid username supplied */
				return false;
			}
		}
		private function getUserEmail(){
			return $this->user_email;
		}
		
		public function getGravatar(){
			
		}
		
		public function set_guest(){
			$this->user_name = $_SESSION['user_name'] = 'Guest-'.substr(session_id(), 0, 4);
			$this->feedback = 'You are not logged in.';
		}
		
	}