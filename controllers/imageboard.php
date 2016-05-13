<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	class Imageboard extends Controller {
		private $image_path = 'images/imageboard';
		private $thumb_path = 'images/imageboard/thumb';
		
		public $file_types = array('gif', 'jpg', 'png', 'svg', 'webm');
		public $max_size = 2560;
		private $file_required = false;
		private $mod_approval = false;
		private $thumb_width = 320;
		private $thumb_height = 200;
		
		private $trip_seed = 'd8UqfH8NwjD8YXny';
		
		public function __construct(){
			$dirs = array($this->image_path, $this->thumb_path);
			foreach($dirs as $dir) if(!is_writable($dir)) $this->feedback = 'Directory "'.$dir.'" needs write permission.';
			$this->model();
			$this->run();
		}	
		
		private function run(){
			if(isset($_POST['create'])){
				$this->post();
			}elseif(isset($_POST['delete'])){
				$this->delete();
			}elseif(isset($_POST['manage'])){
				$this->manage();
			}
		}
		
		public function view_board($file = NULL){
			global $uri;
			$path = explode('/',$uri->path);
			$this->title = isset($path[1]) ? $path[1] : NULL;
			parent::view('/imageboard/board.php');
		}
		
		public function view_thread($file = NULL){
			parent::view('imageboard/thread.php');
		}
		
		private function post(){
			/* If the user is not logged in, do some extra security. */
			global $login;
			if(!$login->get_status()){
				/* Check the CAPTCHA. */
				if(!isset($_SESSION['captcha'])){
					$this->feedback = 'Please re-submit the form.';
					//return false;
				}elseif(strtolower($_POST['captcha']) != strtolower($_SESSION['captcha'])){
					$this->feedback = 'Wrong CAPTCHA. Please try again.';
					//return false;
				}
				// Extra checks.
				#$this->check_banned();
				#$this->check_message_size();
				#$this->check_flood();
			}
			
			// Is this a reply or a new thread?
			if(isset($_POST['parent']) && $_POST['parent'] != 0 && $this->db->thread_exists($_POST['parent'])){
				$post['parent'] = $_POST['parent'];
			}else{
				$post['parent'] = 0;
			}
			
			// Gather some information.
			global $uri;
			global $security;
			$post['author'] = $security->cleanse(substr($this->tripcode($_POST['author'])[0], 0, 36));
			$post['tripcode'] = $security->cleanse($this->tripcode($_POST['author'])[1]);
			$post['email'] = $security->cleanse(substr($_POST['email'], 0, 72));
			$post['subject'] = $security->cleanse(substr($_POST['subject'], 0, 36));
			$post['message'] = $security->cleanse(substr($_POST['message'], 0, 2048));
			$post['password'] = ($_POST['password'] != '') ? md5(md5($_POST['password'])) : NULL;
			if($login->is_admin() && ($post['author'] == 'Admin' || $post['author'] == 'Administrator')){
				$post['author_block'] = '<a class="ajax-link admin" href="/profile/admin">'.$post['author'].'</a>';
			}elseif($login->is_user() && $login->get_username() == $post['author']){
				$post['author_block'] = '<a class="ajax-link user" href="/profile/'.$post['author'].'">'.$post['author'].'</a>';
			}elseif($post['author']){
				$post['author_block'] = '<a class="ajax-link anon" href="/profile/anonymous">'.$post['author'].'</a>';
			}else{
				$post['author_block'] = '<a class="ajax-link anon" href="/profile/anonymous">Anonymous</a>';
			}
			
			// Process file upload.
			$file = $_FILES['file'];
			if($file['name']){
				if(!$this->file_verify($file)) return false;
				
				// File information.
				$post['file_name'] = trim(htmlentities(substr($file['name'], 0, 50), ENT_QUOTES));
				$post['file_hex'] = md5_file($file['tmp_name']);
				$post['file_size'] = $file['size'];
				
				// File type.
				$file_type = pathinfo($file['name'])['extension'];
				if($file_type == 'jpeg') $file_type = 'jpg';
				if($file_type == 'weba') $file_type = 'webm';
				
				// Thumbnail type.
				if($file_type == 'webm'){
					$thumb_type = 'jpg';
				}elseif($file_type == 'swf'){
					$thumb_type = 'png';
				}else{
					$thumb_type = $file_type;
				}
				
				// Name and pathing information.
				$file_name = time().substr(microtime(), 2, 3);
				$post['file'] = $file_name.'.'.$file_type;
				$post['thumb'] = $file_name.'.'.$thumb_type;
				$file_location = $this->image_path.'/'.$post['file'];
				$thumb_location = $this->thumb_path.'/'.$post['thumb'];
				
				// Check for duplicate.
				if($this->db->check_duplicate($post['file_hex'], 'hex')){
					$this->feedback = 'Duplicate file detected.';
					return false;
				}
				
				// Authorization.
				if(!in_array($file_type, $this->file_types)){
					$this->feedback = 'Disallowed file type.';
					return false;
				}
				
				if($file_type == 'webm'){
					$file_mime = shell_exec('file --mime-type ' . $file['tmp_name']);
					$file_mime = explode(' ', $file_mime);
					$file_mime = strtolower(trim(array_pop($file_mime)));
				}else{
					if(!@getimagesize($file['tmp_name'])){
						$this->feedback = 'Failed to read image size. Please try again.';
						return false;
					}
					$file_info = getimagesize($file['tmp_name']);
					$file_mime = $file_info['mime'];
				}
				
				// MIME type whitelist.
				if(!($file_mime == 'image/jpeg' || $file_mime == 'image/gif' || $file_mime == 'image/png' || (in_array('webm', $this->file_types) && ($file_mime == 'video/webm' || $file_mime == 'audio/webm')) || (in_array('swf', $this->file_types) && ($file_mime == 'application/x-shockwave-flash')))){
					$this->feedback = 'Disallowed file type.';
					return false;
				}
				
				// Move the file.
				if(!move_uploaded_file($file['tmp_name'], $file_location)){
					@unlink($file_location);
					$this->feedback = 'Could not copy uploaded file.';
					return false;
				}
				
				// Ensure file was fully transferred.
				if($file['size'] != filesize($file_location)){
					@unlink($file_location);
					$this->feedback = 'Failed to transfer file. Please try again.';
					return false;
				}
				
				// Additional file information and thumbnail creation.
				if($file_mime == 'audio/webm' || $file_mime == 'video/webm'){
					$post['image_width'] = intval(shell_exec('mediainfo --Inform="Video;%Width%" ' . $file_location));
					$post['image_height'] = intval(shell_exec('mediainfo --Inform="Video;%Height%" ' . $file_location));
					
					if($post['image_width'] <= 0 || $post['image_height'] <= 0){
						$post['image_width'] = 0;
						$post['image_height'] = 0;
						$file_location_old = $file_location;
						$file_location = substr($file_location, 0, -1) . 'a';
						rename($file_location_old, $file_location);
						$post['file'] = substr($post['file'], 0, -1) . 'a';
					}
					
					if($file_mime == 'video/webm'){
						list($thumb_maxwidth, $thumb_maxheight) = thumbnailDimensions($post);
						shell_exec('ffmpegthumbnailer -s ' . max($thumb_maxwidth, $thumb_maxheight) . ' -i $file_location -o $thumb_location');
					
						$thumb_info = getimagesize($thumb_location);
						$post['thumb_width'] = $thumb_info[0];
						$post['thumb_height'] = $thumb_info[1];
						if($post['thumb_width'] <= 0 || $post['thumb_height'] <= 0){
							@unlink($file_location);
							@unlink($thumb_location);
							$this->feedback = 'Sorry, your video appears to be corrupt.';
							return false;
						}
						addVideoOverlay($thumb_location);
					}
					
					$duration = intval(shell_exec('mediainfo --Inform="' . ($file_mime == 'video/webm' ? 'Video' : 'Audio') . ';%Duration%" ' . $file_location));
					$mins = floor(round($duration / 1000) / 60);
					$secs = str_pad(floor(round($duration / 1000) % 60), 2, '0', STR_PAD_LEFT);
					$post['file_original'] = $mins.':'.$secs.($post['file_original'] != '' ? (', ' . $post['file_original']) : '');
				}else{
					$file_info = getimagesize($file_location);
					$post['image_width'] = $file_info[0];
					$post['image_height'] = $file_info[1];
					if($file_mime == 'application/x-shockwave-flash'){
						if(!copy('swf_thumbnail.png', $thumb_location)){
							@unlink($file_location);
							$this->feedback = 'Could not create thumbnail.';
							return false;
						}
						addVideoOverlay($thumb_location);
					}else{
						if(!$this->create_thumbnail($file_location, $thumb_location, $this->thumb_width, $this->thumb_height)){
							@unlink($file_location);
							$this->feedback = 'Could not create thumbnail.';
							return false;
						}
					}
				}
				$thumb_info = getimagesize($thumb_location);
				$post['thumb_width'] = $thumb_info[0];
				$post['thumb_height'] = $thumb_info[1];
				
				// Successful file upload!
				$this->feedback = $post['file_name'].' has been uploaded!';
			}else{
				if($post['parent'] == 0 && $this->file_required){
					$this->feedback = 'A file is required to start a thread.';
					return false;
				}
				if($post['message'] == ''){
					$this->feedback = 'Please post a message and/or upload a file.';
					return false;
				}
			}
			
			// Check if the post needs to be approved by the moderator.
			global $login;
			if($this->mod_approval){
				$post['moderated'] = 0;
				$this->feedback = 'Your post will be displayed once it has been approved by a moderator.';
			}else{
				$post['moderated'] = 1;
			}
			
			// Add the post into the database.
			$post['id'] = $this->db->add_post($post);
		}
		
		private function tripcode($name){
			/* If the name is a tripcode */
			if(preg_match("/(#|!)(.*)/", $name, $regs)){
				$cap = $regs[2];
				$cap_full = '#' . $regs[2];
				
				if(function_exists('mb_convert_encoding')){
					$recoded_cap = mb_convert_encoding($cap, 'SJIS', 'UTF-8');
					if($recoded_cap != ''){
						$cap = $recoded_cap;
					}
				}
				
				if(strpos($name, '#') === false){
					$cap_delimiter = '!';
				}elseif(strpos($name, '!') === false){
					$cap_delimiter = '#';
				}else{
					$cap_delimiter = (strpos($name, '#') < strpos($name, '!')) ? '#' : '!';
				}
				
				if(preg_match("/(.*)(" . $cap_delimiter . ")(.*)/", $cap, $regs_secure)){
					$cap = $regs_secure[1];
					$cap_secure = $regs_secure[3];
					$is_secure_trip = true;
				}else{
					$is_secure_trip = false;
				}
				
				$tripcode = "";
				if($cap != ""){
					$cap = strtr($cap, "&amp;", "&");
					$cap = strtr($cap, "&#44;", ", ");
					$salt = substr($cap . "H.", 1, 2);
					$salt = preg_replace("/[^\.-z]/", ".", $salt);
					$salt = strtr($salt, ":;<=>?@[\\]^_`", "ABCDEFGabcdef");
					$tripcode = substr(crypt($cap, $salt), -10);
				}
				
				if($is_secure_trip){
					if($cap != ""){
						$tripcode .= "!";
					}
					
					$tripcode .= "!" . substr(md5($cap_secure . $this->trip_seed), 2, 10);
				}
				
				return array(preg_replace("/(" . $cap_delimiter . ")(.*)/", "", $name), $tripcode);
			}
			return array($name, "");
		}
		
		public function file_verify($file){
			/* Upload error checking */
			switch($file['error']){
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->feedback = 'That file is larger than ' . $this->max_size . ' kb.';
					return false;
				case UPLOAD_ERR_INI_SIZE:
					$this->feedback = 'The uploaded file exceeds the upload_max_filesize directive (' . ini_get('upload_max_filesize') . ') in php.ini.';
					return false;
				case UPLOAD_ERR_PARTIAL:
					$this->feedback = 'The uploaded file was only partially uploaded.';
					return false;
				case UPLOAD_ERR_NO_FILE:
					$this->feedback = 'No file was uploaded.';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->feedback = 'Missing a temporary folder.';
					return false;
				case UPLOAD_ERR_CANT_WRITE:
					$this->feedback = 'Failed to write file to disk.';
					return false;
				default:
					$this->feedback = 'Unable to save the uploaded file.';
					return false;
			}
			if(!is_file($file['tmp_name']) || !is_readable($file['tmp_name'])){
				$this->feedback = 'File transfer failure. Please try again.';
				return false;
			}
			if(($this->max_size > 0) && (filesize($file['tmp_name']) > ($this->max_size * 1024))){
				$this->feedback = 'File is larger than ' . $this->max_size . ' kb.';
				return false;
			}
			return true;
		}
		
		private function create_thumbnail($file_location, $thumb_location, $new_w, $new_h){
			$system = explode('.', $thumb_location);
			$system = array_reverse($system);
			if(preg_match('/jpg|jpeg/', $system[0])){
				$src_img = imagecreatefromjpeg($file_location);
			}elseif(preg_match('/png/', $system[0])){
				$src_img = imagecreatefrompng($file_location);
			}elseif(preg_match('/gif/', $system[0])){
				$src_img = imagecreatefromgif($file_location);
			}else{
				return false;
			}
			if(!$src_img) $this->feedback = 'Unable to read uploaded file during thumbnailing. A common cause for this is an incorrect extension when the file is actually of a different type.';
			
			$old_x = imageSX($src_img);
			$old_y = imageSY($src_img);
			$percent = ($old_x > $old_y) ? ($new_w / $old_x) : ($new_h / $old_y);
			$thumb_w = round($old_x * $percent);
			$thumb_h = round($old_y * $percent);
			$dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
			if(preg_match('/png/', $system[0]) && imagepng($src_img, $thumb_location)){
				imagealphablending($dst_img, false);
				imagesavealpha($dst_img, true);
				$color = imagecolorallocatealpha($dst_img, 0, 0, 0, 0);
				imagefilledrectangle($dst_img, 0, 0, $thumb_w, $thumb_h, $color);
				imagecolortransparent($dst_img, $color);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
			}else{
				$this->fastimagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
			}
				if(preg_match('/png/', $system[0])){
				if(!imagepng($dst_img, $thumb_location)){
					return false;
				}
			}else if(preg_match('/jpg|jpeg/', $system[0])){
				if(!imagejpeg($dst_img, $thumb_location, 70)){
					return false;
				}
			}else if(preg_match('/gif/', $system[0])){
				if(!imagegif($dst_img, $thumb_location)){
					return false;
				}
			}
			imagedestroy($dst_img);
			imagedestroy($src_img);
			return true;
		}

		// Author: Tim Eckel - Date: 12/17/04 - Project: FreeRingers.net - Freely distributable.
		private function fastimagecopyresampled(&$dst_image, &$src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3){
			if(empty($src_image) || empty($dst_image)){
				return false;
			}
			
			if($quality <= 1){
				$temp = imagecreatetruecolor($dst_w + 1, $dst_h + 1);

				imagecopyresized($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
				imagecopyresized($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
				imagedestroy($temp);
			}elseif($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)){
				$tmp_w = $dst_w * $quality;
				$tmp_h = $dst_h * $quality;
				$temp = imagecreatetruecolor($tmp_w + 1, $tmp_h + 1);

				imagecopyresized($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
				imagecopyresampled($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
				imagedestroy($temp);
			}else{
				imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
			}
			return true;
		}
	}