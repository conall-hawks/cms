<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| User controller.                                                             |
\-----------------------------------------------------------------------------*/
class User extends _Controller {

    /** @var object: Maximum failed login attempts allowed. */
    private $max_failed_logins = -1;

    /**------------------------------------------------------------------------\
    | Construct; handle various POST requests for user management.             |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        /* Load model and view. */
        parent::__construct();

        /* Login. */
        if(isset(
            $_POST[$this->view->form_token('username')],
            $_POST[$this->view->form_token('password')])
        ){
            $this->login(
                $_POST[$this->view->form_token('username')],
                $_POST[$this->view->form_token('password')]
            );
        }

        /* Logout. */
        if(isset($_POST[$this->view->form_token('logout')])){
            $this->logout();
        }

        /* Register. */
        if(!empty($_POST['register'])){
            if(!captcha_verify($_POST[$this->view->form_token('captcha')])){
                 logger('CAPTCHA failed.');
            }elseif($_POST['password'] !== $_POST['password_repeat']){
                logger('Passwords do not match.');
            }elseif($this->register($_POST['username'], $_POST['password'])){
                $this->login($_POST['username'], $_POST['password']);
                #global $uri;
                #$uri->set_route('/profile/'.$this->username());
                #unset($_POST);
                #$this->view = load_controller('profile')->view->html();
                header('Location: /profile/'.$this->username(), true, 301);
                die();
            }
        }

        /* Handle guest account. */
        if(!$this->status()){

            /* Begin a session if one has not already started. */
            if(session_status() !== PHP_SESSION_ACTIVE) session_start();

            if(!isset($_SESSION['user']) || !isset($_SESSION['user']['username'])){

                /* Connect to the database. */
                if(!$this->db) $this->db();

                /* Generate a guest name that isn't already taken. */
                do{$_SESSION['user']['username'] = 'Guest-'.substr(uniqid(), -4);}
                while($this->db->get_user($_SESSION['user']['username']));
            }
        }

        /* Password reset. */
        if(!empty($_POST['password_reset'])){

        }

