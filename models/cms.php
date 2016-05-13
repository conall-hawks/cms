<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Cms_model extends Model{
		protected $db_name = 'content';
		
		public function insert_post($path, $title, $content, $priority = 255){
			global $uri;
			global $login;
			$post = $this->db->prepare("INSERT INTO `post` SET `Parent` = :parent, `Path` = :path, `Timestamp` = :time, `Priority` = :priority, `Author` = :author, `IP` = :ip, `Title` = :title, `Content` = :content;");
			$post->bindValue(':parent', $uri->path);
			$post->bindValue(':path', $uri->path.'/'.trim($path, '/'));
			$post->bindValue(':time', time());
			$post->bindValue(':priority', $priority);
			$post->bindValue(':author', $login->get_username());
			$post->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
			$post->bindValue(':title', $title);
			$post->bindValue(':content', $content);
			$post = $post->execute();
			return $post;
		}
		
		public function delete_post($id){
			$post = $this->db->prepare("DELETE FROM `post` WHERE `ID` = :id");
			return $post->execute(array(':id' => $id));
		}
		
		public function get_post($path = NULL){
			if($path == NULL){
				global $uri;
				$path = $uri->path;
			}
			$post = $this->db->prepare("SELECT * FROM `post` WHERE `Path` = :path LIMIT 1;");
			$post->bindValue(':path', $path);
			$post->execute();
			$post = $post->fetchAll(PDO::FETCH_ASSOC);
			return $post;
		}
		
		public function get_children($path = NULL){
			if($path == NULL){
				global $uri;
				$path = $uri->path;
			}
			$posts = $this->db->prepare("SELECT * FROM `post` WHERE `Parent` = :parent ORDER BY `Priority`, `Timestamp` DESC;");
			$posts->bindValue(':parent', $path);
			$posts->execute();
			$posts = $posts->fetchAll(PDO::FETCH_ASSOC);
			return $posts;
		}
		
		public function rebuild(){
			// The site database.
			$this->db->exec("DROP DATABASE IF EXISTS ".$this->db_name);
			$this->db->exec("CREATE DATABASE ".$this->db_name);
			$this->db->exec("USE ".$this->db_name);
			
			// Table to hold the posts.
			$this->db->exec("
				CREATE TABLE IF NOT EXISTS `post`(
					`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each post, unique index.',
					`Parent` TEXT NOT NULL COMMENT 'URL of the parent.',
					`Path` TEXT NOT NULL COMMENT 'URL of the post.',
					`Timestamp` INT UNSIGNED NOT NULL COMMENT 'Creation date & time in UNIX format.',
					`Priority` INT UNSIGNED NOT NULL DEFAULT 255 COMMENT 'Order by which the post appears.',
					`Author` VARCHAR(255) NOT NULL COMMENT 'Name of the author.',
					`IP` VARCHAR(39) NOT NULL COMMENT 'IP address associated with the post.',
					`Title` VARCHAR(255) NOT NULL COMMENT 'Title of the post.',
					`Content` LONGTEXT COMMENT 'Content of the post in HTML.'
				) CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Content Management System';
			");
		}
		
		public function reset(){
			$lorem_ipsum = '<p><video src="https://r5---sn-vgqs7n7s.googlevideo.com/videoplayback?fexp=9405985%2C9416126%2C9420452%2C9422596%2C9423038%2C9423581%2C9423661%2C9423662%2C9425970%2C9427902%2C9428222%2C9428544%2C9428967%2C9431012%2C9431463%2C9431521%2C9431549%2C9431730%2C9431862&nh=IgpwcjAxLm9yZDEyKhQyNjEwOjE4OjExMTo0MDAwOjo4NQ&dur=7017.557&ratebypass=yes&sver=3&requiressl=yes&source=youtube&initcwndbps=352500&ipbits=0&sparams=dur%2Cid%2Cinitcwndbps%2Cip%2Cipbits%2Citag%2Clmt%2Cmime%2Cmm%2Cmn%2Cms%2Cmv%2Cnh%2Cpl%2Cratebypass%2Crequiressl%2Csource%2Cupn%2Cexpire&lmt=1444999489583281&id=o-AAFQb-9fr_WctYAin6XoKbS7ZUF_Ge3SyjVcT6ljBpKV&signature=CF1DDDA82E5ADF82853F4ED061B747A8D5234FB0.3AB40028F856618004B372BFCEA06842FEC64826&ms=au&mt=1458533676&mv=m&upn=QxK011f3_do&expire=1458555360&pl=44&ip=2600%3A1000%3Ab123%3Afeda%3A29cf%3Adc48%3A5e5a%3A3357&mm=31&mn=sn-vgqs7n7s&itag=18&key=yt6&mime=video%2Fmp4&fallback_host=tc.v24.cache7.googlevideo.com&quality=medium" controls></video>
			Delete me, you fool! Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium 
			doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto 
			beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut 
			fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. </p><p>Neque porro 
			quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam 
			eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima 
			veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi 
			consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae 
			consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>';
			
			$this->db->exec("INSERT INTO `post` SET `ID` = 1, `Parent` = 'starcraft-2', `Path` = 'starcraft-2/old-post', `Timestamp` = ".(time() - 10).", `Priority` = 1, `Author` = 'admin', `IP` = '".$_SERVER['REMOTE_ADDR']."', `Title` = 'Old Post With High Priority', `Content` = '".$lorem_ipsum."';");
			$this->db->exec("INSERT INTO `post` SET `ID` = 2, `Parent` = 'starcraft-2', `Path` = 'starcraft-2/new-post', `Timestamp` = ".(time() - 5).", `Priority` = 255, `Author` = 'admin', `IP` = '".$_SERVER['REMOTE_ADDR']."', `Title` = 'Newer Post', `Content` = '".$lorem_ipsum."';");
			$this->db->exec("INSERT INTO `post` SET `ID` = 3, `Parent` = 'starcraft-2', `Path` = 'starcraft-2/newer-post', `Timestamp` = ".(time() - 1).", `Priority` = 255, `Author` = 'admin', `IP` = '".$_SERVER['REMOTE_ADDR']."', `Title` = 'New Post', `Content` = '".$lorem_ipsum."';");
			$this->db->exec("INSERT INTO `post` SET `ID` = 4, `Parent` = 'starcraft-2/old-post', `Path` = 'starcraft-2/old-post/nested-post', `Timestamp` = ".(time() - 8).", `Priority` = 255, `Author` = 'admin', `IP` = '".$_SERVER['REMOTE_ADDR']."', `Title` = 'Nested Post (Reply)', `Content` = '<p>I\'m nested in post #1!</p>".$lorem_ipsum."';");
		}
	}