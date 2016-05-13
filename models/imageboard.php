<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Imageboard_model extends Model{
		protected $db_name = 'imageboard';
		
		public function __construct(){
			parent::__construct();
			#$this->rebuild();
			#$this->reset();
		}
		
		public function get_recent_threads(){
			//replace me with query
			$threads = $this->db->prepare("SELECT * FROM `post` WHERE `Parent` = 0 ORDER BY `Stickied`, `Timestamp` DESC LIMIT 10;");
			$threads->execute();
			return $threads->fetchAll(PDO::FETCH_ASSOC);
		}
		
		public function get_threads($board){
			$threads = $this->db->prepare("SELECT * FROM `post` WHERE `Board` = :board AND `Parent` = 0 ORDER BY `Stickied`, `Timestamp` DESC LIMIT 10;");
			$threads->execute(array(':board' => $board));
			return $threads->fetchAll(PDO::FETCH_ASSOC);
		}
		
		public function get_thread($thread){
			$threads = $this->db->prepare("SELECT DISTINCT * FROM `post` WHERE `ID` = :id AND `Parent` = 0 ORDER BY `Stickied`, `Timestamp`;");
			$threads->execute(array(':id' => $thread));
			return $threads->fetchAll(PDO::FETCH_ASSOC);
		}
		
		public function get_replies($thread, $limit = NULL){
			$sql = "SELECT * FROM `post` WHERE `Parent` = :parent ORDER BY `Stickied`, `Timestamp`".(is_integer($limit) ? " LIMIT ".$limit : NULL).";";
			$replies = $this->db->prepare($sql);
			$replies->execute(array(':parent' => $thread));
			return $replies->fetchAll(PDO::FETCH_ASSOC);
		}
		
		public function add_post($post){
			global $uri;
			if($post['author'] = '') $post['author'] = NULL;
			if($post['tripcode'] = '') $post['tripcode'] = NULL;
			if($post['email'] = '') $post['email'] = NULL;
			if($post['subject'] = '') $post['subject'] = NULL;
			if($post['message'] = '') $post['message'] = NULL;
			if(!isset($post['file'])) $post['file'] = $post['file_hex'] = $post['file_name'] = $post['file_size'] = $post['image_width'] = $post['image_height'] = NULL;
			$sql = $this->db->prepare("
				INSERT INTO `post` SET 
					`Board` = :board, 
					`Parent` = :parent, 
					`Timestamp` = :time, 
					`IP` = :ip, 
					`Author` = :author, 
					`AuthorBlock` = :block, 
					`Tripcode` = :trip, 
					`Email` = :email, 
					`Password` = :password, 
					`Subject` = :subject, 
					`Message` = :message, 
					`File` = :file, 
					`FileHex` = :hex, 
					`FileName` = :name,
					`FileSize` = :size, 
					`ImageWidth` = :width, 
					`ImageHeight` = :height, 
					`Moderated` = :mod
				;");
			$sql->execute(array(
			':board' => explode('/', $uri->path)[1], 
			':parent' => $post['parent'], 
			':time' => time(),
			':ip' => $_SERVER['REMOTE_ADDR'], 
			':author' => $post['author'], 
			':block' => $post['author_block'], 
			':trip' => $post['tripcode'], 
			':email' => $post['email'],
			':password' => $post['password'], 
			':subject' => $post['subject'], 
			':message' => $post['message'], 
			':file' => $post['file'], 
			':hex' => $post['file_hex'], 
			':name' => $post['file_name'], 
			':size' => $post['file_size'], 
			':width' => $post['image_width'], 
			':height' => $post['image_height'], 
			':mod' => $post['moderated']));
			return $this->db->lastInsertId();
		}
		
		function thread_exists($id) {
			$sql = $this->db->prepare("SELECT COUNT(*) FROM `post` WHERE `ID` = :id AND `Parent` = 0;");
			$sql->execute(array(':id' => $id));
			return $sql->fetchColumn();
		}
		
		function check_duplicate($hex) {
			$sql = $this->db->prepare("SELECT COUNT(*) FROM `post` WHERE `FileHex` = :hex;");
			$sql->execute(array(':hex' => $hex));
			return $sql->fetchColumn();
		}
		
		public function rebuild(){
			// The site database.
			$this->db->exec("DROP DATABASE IF EXISTS `".$this->db_name."`");
			$this->db->exec("CREATE DATABASE `".$this->db_name."` DEFAULT CHARACTER SET=utf8 DEFAULT COLLATE=utf8_unicode_ci");
			$this->db->exec("USE `".$this->db_name."`");
			
			// Table to hold the posts.
			$result = $this->db->exec("
				CREATE TABLE IF NOT EXISTS `post`(
					`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each post, unique index.',
					`Board` CHAR(64) NOT NULL COMMENT 'Short name of the board the post was made in.',
					`Parent` INT UNSIGNED DEFAULT '0' COMMENT 'ID of the parent post. 0 means this post is a parent.',
					`Timestamp` INT UNSIGNED NOT NULL COMMENT 'Creation date & time in UNIX format.',
					`Bumped` TINYINT UNSIGNED DEFAULT '0' COMMENT 'Number of times this post has been bumped.',
					`IP` VARCHAR(39) NOT NULL COMMENT 'IP address associated with the post.',
					`Author` VARCHAR(64) COMMENT 'Name of the post\'s author.',
					`AuthorBlock` VARCHAR(128) COMMENT 'Name of the post\'s author in styled HTML.',
					`Tripcode` VARCHAR(10) COMMENT 'Tripcode of a pseudonymous author.',
					`Email` VARCHAR(96) COMMENT 'Email of the post\'s author.',
					`Password` VARCHAR(255) COMMENT 'Password for post management & deletion.',
					`Subject` VARCHAR(64) COMMENT 'Subject of the post.',
					`Message` TEXT COMMENT 'Message of the post.',
					`File` VARCHAR(255) COMMENT 'File associated with the post.',
					`FileHex` VARCHAR(64) COMMENT 'The file\'s hex value. Used for duplicate detection.',
					`FileName` VARCHAR(128) COMMENT 'The file\'s original name.',
					`FileSize` INT UNSIGNED DEFAULT '0' COMMENT 'The size of the file in bytes.',
					`ImageWidth` SMALLINT UNSIGNED DEFAULT '0' COMMENT 'Width of the image in pixels.',
					`ImageHeight` SMALLINT UNSIGNED DEFAULT '0' COMMENT 'Height of the image in pixels.',
					`Stickied` TINYINT(1) DEFAULT '0' COMMENT 'Is the post sticky?',
					`Moderated` TINYINT(1) DEFAULT '0' COMMENT 'Has the post been moderated?'
				) AUTO_INCREMENT=24978 COMMENT 'Imageboard posts';
			");
			return $result;
		}
		
		public function reset(){
			// Create some preliminary posts.
			return $this->db->exec("INSERT INTO `post` SET `Board` = 'b', `Parent` = 0, `Timestamp` = 1456196751, `IP` = '127.0.0.1', `Author` = 'admin', `AuthorBlock` = '<a class=\"ajax-link admin\" href=\"/profile/admin\">Admin</a>', `Subject` = 'Da Rulez', `Message` = 'Keep it clean.', `File` = '1456196751856.jpg', `FileHex` = '968329ec4a37c89ec046f7eac3cc74bf', `FileName` = 'Candy.jpg', `FileSize` = '872646', `ImageWidth` = '1920', `ImageHeight` = '1080', `Stickied` = 1, `Moderated` = 0;");
			
		}
	}