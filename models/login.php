<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Login_model extends Model {
		protected $db_name = 'login';
		
		/* @var int: Hashing algorithm factor (higher the better, costing more CPU). */
		private $hash_factor = 10;
		
		/* Get login information for a user. */
		public function get_user($name, $email = NULL){
			$sql = $this->db->prepare("SELECT `ID`, `Name`, `Email`, `PassHash`, `FailedLogins`, `FailedLoginTimestamp`, `Active` FROM `user` WHERE `Name` = :name OR `Email` = :name LIMIT 1");
			$sql->execute(array(':name' => trim($name), ':email' => trim($email)));
			return $sql->fetch();
		}
		
		/* Increment the failed login counter and set a failed login timestamp. */
		public function failed_login($id){
			$sql = $this->db->prepare("UPDATE `user` SET `FailedLogins` = `FailedLogins` + 1, `FailedLoginTimestamp` = :time WHERE `ID` = :id LIMIT 1;");
			return $sql->execute(array(':time' => time(), ':id' => $id));
		}
		
		/* Reset the failed login counter. */
		public function reset_failed($id){
			$sql = $this->db->prepare("UPDATE `user` SET `FailedLogins` = 0 WHERE `ID` = :id LIMIT 1;");
			return $sql->execute(array(':id' => $id));
		}
		
		/* Generates a password. */
		public function generate_pass_hash($pass){
			return password_hash($pass, PASSWORD_DEFAULT, array('cost' => $this->hash_factor));
		}
		
		/* Changes a user's password. */
		public function change_pass($id, $pass){
			if(is_string($id)) $id = $this->get_user($id)['ID'];
			
			$pass_hash = $this->generate_pass_hash($pass);
			$sql = $this->db->prepare("UPDATE `user` SET `PassHash` = :pass_hash WHERE `ID` = :id");
			return $sql->execute(array(':pass_hash' => $pass_hash, ':id' => $id));
		}
		
		/* Check a password's hash integrity. */
		public function rehash_pass($id){
			$pass_hash = $this->db->prepare("SELECT `PassHash` FROM `user` WHERE `ID` = :id LIMIT 1;");
			$pass_hash->execute(array(':id' => $id));
			$pass_hash = $pass_hash->fetch()['PassHash'];
			if(password_needs_rehash($pass_hash, PASSWORD_DEFAULT, array('cost' => $this->hash_factor))){
				$pass_hash = $this->generate_pass_hash($pass);
				$sql = $this->db->prepare("UPDATE `user` SET `PassHash` = :pass_hash WHERE `ID` = :id LIMIT 1;");
				return $sql->execute(array(':pass_hash' => $pass_hash, ':id' => $id));
			}
			return false;
		}
		
		/* Remove a cookie token. */
		public function forget_user($id){
			$sql = $this->db->prepare("UPDATE `user` SET `RememberMeToken` = NULL WHERE `ID` = :id");
			return $sql->execute(array(':id' => $id));
		}
		
		public function add_user($name, $email, $pass){
				// Generate password hash.
				$pass_hash = generate_pass_hash($pass);
				
				// Generate random hash for email verification (40 char string).
				$activation_hash = hash('sha512', uniqid(mt_rand(), true));
				
				// Write new user data into database.
				$sql = $this->db->prepare("INSERT INTO `user` SET `Name` = :name, `PassHash` = :pass_hash, `Email` = :email, `ActivationHash` = :activation_hash, `IP` = :ip, `RegistrationTimestamp` = :time");
				$sql->execute(array(':name' => $name, ':email' => $email, ':pass_hash' => $pass_hash, ':activation_hash' => $activation_hash, ':ip' => $_SERVER['REMOTE_ADDR'], ':time' => time()));
				
				// Return some information about our new user.
				$user['ID'] = $this->db->lastInsertId();
				$user['ActivationHash'] = $pass_hash;
				return $user;
		}
		
		public function remove_user($id){
			$sql = $this->db->prepare("DELETE FROM `user` WHERE `ID` = :id");
			return $sql->execute(array(':id' => $id));
		}
		
		public function rebuild(){
			// Table to hold the users.
			$this->db->exec("DROP DATABASE IF EXISTS `".$this->db_name."`");
			$this->db->exec("CREATE DATABASE `".$this->db_name."`");
			$this->db->exec("USE `".$this->db_name."`");
			
			$this->db->exec("
				CREATE TABLE IF NOT EXISTS `user` (
				`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each user, unique index.',
				`Name` VARCHAR(64) NOT NULL UNIQUE KEY COMMENT 'Username, unique.',
				`Email` VARCHAR(64) NOT NULL UNIQUE KEY COMMENT 'User\'s email, unique.',
				`PassHash` CHAR(255) NOT NULL COMMENT 'User\'s password in a salted and hashed format.',
				`Active` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'User\'s activation status.',
				`ActivationHash` VARCHAR(40) COMMENT 'User\'s email verification hash.',
				`PrivilegeLevel` TINYINT(1) NOT NULL DEFAULT 9 COMMENT 'User\'s privilege level.',
				`PassResetHash` VARCHAR(40) COMMENT 'User\'s password reset code.',
				`PassResetTimestamp` INT UNSIGNED COMMENT 'Timestamp of the password reset request.',
				`RememberMeToken` VARCHAR(64) COMMENT 'User\'s remember-me cookie token.',
				`FailedLogins` TINYINT(2) NOT NULL DEFAULT 0 COMMENT 'User\'s failed login attempts.',
				`FailedLoginTimestamp` INT UNSIGNED COMMENT 'UNIX timestamp of the last failed login attempt.',
				`RegistrationTimestamp` INT UNSIGNED COMMENT 'UNIX timestamp of user\'s registration.',
				`IP` VARCHAR(39) NOT NULL DEFAULT '0.0.0.0' COMMENT 'User\'s IP address.'
				) CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'User data';
			");
		}
		
		public function reset(){
			/* Create an admin with the default password: 'password'. */
			$this->db->exec("INSERT INTO `user` SET `ID` = 1, `Name` = 'Admin', `PassHash` = '$2y$10\$GhYF9BlSKC4crlWj7Z8mcu56k7PyE65jBBi9jcM2i0Fb0dApwfSZW', `Email` = 'admin@cenari.us', `Active` = 1, `PrivilegeLevel` = 1, `ip` = '::1', `RegistrationTimestamp` = now();");
			
			/* Create some moderators with the default password: 'password'. */
			$this->db->exec("INSERT INTO `user` SET `ID` = 2, `Name` = 'Mod1', `PassHash` = '$2y$10\$GhYF9BlSKC4crlWj7Z8mcu56k7PyE65jBBi9jcM2i0Fb0dApwfSZW', `Email` = 'mod1@cenari.us', `Active` = 1, `PrivilegeLevel` = 2, `ip` = '::1', `RegistrationTimestamp` = now();");
			$this->db->exec("INSERT INTO `user` SET `ID` = 3, `Name` = 'Mod2', `PassHash` = '$2y$10\$GhYF9BlSKC4crlWj7Z8mcu56k7PyE65jBBi9jcM2i0Fb0dApwfSZW', `Email` = 'mod2@cenari.us', `Active` = 1, `PrivilegeLevel` = 2, `ip` = '::1', `RegistrationTimestamp` = now();");
		}
	}