<?php 
	class Chat extends Controller{
		public function __construct(){
		}
		
		public function view($file = NULL){
			if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
				$output = shell_exec("wmic process get description,executablepath");
				echo $output;
				if(!strpos($output, "chat.exe")){
					ob_end_clean();
					header("Connection: close");
					ignore_user_abort(); // optional
					ob_start();
					echo ('Text the user will see');
					$size = ob_get_length();
					header("Content-Length: $size");
					ob_end_flush();     // Will not work
					flush();            // Unless both are called !

					// At this point, the browser has closed connection to the web server

					// Do processing here
					include('/includes/chat.php');

					echo('Text user will never see');
				}
			}else{
				$output = shell_exec("ps -U administrator -u administrator u", $output, $result);
				foreach($output as $line) if(strpos($line, "chat.php")) echo "found";
			}
			
		}
	}
	