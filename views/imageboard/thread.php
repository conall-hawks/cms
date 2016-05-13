<?php defined('BASEPATH') or die('Direct script access denied.'); ?>
<?php $path = explode('/', $uri->path); $parent = isset($path[2]) ? $path[2] : 0; ?>

<article class="content-box editor imageboard">
	<h2>Create <noscript><?php echo $parent ? 'Reply' : 'New Thread'; ?><style>.editor h2 select { display: none; }</style></noscript>
		<select required>
			<option selected></option>
			<option value="post"><?php echo $parent ? 'Reply' : 'New Thread'; ?></option>
		</select>:
	</h2>
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="csrf_token" value="" />
		<input type="hidden" name="parent" value="<?php echo $parent; ?>" />
		<table>
			<tr>
				<td rowspan="4">
					<fieldset class="header-field">
						<legend>Header:</legend>
						<input type="text" name="author" accesskey="n" placeholder="Name" maxlength="32" pattern="^[a-zA-Z][a-zA-Z0-9.\-_ ]{3,31}$" title="A username to be associated with the post." />
						<input type="email" name="email" accesskey="e" placeholder="E-mail"  maxlength="96" pattern="[a-zA-Z0-9]+(?:(\.|_)[A-Za-z0-9!#$%&'*+/=?^`{|}~-]+)*@(?!([a-zA-Z0-9]*\.[a-zA-Z0-9]*\.[a-zA-Z0-9]*\.))(?:[A-Za-z0-9](?:[a-zA-Z0-9-]*[A-Za-z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?" title="An e-mail to be associated with the post." />
						<input type="text" name="subject" accesskey="s" placeholder="Subject" maxlength="32" pattern="^[a-zA-Z0-9.\-_ ]+$" title="The topic of the post." />
						<input type="password" name="password" accesskey="p" placeholder="Password" maxlength="256" title="A password for post management & deletion." />
					</fieldset>
				</td>
				<td colspan="2" rowspan="5">
					<fieldset class="content-field">
						<legend>Message:</legend>
						<textarea name="message" placeholder="Message" title="A message to be sent along with the post."></textarea>
					</fieldset>
				</td>
			</tr>
			<tr></tr>
			<tr></tr>
			<tr></tr>
			<tr>
				<td rowspan="4">
					<fieldset class="file-field">
						<legend>File:</legend>
						<ul>
							<li>
								<?php 
									$size = count($imageboard->file_types);
									foreach($imageboard->file_types as $index => $type) echo $index !== $size - 1 ? $type.', ' : $type.' '; ?>allowed.</li>
							<li>Maximum file size allowed is <?php echo $imageboard->max_size / 1024; ?> MB.</li>
							<li>Images greater than 250x250 will be thumbnailed.</li>
						</ul>
						<div><input name="file" type="file" accesskey="f" /></div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td class="captcha-field" rowspan="3">
					<fieldset class="captcha-field">
						<legend>Captcha:</legend>
						<img class="captcha" src="/includes/captcha/captcha.php" alt="Enter this text into the box below." />
						<input type="text" name="captcha" size="6" autocomplete="off" placeholder="Enter the text above." title="I know it's a pain in the ass, but it prevents spam." accesskey="c" />
					</fieldset>
				</td>
				<td rowspan="3">
					<fieldset class="submit-field">
						<div><input name="create" type="submit" value="Create" /></div>
					</fieldset>
				</td>
			</tr>
			<tr>
			</tr>
			<tr></tr>
		</table>
	</form>
</article>

<?php 
	if($parent){
		$posts = $imageboard->db->get_thread($parent);
	}else{
		$board = isset($path[1]) ? strtolower($path[1]) : NULL;
		$posts = $imageboard->db->get_threads($board);
	}
	if(!function_exists('build_imageboard_post')){
		function build_imageboard_post($posts, $reply_limit = NULL){
			$output = NULL;
			global $uri;
			foreach($posts as $post){
				$output .= '<article id="'.$post['ID'].'" class="content-box imageboard-post '.($post['Parent'] == 0 ? 'thread' : 'reply').'">';
				$output .= $post['Parent'] == 0 ? '<h2>' : '<h3>';
				$output .=  '<a class="ajax-link" href="/imageboard/'.$post['Board'].'/'.($post['Parent'] ? $post['Parent'] : $post['ID']).'#'.$post['ID'].'" title="Link to this post.">&#8470;</a> ';
				$output .=  '<a class="ajax-link quote-link" href="/imageboard/'.$post['Board'].'/'.($post['Parent'] ? $post['Parent'] : $post['ID']).'?quote='.$post['ID'].'" title="Reply to and quote this post.">'.$post['ID'].'</a> ';
				$output .=  'By: <span class="name" title="Author of this post.">'.$post['AuthorBlock'].'</span> ';
				$output .=  '<span class="timestamp" title="Date & time of post.">'.date('n/j/y', $post['Timestamp']).' '.date('G:i:s', $post['Timestamp']).'</span>';
				$output .=  $post['Parent'] == 0 && !isset(explode('/', $uri->path)[2]) ? ' [<a class="ajax-link" href="/imageboard/'.$post['Board'].'/'.$post['ID'].'" title="Open thread.">Reply</a>]' : NULL;
				$output .=  '<form class="delete" method="post">';
				$output .=  '<input type="hidden" value="'.$post['ID'].'" />';
				$output .=  '<input type="submit" value="x" title="Flag for deletion." />';
				$output .=  '</form>';
				$output .= $post['Parent'] == 0 ? '</h2>' : '</h3>';
				$output .= $post['File'] ? '<a class="thumb" href="/images/imageboard/'.$post['File'].'" style="background-image: url(\'/images/imageboard/thumb/'.$post['File'].'\')" target="_blank" title="'.$post['FileName'].'"></a>' : NULL;
				if($post['File'] || $post['Subject']){
					$output .=  '<span class="post-info">';
					if($post['Subject']) $output .= '<span class="post-title red">'.$post['Subject'].'</span> ';
					if($post['File']) $output .= 'File: <a href="/images/imageboard/'.$post['File'].'" target="_blank" title="Open file in new window.">'.$post['File'].'</a> &ndash; ('.$post['FileName'].', '.$post['ImageWidth'].'&times;'.$post['ImageHeight'].', '.number_format($post['FileSize']/1048576, 2).'&nbsp;MB)';
					$output .= '</span>';
				}
				$output .= '<span class="message">';
				$output .= $post['Message'] ? nl2br(linkify($post['Message'])) : '<span class="grey">No message.</span>';/*todo: make "quotify for >># quoting"*/
				$output .= '</span>';
				if($post['Parent'] == 0) {
					global $imageboard;
					$replies = $imageboard->db->get_replies($post['ID'], $reply_limit);
					$output .= build_imageboard_post($replies);
				}
				$output .= '</article>';
			}
			return $output;
		}
	}
	
	if($parent){
		echo build_imageboard_post($posts);
	}else{
		echo build_imageboard_post($posts, 3);
	}
