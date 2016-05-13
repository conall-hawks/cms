<table border="1" class="contentTable">
	<!--Begin macros navigation include-->
	<tr>
		<?php
			$filepath = str_replace('.php', '\\*.php', __FILE__);		//* in subdirectory of document's name
			$filepath = str_replace('\\', '/', $filepath);			//all backslashes into forward slashes
			$dirname = basename(str_replace('.php', '', __FILE__));		//grab the name of the directory
			$index = 0;
			foreach (glob($filepath) as $file) {
				$index++;
				$filename = basename($file);					//remove path info
				$filename = str_replace('.php', '', $filename);			//remove file extension
				$hashmark = preg_replace('/[^a-z0-9&]/i', '', $filename);	//remove specials except ampersands
				$hashmark = str_replace('&', '-', $hashmark);			//ampersands into dashes
				$hashmark = strtolower($hashmark);//all to lowercase
				echo '<td class="cell">';
				echo '	<a class="tableLink scrollLink" href="#' . $hashmark . '">' . $filename . '<br />';
				echo '	<img class="icon" src="/images/buttons/' . $dirname . '/' . $hashmark . '.png" /></a>';
				echo '</td>';
				if ($index == 8) {
					echo '</tr>';		//maximum number of columns reached
					echo '<tr>';		//start a new column
					$index = 0;		//reset the counter
				}
			}
		?>
	</tr>
	<!--End macro navigation include-->
</table>