<?php
class Register extends Controller{
	/* Success status of registration & verification. */
	private $registration_success = false;
	private $verification_success = false;
	
	/* SMTP */
	private $use_smtp = true;
	private $smtp_host = 'ssl://smtp.gmail.com';
	private $smtp_auth = true;
	private $smtp_user = SMTP_USER;
	private $smtp_pass = SMTP_PASS;
	private $smtp_port = 465;
	private $smtp_encrypt = 'ssl';
	
	/* Email verification. */
	private $verification_from = '';
	private $verification_name = 'Registration';
	private $verification_subject = 'Registration Verification';
	private $verification_url = 'localhost/register';
	private $verification_body = 'Please click on this link to activate your account: ';
	
	public $feedback = '';
	
	/* 
	 * Get a handle to $login's database. Then handle program flow.
	 */
	public function __construct(){
		global $login;
		if($login->getStatus()) return false;
		if(isset($_POST['register'], $_POST['user_name_new'], $_POST['user_email_new'], $_POST['user_pass_new'], $_POST['user_pass_repeat'], $_POST['captcha'])){
			/* The user has POSTed a registration form, it needs to be processed. */
			$this->register($_POST['user_name_new'], $_POST['user_email_new'], $_POST['user_pass_new'], $_POST['user_pass_repeat'], $_POST['captcha']);
		}elseif(isset($_GET['id'], $_GET['activation_hash'])){
			/* The user has sent us an activation hash, it needs to be verified. */
			$this->verify($_GET['id'], $_GET['activation_hash']);
		}
	}
	
	/* The registration process. Check all error possibilities, then create a new user in the database. */
	private function register($name, $email, $pass, $pass_repeat, $captcha){
		/* Trim extra space on username and email. */
		$name  = trim($name);
		$email = trim($email);
		
		/* Check data. */
		if(!isset($_SESSION['captcha'])){
			$this->feedback = 'Please re-submit the form.';
		}/*elseif(strtolower($captcha) != strtolower($_SESSION['captcha'])){
			$this->feedback = 'Wrong CAPTCHA. Please try again.';
		}*/elseif(empty($name)){
			$this->feedback = 'Empty username.';
		}elseif(empty($pass) || empty($pass_repeat)){
			$this->feedback = 'Empty password.';
		}elseif($pass !== $pass_repeat){
			$this->feedback = 'Passwords do not match.';
		}elseif(strlen($pass) < 6){
			$this->feedback = 'Password too short.';
		}elseif(strlen($name) > 64 || strlen($name) < 4){
			$this->feedback = 'Username is too big/small.';
		}elseif(!preg_match('/^[a-z\d]{2,64}$/i', $name)){
			$this->feedback = 'Username may only contain letters/numbers.';
		}elseif(empty($email)){
			$this->feedback = 'Empty email.';
		}elseif(strlen($email) > 65){
			$this->feedback = 'Email too long.';
		}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$this->feedback = 'Invalid email.';
		}elseif($this->model('login')){
			/* Check for a similiar username/email. */
			$result = $this->db->get_user($name, $email);
			if(!empty($result)){
				$this->feedback = ($result['Name'] == $name) ? 'User \''.$name.'\' already exists.' : 'Email \''.$email.'\' is already in use.';
			}else{
				/* Add the new user to the database. */
				$user = $this->db->add_user($name, $email, $pass);
				
				/* Was the user written into the database successfully? */
				if($user['ID']){
					/* Send verification email. */
					if($this->send_verification($user['ID'], $email, $user['ActivationHash'])){
						return true;
					}else{
						/* Delete this account immediately; we could not send a verification email. */
						$this->db->remove_user($user['ID']);
					}
				}else{
					$this->feedback = 'Registration failed. New user could not be added to the database.';
				}
			}
		}else{
			$this->feedback = 'Could not connect to the login database.';
		}
		return false;
	}

	/* Send a verification email to the provided email address.
	 * @return bool: True if mail has been sent, false if no mail could be sent.
	 */
	private function send_verification($id, $email, $activation_hash){
		require_once(INCLUDES.'/phpmailer/class.phpmailer.php');
		require_once(INCLUDES.'/phpmailer/class.smtp.php');
		$mail = new PHPMailer;
		/* Use SMTP or mail(). */
		if($this->use_smtp){
			/* Set PHPMailer to use SMTP */
			$mail->IsSMTP();
			/* Debugging tool, shows SMTP errors. 1: Errors and messages, 2: Messages only. */
			//$mail->SMTPDebug = 1;
			/* Enable/Disable SMTP authentication */
			$mail->SMTPAuth = $this->smtp_auth;
			/* Enable encryption, usually SSL/TLS */
			if(defined($this->smtp_encryption)) $mail->SMTPSecure = $this->smtp_encryption;
			/* Specify host server */
			$mail->Host = $this->smtp_host;
			$mail->Username = $this->smtp_user;
			$mail->Password = $this->smtp_pass;
			$mail->Port = $this->smtp_port;
		}
		
		/* Build mail */
		if(empty($this->verification_from)) $this->verification_from = $_SERVER['SERVER_ADMIN'];
		$mail->setFrom($this->verification_from, $this->verification_name);
		$mail->AddAddress($email);
		$mail->Subject = $this->verification_subject;
		
		/* Generate activation hash link for mail body. */
		$link = $this->verification_url.'?id='.urlencode($id).'&activation_hash='.urlencode($activation_hash);
		$mail->Body = $this->verification_body.$link;
		
		if($mail->Send()){
			$this->feedback = 'Verification mail was sent, check your inbox to activate your account.';
			return true;
		}else{
			$this->feedback = 'Verification mail could not be sent. ' . $mail->ErrorInfo;
			return false;
		}
	}

	/* Checks the ID/verification code combination then sets the user's activation status to 1 in the database.
	 */
	private function verify($id, $activation_hash){
			$sql = $this->db->prepare('UPDATE user SET active = 1, activation_hash = NULL WHERE id = :id AND activation_hash = :activation_hash');
			$sql->bindValue(':id', intval(trim($id)), PDO::PARAM_INT);
			$sql->bindValue(':activation_hash', $activation_hash, PDO::PARAM_STR);
			$sql->execute();
			if($sql->rowCount() > 0){
				$this->verification_successful = true;
				$this->feedback = 'Verification successful.';
			}else{
				$this->feedback = 'Verification failure.';
			}
		}
	}
