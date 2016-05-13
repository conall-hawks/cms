<table class="header">
	<tr>
		<td rowspan="3"><a class="ajax-link header-logo" href="/"></a></td>
		<td><div><a class="ajax-link" href="/">Front Page</a></div></td>
		<td><div><a class="ajax-link" href="/programming">Programming</a></div></td>
		<td><div><a class="ajax-link" href="/security">Security</a></div></td>
		<td><div><a class="ajax-link" href="/starcraft-2">Starcraft 2</a></div></td>
		<td><div><a class="ajax-link" href="/world-of-warcraft">World of Warcraft</a></div></td>
	</tr>
	<tr>
		<td><div><a class="ajax-link" href="/forum">Forum</a></div></td>
		<td colspan="3" rowspan="2">
			<table class="info">
				<tr>
					<td class="clock" title="<?php echo 'Server time: '.date('g:i A T'); ?>"><?php echo date('F j').'<sup>'.date('S').'</sup>'.date(', Y'); ?></td>
					<td colspan="3" rowspan="3" title=""><h1>Cenari.us</h1></td>
					<td>User: <span id="username" class="<?php echo $login->get_status() ? 'lime' : 'red'; ?>"><?php echo $login->get_username(); ?></span></td>
				</tr>
				<tr>
					<td><a class="blue ajax-link" href="/news">News</a></td>
					<td><?php echo $login->get_status() ? 'Messages: <span class="red">None</span>' : '<a class="blue ajax-link" href="/register">Register</a>';?></td>
				</tr>
				<tr>
					<td><a class="blue ajax-link" href="/bookmarks">Bookmarks</a></td>
					<td title="Encrypted connection status.">HTTPS: <span class="<?php echo isset($_SERVER['HTTPS']) ? 'lime">Yes' : 'red">No'; ?></span></td>
				</tr>
				<tr>
					<td>Forum: <span class="red">Offline</span></td>
					<td colspan="3" rowspan="2" title="Sexual innuendo? I can keep it up all night."><h2 id="header-h2"><?php echo generate_pathing_links(); ?></h2></td>
					<td>
						Chat: 
						<?php 
							if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
								if(strpos(shell_exec('wmic process get description,executablepath'), 'php.exe') === false){
									echo '<span class="red">Offline</span>';
								}else{
									echo '<a class="lime chat-link">Online</a>';
								}
							}else{
								if(strpos(shell_exec('ps -A'), 'php') === false){
									echo '<span class="red">Offline</span>';
									#shell_exec('php /var/www/html/includes/chat.php  > /dev/null 2>&1 &');
								}else{
									echo '<a class="lime chat-link">Online</a>';
								}
							}
						?>
					</td>
				</tr>
				<tr>
					<td>Imageboard: <span class="lime">Online</span></td>
					<td class="katamari" title="Katamari!"></td>
				</tr>
			</table>
		</td>
		<td><div><a class="ajax-link" href="/miscellaneous">Miscellaneous</a></div></td>
	</tr>
	<tr>
		<td><div><a class="ajax-link" href="/imageboard">Imageboard</a></div></td>
		<td>
			<?php if($login->get_status()){ ?>
				<div><a class="ajax-link" href="/profile/<?php echo $login->get_username();?>">Profile</a></div>
			<?php }else{ ?>
				<form class="login" method="post">
					<input type="hidden" name="csrf_token" value="<?php echo $security->csrf_token; ?>" />
					<table>
						<tr>
							<td colspan="3"><input name="user_name" tabindex="1" type="text" placeholder="Username" pattern="^[a-zA-Z][a-zA-Z0-9.\-_]{3,31}$" /></td>
							<td><input class="header-link login-submit" name="login" tabindex="3" type="submit" value="Enter" /></td>
						</tr>
						<tr>
							<td colspan="3"><input name="user_pass"  autocomplete="off" placeholder="Password" tabindex="2" type="password" /></td>
							<td>
								<div class="slider">
									<p>Remember Me:</p>
									<input id="remember_me" name="remember_me" tabindex="4" type="checkbox" value="1" />
									<label for="remember_me"></label>
								</div>
							</td>
						</tr>
					</table>
				</form>
			<?php } ?>
		</td>
	</tr>
</table>

<table class="navbar">
	<tr>
		<td><a class="ajax-link navbar-logo" href="/">Cenari.us</a></td>
		<td><div><a class="ajax-link" href="/">Front Page</a></div></td>
		<td><div><a class="ajax-link" href="/programming">Programming</a></div></td>
		<td><div><a class="ajax-link" href="/security">Security</a></div></td>
		<td><div><a class="ajax-link" href="/starcraft-2">Starcraft 2</a></div></td>
		<td><div><a class="ajax-link" href="/world-of-warcraft">World of Warcraft</a></div></td>
	</tr>
</table>