        /* Profile. */
        if(!empty($_POST['profile'])){

            /* Avatar upload. */
            if(is_file($_FILES['avatar']['tmp_name'])) $this->upload('avatar');

            /* Photograph upload. */
            if(is_file($_FILES['photo']['tmp_name'])) $this->upload('photo');

            /* Username change. */
            if(!empty($_POST['username']) && is_string($_POST['username'])){
                $this->username_set($_SESSION['user']['username'], $_POST['username']);
            }

            /* Basic strings. */
            foreach(['location', 'status'] as $field){
                if(!isset($_POST[$field])) continue;
                $field = mb_substr($field, 0, 255);
                if(!$this->db) $this->db();
                if(!$this->db->set('user', ['id' => $_SESSION['user']['id'], $field => $_POST[$field]])){
                    logger(mb_ucfirst($key).' change failed.');
                }
            }
        }
    }

    /**------------------------------------------------------------------------\
    | Updates a user's name.                                                   |
    +---------+---------+-----------+------------------------------------------+
    | @param  | string  | $username | New name for this user.                  |
    | @return | boolean |           | Login result.                            |
    \---------+---------+-----------+-----------------------------------------*/
    private function username_set($old_username, $new_username){

        /* Connect to the database. */
        if(!$this->db) $this->db();

        /* Normalize username. */
        if(mb_strlen($new_username) > $this->db->username_length_max){
            $new_username = mb_substr($new_username, 0, $this->db->username_length_max);
            logger('Username too long. Truncated to '.$this->db->username_length_max.' characters.');
        }

        /* Validate old username. */
        $user = $this->db->get_user($old_username);
        if(!$user){
            logger('User does not exist.');
            return false;
        }

        /* Validate new username. */
        if(mb_strlen($new_username) < $this->db->username_length_min){
            logger('Username too short.');
            return false;
        }
        if($this->db->get_user($new_username)){
            logger('Username taken.');
            return false;
        }

        /* Change username. */
        if(!$this->db->set('user', ['id' => $user['id'], 'username' => $new_username])){
            logger('Username change failed.');
            return false;
        }
        $_SESSION['user']['username'] = $new_username;

        /* Done. */
        logger('Username changed to: "'.$new_username.'".');
        return true;
    }

    /*-------------------------------------------------------------------------\
    | Upload a file.                                                           |
    \-------------------------------------------------------------------------*/
    public function upload($key, $max = 1048576 * 5, $min = 1048576 * 5){

        // Ignore non-posts.
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            logger('Only POST requests allowed.');
            http_response_code(404);
            $this->view->html();
        }

        /* Get file upload. */
        if(!isset($_FILES[$key])){
            logger(mb_ucfirst($key).' upload failed: unknown key "'.$key.'".');
            return false;
        }
        $_FILES[$key];

        /* Verify error parameter. */
        if(!isset($_FILES[$key]['error'])){
            logger(mb_ucfirst($key).' upload failed: missing error parameter.');
            return false;
        }

        /* Check for errors. */
        switch($_FILES[$key]['error']){
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                logger(mb_ucfirst($key).' upload failed: No file.');
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                logger(mb_ucfirst($key).' upload failed: Exceeded size limit.');
                return false;
            default:
                logger(mb_ucfirst($key).' upload failed: Unknown error.');
                return false;
        }

        if($file['size'] > $max){
            logger(mb_ucfirst($key).' upload failed: Exceeded size limit.');
            return false;
        }

        /* Enforce type. */
        if(in_array($key, ['avatar', 'photo'])){
            $allowed = [
                'gif' => 'image/gif',
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            ];
            $mime = new finfo(FILEINFO_MIME_TYPE);
            $mime = $mime->file($file['tmp_name']);
            $ext  = array_search($mime, $allowed);
            if(!$ext){
                logger(mb_ucfirst($key).' upload failed: Invalid file type. Allowed types: '.implode(', ', array_keys($allowed)).'.');
                return false;
            }
        }else{
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        }

        /* Build file name and path. */
        $name = hash_file('sha512', $file['tmp_name']).($ext ? '.'.$ext : '');
        if(in_array($key, ['avatar', 'photo'])){
            $dir  = IMAGE.'/user/'.$key;
            $path = $dir.'/'.$name;
        }elseif($key === 'upload'){
            global $user;
            $dir  = ASSET.'/'.$key.'/'.$user->id();
            $path = $dir.'/'.$name;
        }

        /* Ensure parent directories exist. */
        if(!is_dir($dir)){
            $mask = umask(0);
            mkdir($dir, 0775, true);
            umask($mask);
        }

        /* TODO: EXIF stripping. */
        #

        /* TODO: Normalizations. */
        #

        /* Rename and move upload. */
        if(!file_exists($path)){
            if(!move_uploaded_file($file['tmp_name'], $path)){
                logger(mb_ucfirst($key).' upload failed: Failed to move uploaded file.');
                return false;
            }
        }else{
            logger(mb_ucfirst($key).' upload canceled: Duplicate file exists.');
            return false;
        }

        /* Connect to the database. */
        if(!$this->db) $this->db();

        /* Remove old file. */
        if(in_array($key, ['avatar', 'photo'])){
            $old_file = ltrim($this->db->get_user($this->username())[$key], '/');
            if(file_exists($old_file)) unlink($old_file);
        }

        /* Limit upload. */
        if(in_array($key, ['upload'])){
            $files = $this->db->get_uploads();
            foreach($files as $file){
                $size += filesize(ASSET.'/'.$key.'/'.$_SESSION['user']['id'].'/'.$file['name']);
            }
        }

        /* Add path to database. */
        if(!$this->db->set('user', ['id' => $_SESSION['user']['id'], $key => '/'.$path])){
            logger(mb_ucfirst($key).' change failed: Failed to add path to database.');
            return false;
        }

        /* Clean orphaned uploads. */
        $this->db->clean_uploads();

        /* Done! */
        logger('Successfully changed '.$key.' to: '.basename($file['name']).'.');
    }

    /**------------------------------------------------------------------------\
    | Log the user in.                                                         |
    |                                                                          |
    | Record failed login attempts.                                            |
    +---------+---------+-----------+------------------------------------------+
    | @param  | string  | $username | Name of the user.                        |
    | @param  | string  | $password | Password.                                |
    | @return | boolean |           | Login result.                            |
    \---------+---------+-----------+-----------------------------------------*/
    private function login($username, $password){

        /* Connect to the database. */
        if(!$this->db) $this->db();

        /* Verify user exists. */
        $user = $this->db->get_user($username);
        if(!$user){
            if(ENVIRONMENT === 'development') logger('User does not exist');
            logger('Login failed.');
            return false;
        }

        /* Check for too many failed login attempts. */
        if($this->max_failed_logins > 0 && $user['failed_logins'] >= $this->max_failed_logins){
            if(ENVIRONMENT === 'development') logger('Too many failed login attempts.');
            $this->db->failed_login($username);
            logger('Login failed.');
            return false;
        }

        /* Verify password. */
        if(!password_verify($password, $user['password'])){
            if(ENVIRONMENT === 'development') logger('Wrong password.');
            logger('Login failed.');
            $this->db->failed_login($username);
            return false;
        }

        /* Success! Log the user in. */
        $this->logout();
        $_SESSION['user'] = $user;

        /* Reset failed login attempts. */
        $this->db->failed_login($username, 0);

        /* Done. */
        logger('Login successful.');
        return true;
    }

    /**------------------------------------------------------------------------\
    | Log the user out; wipe session.                                          |
    \-------------------------------------------------------------------------*/
    public function logout(){

        // Wipe cookie via induced expiry.
        if(isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie){
                $name = trim(explode('=', $cookie)[0]);
                setcookie($name, '', time() - 3600);
                setcookie($name, '', time() - 3600, '/');
            }
        }

        // Wipe session; preserve log.
        $log = $_SESSION['log'];
        $_SESSION = [];
        session_destroy();

        // Start new session, but using same session ID.
        session_start();
        header('Set-Cookie: PHPSESSID='.session_id().'; secure; httpOnly; sameSite=strict');
        $_SESSION['log'] = $log;

        // Feedback.
        logger('Logout successful; session wiped.');
        return true;
    }

    /**------------------------------------------------------------------------\
    | Register a new user.                                                     |
    \-------------------------------------------------------------------------*/
    public function register($username, $password){

        /* Connect to the database. */
        if(!$this->db) $this->db();

        /* Validate username. */
        if($this->db->get_user($username)){
            logger('Username taken');
            return false;
        }
        if(mb_strlen($username) > $this->db->username_length_max){
            logger('Username too long.');
            return false;
        }
        if(mb_strlen($username) < $this->db->username_length_min){
            logger('Username too short.');
            return false;
        }

        /* Validate password. */
        if(mb_strlen($password) < 4){
            logger('Password too short.');
            return false;
        }

        /* Prepare parameters. */
        $user['username'] = $username;
        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        $user['ip']       = $_SERVER['REMOTE_ADDR'];

        /* Add user to database. */
        if(!$this->db->set('user', $user)){
            logger('Registration failed.');
            return false;
        }

        /* Done. */
        logger('Registration successful.');
        return true;
    }

    /**------------------------------------------------------------------------\
    | Get the login status.                                                    |
    \-------------------------------------------------------------------------*/
    public function status(){

        /* Begin a session if one has not already started. */
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        /* Verify session. */
        if(empty($_SESSION['user'])) return false;

        /* Verify non-guest. */
        if($this->is_guest()) return false;

        /* Done. */
        return true;
    }

    /**------------------------------------------------------------------------\
    | Get the username.                                                        |
    \-------------------------------------------------------------------------*/
    public function username($id = NULL){
        if(!is_numeric($id)){
            if(!empty($_SESSION['user'])) return $_SESSION['user']['username'];
        }else{
            return $this->db->get_user($id, 'id')['username'];
        }
    }

    /**------------------------------------------------------------------------\
    | Get the id.                                                              |
    \-------------------------------------------------------------------------*/
    public function id($username = NULL){
        if(!$username){
            if(!empty($_SESSION['user'])) return $_SESSION['user']['id'] ?? NULL;
        }else{
            return $this->db->get_user($username)['id'];
        }
    }

    /**------------------------------------------------------------------------\
    | Get the privilege level.                                                 |
    \-------------------------------------------------------------------------*/
    public function privilege_level(){
        if(!isset($_SESSION['user'])) return 99;
        if(!isset($_SESSION['user']['privilege_level'])) return 99;
        return $_SESSION['user']['privilege_level'];
    }
    public function is_admin(){
        if((int)$this->privilege_level() === 1) return true;
        return false;
    }
    public function is_guest(){
        if((int)$this->privilege_level() === 99) return true;
        return false;
    }
}
