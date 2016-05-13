<?php 
	session_start();
	$text = $_POST['text'];
	
	// Clear the log if it's bigger than 2kb
	if(filesize('log.html') > 2048) file_put_contents('log.html', '');
	
	// Write the text to the log
	$log = fopen('log.html', 'a');
	fwrite($log, '<div class="chat-message-line">[<span class="red">'.date('H:i:s').'</span>] <b class="lime">'.$_SESSION['user_name'].'</b>: '.stripslashes(htmlspecialchars($text)).'<br /></div>'."\n");
	fclose($log);