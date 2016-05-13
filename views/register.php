<article class="content-box">
	<h2>Registration: <span class="red" style="float: right; font-size: 60%;"><?php echo $register->feedback?></span></h2>
	<?php if($login->getStatus() === 1){ ?>
		<p>You are already logged in! ;3</p>
	<?php }else{ ?>
		<form method="post">
			<table>
				<tbody>
					<tr>
						<td colspan="2" rowspan="3">
							<fieldset>
								<legend>Header:</legend>
								<input accesskey="n" name="user_name_new" placeholder="Username" pattern="[a-zA-Z0-9]{4,64}" tabindex="5" type="text" />
								<input accesskey="e" name="user_email_new" placeholder="Email" tabindex="6" type="text" pattern="[a-zA-Z0-9]+(?:(\.|_|\+)[A-Za-z0-9!#$%&'*+/=?^`{|}~-]+)*@(?!([a-zA-Z0-9]*\.[a-zA-Z0-9]*\.[a-zA-Z0-9]*\.))(?:[A-Za-z0-9](?:[a-zA-Z0-9-]*[A-Za-z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?" />
								<input accesskey="p" name="user_pass_new" placeholder="Password" tabindex="7" type="password" pattern="^.{7,64}$" />
								<input accesskey="p" name="user_pass_repeat" placeholder="Password" tabindex="8" type="password" pattern="^.{7,64}$" />
							</fieldset>
						</td>
						<td colspan="4" rowspan="6">
							<fieldset >
								<legend>Information:</legend>
								<ul>
									<li>Usernames must be between 4 to 64 characters.</li>
									<li>Email must be valid; verification will be sent. Temporary emails: <a href="https://www.guerrillamail.com/" target="_blank">here</a>.</li>
									<li>Passwords must be greater than 6 characters.</li>
									<li>Allows you to post in the imageboard without using a captcha.</li>
									<li>Allows you to post in the forum.</li>
									<li>Gives you a name in the chatroom.</li>
									<li>Allows access to exclusive content.</li>
									<li>Allows you to send messages to other members.</li>
									<li>Removes some ads.</li>
									<li>Rules:</li>
									<li>Don't piss the mods off: They have ban privileges.</li>
									<li>Don't piss the admin off: They also have ban privileges.</li>
									<li>If you would like to view the privacy policy regarding user accounts, click <a href="/privacy#members">here</a>.</li>
									<li></li>
									<li>This system is in beta.</li>
								</ul>
							</fieldset>
						</td>
					</tr>
					<tr></tr>
					<tr></tr>
					<tr>
						<td colspan="2" rowspan="2">
							<fieldset>
								<legend>Captcha:</legend>
								<img class="captcha" src="/includes/captcha/captcha.php" alt="Enter this text into the box below." />
								<input accesskey="c" autocomplete="off" name="captcha" placeholder="Enter the CAPTCHA above." size="6" tabindex="9" type="text" />
							</fieldset>
						</td>
					</tr>
					<tr></tr>
					<tr>
						<td colspan="2">
							<fieldset>
								<input accesskey="s" name="register" tabindex="10 " type="submit" value="Submit" />
							</fieldset>
						</td>
					</tr>
					</tbody>
			</table>
		</form>
	<?php } ?>
</article>