?>
<style>
	.imageboard-post {
		overflow: hidden;
		user-select: text;
	}
	
	.imageboard-post > h2, .imageboard-post h3 {
		font-family: consolas;
	}
	
	.imageboard-post a {
		font-family: 'Montserrat', 'Georgia', consolas, serif;
		font-size: 90%;
		text-decoration: none;
	}
	
	.imageboard-post a:focus, .imageboard-post a:hover {
		text-decoration: underline;
	}
	
	.imageboard-post .delete {
		display: inline-block;
		height: 12px;
		position: absolute;
		right: 6px;
		top: 1px;
		width: 12px;
	}
	
	.imageboard-post .delete input {
		height: 14px;
		padding: 0 0 3px;
		position: absolute;
		width: 14px;
	}
	
	.imageboard-post .thumb {
		background: rgba(0, 0, 0, .125) url("/images/loading.gif") center / contain no-repeat;
		border: 1px solid rgba(255, 255, 255, .0625);
		border-radius: 2px;
		display: block;
		float: left;
		height: 11.25vw;
		margin: 4px;
		min-width: 120px;
		min-height: 67px;
		width: 20vw;
	}
	
	.imageboard-post .reply .thumb {
		height: 5.625vw;
		width: 10vw;
	}

	.imageboard-post .thumb-big {
		height: 36.365625vw;
		width: 64.925vw;
	}
	
	.imageboard-post .reply .thumb-big {
		height: 35.915625vw;
		width: 63.85vw;
	}
	
	.imageboard-post .post-info {
		border-bottom: 1px solid rgba(255, 255, 255, .0625);
		display: inline-block;
		font-size: 85%;
		margin: 0 4px 0 0;
		width: 34%;
		white-space: nowrap;
	}

	.imageboard-post .post-info-big {
		margin: 0 4px;
		width: calc(100% - 8px);
		max-width: none;
	}
	
	.imageboard-post .post-title {
		font-size: 125%;
	}
	
	.imageboard-post .message {
		display: block;
		margin: 4px 0 0 4px;
	}
	
	.imageboard-post .reply {
		box-shadow: none;
		display: table;
		margin: 4px;
		min-height: 50px;
	}
	
	.imageboard-post .reply:first-of-type {
		margin-top: 2%;
	}
</style>
	<style>
		.captcha {
			background: white;
			border: 1px solid #BBB;
			border-radius: 2px;
			box-shadow: 0 0 2px #BBB;
			height: 85px;
			width: 94%;
		}
		
		.imageboard tr {
			height: 50px;
		}
		
		.imageboard fieldset {
			margin: 0;
			padding: 0;
			position: relative;
			text-align: center;
			bottom: 0;
		}
		
		.imageboard legend {
			margin-left: .5em;
		}
		
		.imageboard td:not(:last-of-type) fieldset, .imageboard .file-field {
			margin-right: 8px;
		}
		
		.imageboard .header-field, .imageboard .file-field {
			height: 196px;
		}
		
		.imageboard .header-field input {
			margin-bottom: 8px;
			padding: 0 2%;
			width: 91%;
		}
		
		.imageboard .content-field {
			height: 247px;
		}
		
		.imageboard .content-field textarea {
			height: 223px;
			margin-bottom: 8px;
			padding: 0 1%;
			width: 95%;
		}
		
		.imageboard .file-field {
			margin-bottom: 7px;
		}
		.imageboard .file-field input {
			bottom: 10px;
			left: 3%;
			position: absolute;
			width: 94%;
		}
		
		.imageboard .file-field li {
			font-size: 80%;
			list-style: disc inside;
			white-space: normal;
			width: 98%;
			overflow: hidden;
		}
		
		.imageboard .captcha-field {
			height: 145px;
			margin-bottom: 7px;
		}
		
		.imageboard .captcha-field input {
			bottom: 10px;
			left: 3%;
			position: absolute;
			text-align: center;
			width: 94%;
		}
		
		.imageboard .submit-field {
			height: 137px;
			vertical-align: middle;
		}
		
		.imageboard .submit-field div {
			margin-top: 58px;
			width: 30%;
		}
	</style>
	
	
	<script>
		$(".editor form").css("display", "none");
		$(".editor h2 select").change(function(){
			element = $(".editor form");
			switch(this.value){
				case "post":
					element.css("display", "block");
					setTimeout(function(){element.css("opacity", 1)}, 0);
					break;
				default:
					setTimeout(function(){
						element.css("display", "none");
					}, 10);
			}
		});
	</script>