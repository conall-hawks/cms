<?php defined('BASEPATH') or die('Direct script access denied.'); ?>

<div class="content-box editor imageboard">
	<h2>Create <noscript>New Thread<style>.editor h2 select { display: none; }</style></noscript>
		<select required>
			<option selected></option>
			<option value="post">New Thread</option>
		</select>:
	</h2>
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
			margin-top: 55px;
			width: 30%;
		}
	</style>
	<form method="post" enctype="multipart/form-data">
		<table>
			<tr>
				<td rowspan="4">
					<fieldset class="header-field">
						<legend>Header:</legend>
						<input type="text" name="author" accesskey="n" placeholder="Name" maxlength="32" pattern="^[a-zA-Z][a-zA-Z0-9.\-_]{3,31}$" title="A username to be associated with the post." />
						<input type="email" name="email" accesskey="e" placeholder="E-mail"  maxlength="96" pattern="[a-zA-Z0-9]+(?:(\.|_)[A-Za-z0-9!#$%&'*+/=?^`{|}~-]+)*@(?!([a-zA-Z0-9]*\.[a-zA-Z0-9]*\.[a-zA-Z0-9]*\.))(?:[A-Za-z0-9](?:[a-zA-Z0-9-]*[A-Za-z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?" title="An e-mail to be associated with the post." />
						<input type="text" name="subject" accesskey="s" placeholder="Subject" maxlength="32" pattern="^[a-zA-Z0-9.\-_]+$" title="The topic of the post." />
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
							<li>GIF, JPG, PNG, SVG, WEBM allowed.</li>
							<li>Maximum file size allowed is 2 MB.</li>
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
</div>

<?php 
	if(!function_exists('build_imageboard_post')){
		function build_imageboard_post($posts){
			$output = NULL;
			global $uri;
			foreach($posts as $post){
				$output .= '<article class="content-box imageboard-post '.($post['Parent'] == 0 ? 'thread' : 'reply').'">';
				$output .= $post['Parent'] == 0 ? '<h2>' : '<h3>';
				$output .=  '<a class="ajax-link" href="/imageboard/'.$post['Board'].'/'.($post['Parent'] ? $post['Parent'] : $post['ID']).'#'.$post['ID'].'" title="Link to this post.">No</a>. ';
				$output .=  '<a class="ajax-link quotelink" href="/imageboard/'.$post['Board'].'/'.$post['ID'].'?quote='.$post['ID'].'" title="Reply to thread and quote this post.">'.$post['ID'].'</a> ';
				$output .=  'By: <span class="name" title="Author of this post.">'.$post['AuthorBlock'].'</span> ';
				$output .=  '<span class="timestamp" title="Date & time of post.">'.date('n/j/y', $post['Timestamp']).' '.date('G:i:s', $post['Timestamp']).'</span>';
				$output .=  $post['Parent'] == 0 && isset($uri->path[1]) ? ' [<a class="ajax-link" href="/imageboard/'.$post['Board'].'/'.$post['ID'].'" title="Open thread.">Reply</a>]' : NULL;
				$output .=  '<form class="delete" method="post">';
				$output .=  '<input type="checkbox" name="delete" value="'.$post['ID'].'" title="Flag for deletion." />';
				$output .=  '</form>';
				$output .= $post['Parent'] == 0 ? '</h2>' : '</h3>';
				$output .= $post['File'] ? '<a class="thumb" href="/images/imageboard/'.$post['File'].'" style="background-image: url(\'/images/imageboard/thumb/'.$post['File'].'\')" target="_blank" title="'.$post['FileName'].'"></a>' : NULL;
				if($post['File'] || $post['Subject']){
					$output .=  '<span class="post-info">';
					if($post['Subject']) $output .= '<span class="post-title red">'.$post['Subject'].'</span> ';
					if($post['File']) $output .= 'File: <a href="/images/imageboard/'.$post['File'].'" target="_blank" title="Open file in new window.">'.$post['File'].'</a> &ndash; ('.number_format($post['FileSize']/1048576, 2).'&nbsp;MB, '.$post['ImageWidth'].'&times;'.$post['ImageHeight'].', '.$post['FileName'].')';
					$output .= '</span>';
				}
				$output .= '<span class="message">';
				$output .= $post['Message'] ? nl2br(linkify($post['Message'])) : '<span class="grey">No message.</span>';/*todo: make "quotify for >># quoting"*/
				$output .= '</span>';
				if($post['Parent'] == 0) {
					global $imageboard;
					$replies = $imageboard->db->get_replies($post['ID']);
					$output .= build_imageboard_post($replies);
				}
				$output .= '</article>';
			}
			return $output;
		}
	}
	$path = explode('/', $uri->path);
	$board = isset($path[1]) ? strtolower($path[1]) : NULL;
	$posts = $imageboard->db->get_threads($board);
	echo build_imageboard_post($posts);
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
		text-decoration: none;
	}
	
	.imageboard-post a:focus, .imageboard-post a:hover {
		text-decoration: underline;
	}
	
	.imageboard-post .delete {
		display: inline-block;
		position: absolute;
		right: 1%;
	}
	
	.imageboard-post .thumb {
		background: rgba(0, 0, 0, .125) url("/images/loading.gif") center / contain no-repeat;
		border: 1px solid rgba(255, 255, 255, .0625);
		border-radius: 2px;
		display: block;
		float: left;
		padding-bottom: 15.5%;
		margin: .5%;
		width: 32%;
	}

	.imageboard-post .thumb-big {
		padding-bottom: 50%;
		width: 98.5%;
	}
	
	.imageboard-post .post-info {
		border-bottom: 1px solid rgba(255, 255, 255, .0625);
		font-family: 'Montserrat';
		font-size: 100%;
		color: lime;
		float: left;
		margin-right: .5%;
		width: 65.9%;
	}

	.imageboard-post .post-info-big {
		margin: 0 .5%;
		width: 98.5%;
	}
	
	.imageboard-post .post-title {
		font-size: 125%;
	}
	
	.imageboard-post .imageboard-post {
		background: rgba(255, 255, 255, .0078125);
		clear: right;
		display: table;
		margin: .5%;
		min-height: 50px;
		max-width: 95%;
	}
</style>