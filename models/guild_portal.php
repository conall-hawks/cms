<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Guild_portal_model extends Model{
		protected $db_name = 'guild';
		
		public function get_members($active = 1, $role = '%'){
			$sql = $this->db->prepare('SELECT * FROM `raid_group` WHERE `active` = '.$active.' AND `role` LIKE \''.$role.'\'');
			$sql->execute();
			return $sql->fetchAll();
		}
		
		public function add_member($name, $class, $role, $leader = 0){
			// Add new raider into the raid group database.
			$sql = $this->db->prepare('INSERT INTO raid_group (name, class, role, leader, ip) VALUES (:name, :class, :role, :leader, :ip)');
			$sql->bindValue(':name', $name, PDO::PARAM_STR);
			$sql->bindValue(':class', $class, PDO::PARAM_STR);
			$sql->bindValue(':role', $role, PDO::PARAM_STR);
			$sql->bindValue(':leader', $leader, PDO::PARAM_INT);
			$sql->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			return $sql->execute();
		}
		
		public function rebuild_db(){
			$this->db->exec('CREATE DATABASE '.$this->db_name);
			$this->db->exec('USE '.$this->db_name);
			$this->db->exec("
				CREATE TABLE IF NOT EXISTS `raid_group` (
				`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Auto-incrementing ID of each character, unique index.',
				`name` varchar(64) NOT NULL COMMENT 'Character name, unique.',
				`class` varchar(64) NOT NULL COMMENT 'Character\'s class.',
				`role` varchar(64) NOT NULL COMMENT 'Character\'s role.',
				`leader` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Character is a leader.',
				`active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'User\'s application status.',
				`ip` varchar(39) NOT NULL DEFAULT '0.0.0.0' COMMENT 'User\'s IP address.',
				PRIMARY KEY (`id`),
				UNIQUE KEY `name` (`name`)
				) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT 'Guild data.';
			");
		}
	}