<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Cms extends Controller{
		
		/* Start the session and begin. */
		public function __construct(){
			$this->model();
			// DANGER! For temporary use only: use to reset the database.
			#$this->db->rebuild();
			#$this->db->reset();
			
			// Administrators
			global $login;
			if($login->is_admin()){
				if(isset($_POST['create_post']) && isset($_POST['post_path']) && isset($_POST['post_title']) && isset($_POST['post_content'])){
					// Create a new post.
					$this->create_post($_POST['post_path'], $_POST['post_title'], $_POST['post_content']);
				}elseif(isset($_POST['delete_post']) && isset($_POST['post_id'])){
					// Delete an existing post.
					$this->delete_post($_POST['post_id']);
				}elseif(isset($_POST['edit_post']) && isset($_POST['post_id'])){
					// Modify an existing post.
					$this->edit_post($_POST['post_id']);
				}
			}else{
				$this->feedback = 'You are not an administrator';
			}
		}
		
		// Create a new post.
		private function create_post($path, $title, $content, $priority = 255){
			return $this->db->insert_post($path, $title, $content, $priority);
		}
		
		private function edit_post($id, $content = NULL){
			if($content == NULL){
				$this->db->get_post($id);
			}

			return $this->db->edit_post($id);
		}
		
		// Delete a post.
		private function delete_post($id){
			return $this->db->delete_post($id);
		}
		
		// Get all the posts of a url.
		public function get_post(){
			return $this->db->get_post();
		}
		
		// Get all the children posts.
		public function get_children(){
			return $this->db->get_children();
		}
		
		public function build_page(){
			$post = $this->get_post();
			$editor = $this->editor();
			$children = '';//$this->get_children();
			$output = '';
			// The post on this page.
			if($post){
				$output .= '<article class="content-box">';
				$output .= '<h2 class="content-h2">'.$post['title'].'<span class="postStamp"><span class="red">Posted on: </span>'.date('F j', $post['timestamp']).'<sup>'.date('S', $post['timestamp']).'</sup>'.date(', Y', $post['timestamp']). ' at '.date('h:i A', $post['timestamp']).'<span class="red"> By: </span><span class="lime">'.$post['author'].'</span></span></h2>';
				$output .= $post['content'];
				$output .= '</article>';
			}
			
			// An editor for adding posts.
			if(isset($editor)){
				$output .= $editor;
			}
			
			// The children posts.
			if($children){
				foreach($children as $child){
					$output .= '<article class="content-box">';
					$output .= '<h2 class="content-h2"><a class="ajax-link content-h2-link" href="/'.$child['path'].'">'.$child['title'].'</a><span class="postStamp"><span class="red">Posted on: </span>'.date('F j', $child['timestamp']).'<sup>'.date('S', $child['timestamp']).'</sup>'.date(', Y', $child['timestamp']). ' at '.date('h:i A', $child['timestamp']).'<span class="red"> By: </span><span class="lime">'.$child['author'].'</span></span></h2>';
					$output .= $child['content'];
					$output .= '</article>';
				}
			}
			return $output;
		}
		
		public function editor(){
			global $login;
			if(!$login->is_admin()) return false;
			global $uri;
			$output = '
				<div class="content-box editor">
					<h2>Create <noscript>Post<style>.editor h2 select { display: none; }</style></noscript>
						<select required>
							<option selected></option>
							<option value="post">Post</option>
						</select>:
					</h2>
					<form method="post">
						<table>
							<tr>
								<td>
									<fieldset class="title-field">
										<legend>Header:</legend>
										<select required>
												<!--<option disabled hidden value="none">None</option>-->
												<option value="h3">Small (h3)</option>
												<option value="h2" selected>Large (h2)</option>
										</select>
										<input name="post_title" type="text" required placeholder="title" />
									</fieldset>
								</td>
								<td>
									<fieldset class="tags-field">
										<legend>Tags:</legend>
										<input type="text" name="post_tags" placeholder="tag1, tag2, tag3, tag4, tag5" />
										<p>Tags are comma delimited. Maximum of 5.</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td>
									<fieldset class="path-field">
										<legend>URL:</legend>
										<p>'.$uri->path.'/<input name="post_path" type="text" required placeholder="new-post" /></p>
									</fieldset>
								</td>
								<td>
									<fieldset class="extra-field">
										<legend>Info:</legend>
										<p>This space is reserved for new features.</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<fieldset class="content-field">
										<legend>Content:</legend>
										<textarea class="cms-textarea" name="post_content" placeholder="Post Content. HTML code is allowed." title="Post Content. HTML code is allowed.">'.'</textarea>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td class="submit-field" colspan="2">
									<div><input name="create_post" type="submit" value="Create" /></div>
								</td>
							</tr>
						</table>
					</form>
					
					<script>
						if(typeof tinymce != "undefined") delete tinymce;
						if(typeof tinyMCE != "undefined") delete tinyMCE;
						$.getScript("https://cdn.tinymce.com/4/tinymce.min.js");
						setInterval(startTiny, 250);
						function startTiny(){
							if(typeof tinymce != "undefined"){
								tinymce.init({
									browser_spellcheck: true,
									height: 420,
									plugins: "advlist autolink code emoticons image insertdatetime link lists media paste preview save searchreplace table textcolor visualblocks wordcount",
									resize: false,
									selector: ".cms-textarea",
									skin_url: "/includes/tinymce-skin",
									toolbar1: "undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | outdent indent",
									toolbar2: "link image media file | table | bullist numlist | emoticons | preview",
								});
								clearInterval(startTiny);
							}
						}
						
						$(".editor form").css("display", "none");
						$(".editor h2 select").change(function(){
							element = $(".editor form");
							switch(this.value){
								case "post":
									element.css("display", "block");
									setTimeout(function(){element.css("opacity", 1)}, 0);
									break;
								default:
									element.css("display", "none");
							}
						});
						
						$(".editor input[name=post_title]").keyup(function(){
							$(".editor input[name=post_path]")[0].value = this.value.replace(/[^a-z0-9]/gi, "-");
						});
						
						setInterval(function(){
							$("#mceu_20 button").click(function(){
								setTimeout(function(){
									$(".mce-window-body").parent().parent()[0].attributes.style.value = "border-width: 1px; z-index: 65536; left: 25%; width: 648px; height: 588px;";
									if($(".mce-window-body").find("iframe")[0].src.search("<style>p{color:teal}</style>") == -1){
										$(".mce-window-body").find("iframe")[0].src += "<style>p{color:teal}</style>";
									}
								}, 500);
							});
						}, 5000);
					</script>
				</div>
			';
			return $output;
		}
	}