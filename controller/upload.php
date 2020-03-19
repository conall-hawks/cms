<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Uploads controller.                                                          |
\-----------------------------------------------------------------------------*/
class Upload extends Controller {

    /* Garbage collection probability in percentage chance. */
    public $cleanup_probability = 0.01;

    /* Where to store password-protected uploads. */
    public $dir_password = ASSET.'/upload/password';

    /* Where to store public uploads. */
    public $dir_public = ASSET.'/upload/public';

    /* Where to store private uploads. */
    public $dir_private = ASSET.'/upload/private';

    /* Where to store private uploads. */
    public $dir_tmp = ASSET.'/upload/tmp';

    /* Upload's file size maximum. */
    public $file_size_max = PHP_INT_MAX;
    public $file_size_max_enforce = false;

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Load controller defaults.
        parent::__construct();

        // Handle uploads.
        if(!empty($_FILES['qqfile'])) $this->upload();
        if(!empty($_FILES['upload'])) $this->upload_fallback();

        // Handle inappropriate content reports.
        if(!empty($_POST[$this->view->form_token('report')])){
            $this->report($_POST[$this->view->form_token('id')]);
        }

        // Maintain garbage collection.
        if(ENVIRONMENT === 'development') $this->cleanup_probability = 1;
        if(mt_rand(1, 1 / $this->cleanup_probability)) $this->cleanup();
    }

    /**------------------------------------------------------------------------\
    | Garbage collection.                                                      |
    \-------------------------------------------------------------------------*/
    private function cleanup(){
        logger('Running uploads cleanup.');

        // Delete public files older than 7 days.
        $public_dirs = [$this->dir_password, $this->dir_public, $this->dir_private, $this->dir_tmp];
        foreach($public_dirs as $i => $dir) if(!is_dir($dir)) unset($public_dirs[$i]);
        foreach($public_dirs as $dir){
            $inodes = new FilesystemIterator($dir);
            $now = time();
            foreach($inodes as $inode){
                if($now - $inode->getCTime() >= 604800){
                    logger('Deleting upload file: "'.$inode->getPathname().'". Reason: Older than 7 days.');
                    runlink($inode->getPathname());
                }
            }
        }

        // Delete database entries which point to missing files.
        $this->model();
        $uploads = $this->model->upload_select();
        foreach($uploads as $upload){
            if(!is_file(ltrim($upload['path'], '/'))){
                logger('Deleting: "'.($upload['Name'] ?? '').'" from database. Reason: File missing.');
                $this->db->upload_delete($upload['id']);
            }
        }

        // Get upload directories.
        $upload_dirs = [$this->dir_password, $this->dir_public, $this->dir_private];
        $upload_files = array_map(function($i){ return ltrim($i['path'], '/'); }, $uploads);
        foreach($upload_dirs as $dir){
            if(!is_dir($dir)) continue;
            $inodes = new FilesystemIterator($dir, FilesystemIterator::UNIX_PATHS);
            foreach($inodes as $inode){
                if(!in_array($inode->getPathname(), $upload_files)){
                    logger('Deleting file: "'.$inode->getPathname().'". Reason: Missing from database.');
                    unlink($inode->getPathname());
                }
            }
        }

        // Delete empty upload directories.
        foreach(array_merge($upload_dirs, [$this->dir_tmp]) as $dir) if(is_dir($dir) && is_empty_dir($dir)){
            logger('Deleting upload folder: "'.$dir.'". Reason: Empty.');
            runlink($dir);
        }

        // Temporary directory; delete files older than 1 day.
        if(is_dir($this->dir_tmp)){
            $inodes = new FilesystemIterator($this->dir_tmp, FilesystemIterator::UNIX_PATHS);
            foreach($inodes as $inode){
                if(time() - $inode->getMTime() > 86400){
                    runlink($inode->getPathname());
                }
            }
        }
    }

    /**------------------------------------------------------------------------\
    | Handle password-protected files.                                         |
    \-------------------------------------------------------------------------*/
    public function password($name){

        // 404 on missing filenames.
        if(empty($name[0]) || empty($name[1]) || $name === 'download'){
            $user = load_controller('user');
            if($user->is_admin()) $this->view->html();
            logger('Missing file name.');
            http_response_code(404);
            $this->view->html();
        }

        // Get upload metadata.
        if(!$this->db) $this->db();
        $upload = $this->db->upload_select(NULL, 'password', $name[0])[0] ?? NULL;

        // Get path to file.
        $upload['path'] = ltrim($upload['path'], '/');
        if(is_file($upload['path'])){

            // Verify password.
            $user = load_controller('user');
            if($user->is_admin() || isset($_POST[$this->view->form_token('password')])){
                if($user->is_admin() || password_verify($_POST[$this->view->form_token('password')] ?? '', $upload['password'])){
                    header('Content-Disposition: attachment; filename="'.urlencode($upload['name']).'"');
                    View::file($upload['path']);
                }else{
                    $this->view->feedback = 'Wrong password.';
                    logger($this->view->feedback);
                    #http_response_code(403);
                }
                $this->view->feedback = 'Wrong password.';
                logger($this->view->feedback);
            }
            $this->view->html['content'] = TEMPLATE.'/content/upload/password/download.php';
            $this->view->use_explorer = false;
            $this->view->html();
        }

        // Handle missing file.
        logger('File \''.$upload['path'].'\' does not exist.');
        http_response_code(404);
        $this->view->html();
    }

    /**------------------------------------------------------------------------\
    | Handle private files.                                                     |
    \-------------------------------------------------------------------------*/
    public function private($name){

        // Show template on missing filenames.
        if(empty($name[0]) || empty($name[1])) $this->view->html();

        // Get filename.
        $id = $name[0];

        // Get upload metadata.
        if(!$this->db) $this->db();
        $upload = $this->db->upload_select(NULL, 'private', $id)[0] ?? NULL;

        $user = load_controller('user');
        if($user->id() !== $upload['user_id']){
            logger('Unable to open "'.$name[1].'".');
            http_response_code(403);
            $this->view->html();
        }

        // Get path to file.
        $upload['path'] = ltrim($upload['path'], '/');
        if(is_file($upload['path'])){

            // Set MIME type.
            $mime = mime($upload['path']);
            if(substr($mime, 0, 5) === 'text/' || $mime === 'application/javascript'){
                $mime .= '; charset=utf-8';
            }
            header('Content-Type: '.$mime);

            // Output file.
            while(ob_get_level()) ob_end_clean();
            die(file_get_contents($upload['path']));
        }

        // Handle missing file.
        logger('File \''.$upload['path'].'\' does not exist');
        http_response_code(404);
        $this->view->html();
    }

    /**------------------------------------------------------------------------\
    | Handle public files.                                                     |
    \-------------------------------------------------------------------------*/
    public function public($name){

        // 404 on missing filenames.
        if(empty($name[0]) || empty($name[1])){
            logger('Missing file name.');
            http_response_code(404);
            $this->view->html();
        }

        // Get filename.
        $id = $name[0];

        // Get upload metadata.
        if(!$this->db) $this->db();
        $upload = $this->db->upload_select(NULL, 'public', $id)[0] ?? NULL;

        // Get path to file.
        $upload['path'] = ltrim($upload['path'], '/');
        if(is_file($upload['path'])) View::file($upload['path']);

        // Handle missing file.
        logger('File \''.$upload['path'].'\' does not exist');
        http_response_code(404);
        $this->view->html();
    }

    /**------------------------------------------------------------------------\
    | Handle inappropriate content reports.                                    |
    \-------------------------------------------------------------------------*/
    private function report($id){

        /* Begin a session if one has not already started. */
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        // Idempotent reporting.
        if(!empty($_SESSION['uploads']['reported'])
        && in_array($id, $_SESSION['uploads']['reported'])) return false;

        // Validate ID.
        if(!is_numeric($id)) throw new Exception('ID must be numeric.');

        // Increment reported count.
        $this->model();
        $upload = $this->model->upload_select($id)[0];

        // Import dependencies.
        $user = load_controller('user');

        // Delete upload if we have permission.
        if($user->id() === $upload['user_id'] || $user->is_admin()){
            $this->model->upload_delete($id);
            logger('Upload "'.$upload['name'].'" has been deleted.');
        }

        // Report upload.
        else{
            $this->model->set('upload', [
                'id'       => $id,
                'reported' => (int)$upload['reported'] + 1,
            ]);
            logger('Thank you! Upload "'.$upload['name'].'" has been reported.');

            // Add to blacklist.
            $_SESSION['uploads']['reported'][] = $id;
        }
    }

    /**------------------------------------------------------------------------\
    | Handle upload events.                                                    |
    \-------------------------------------------------------------------------*/
    private function upload(){

        // Replies are in JSON.
        header('Content-Type: application/json');

        /* Begin a session if one has not already started. */
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        // Validate and verify CAPTCHA.
        if(empty($_SESSION['captcha'])){
            http_response_code(403);
            $error = 'Missing CAPTCHA session variable.';
            logger($error);
            die(json_encode(['success' => false, 'error' => $error]));
        }
        if(empty($_POST['captcha'])){
            http_response_code(403);
            $error = 'Missing CAPTCHA in POST.';
            logger($error);
            die(json_encode(['success' => false, 'error' => $error]));
        }
        if($_SESSION['captcha'] !== $_POST['captcha']){
            http_response_code(403);
            $error = 'Wrong CAPTCHA.';
            if(ENVIRONMENT === 'development'){
                $error = 'Wrong CAPTCHA. Expected: "'.$_SESSION['captcha'].'". Got: "'.$_POST['captcha'].'".';
            }
            logger($error);
            die(json_encode(['success' => false, 'error' => $error]));
        }

        // Load the Fine Uploader library.
        require_once(LIBRARY.'/fine-uploader.php');
        $upload = new UploadHandler();
        $upload_dir = ASSET.'/upload/tmp';
        $upload->chunksFolder = $upload_dir;
        $upload->chunksCleanupProbability = 0.01;
        $upload->chunksExpireIn = 10;

        // Keep development environment clean.
        if(ENVIRONMENT === 'development') $upload->chunksCleanupProbability = 1;

        // Ensure the temporary uploads folder exists.
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0770, true);

        // Handle upload.
        $output = $upload->handleUpload($upload_dir);

        // Handle chunked completion.
        $complete = false;
        if(!empty($_POST['qqtotalparts']) && !empty($_POST['qqpartindex'])
        && (int)$_POST['qqpartindex'] === (int)$_POST['qqtotalparts'] - 1){

            // Combine chunked uploads.
            $output = $upload->combineChunks($upload_dir);

            // Mark as completed.
            if($output['success']) $complete = true;
        }

        // Handle unchunked completion.
        elseif(empty($_POST['qqtotalparts']) && empty($_POST['qqpartindex'])){

            // Mark as completed.
            $complete = true;
        }

        // Move completed uploads.
        if($complete){

            // Path to the upload.
            $source = $upload_dir.'/'.$_POST['qquuid'].'/'.$_POST['qqfilename'];

            // Set variables based on privacy level.
            switch($_POST['privacy']){
                case 'password':
                    $privacy  = 'password';
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $target   = $this->dir_password.'/'.hash('sha512', hash_file('sha512', $source).$password).'.dat';
                    break;
                case 'private':
                    $privacy  = 'private';
                    $password = NULL;
                    $target   = $this->dir_private.'/'.hash_file('sha512', $source).'.dat';
                    break;
                default:
                    $privacy  = 'public';
                    $password = NULL;
                    $target   = $this->dir_public.'/'.hash_file('sha512', $source).'.dat';
            }

            // Ensure the target directory exists.
            $target_dir = dirname($target);
            if(!is_dir($target_dir)) mkdir($target_dir, 0770, true);

            // Ensure upload doesn't already exist.
            if(is_file($target)){
                $output['success'] = false;
                $output['error']   = 'File already uploaded.';
            }

            // Move the completed upload.
            else{
                rename($source, $target);

                // Cleanup temporary directory.
                rmdir($upload_dir.'/'.$_POST['qquuid']);

                // Add to database.
                $user = load_controller('user');
                if(!$this->db) $this->db();
                if(!$this->db->set('upload', [
                    'privacy'  => $privacy,
                    'name'     => $_POST['qqfilename'],
                    'path'     => '/'.$target,
                    'user_id'  => $user->id(),
                    'password' => $password
                ])){
                    $output['success'] = false;
                    $output['error']   = 'Failed to add upload to database.';
                }
            }

            // Feedback.
            if($output['success']){
                logger('Successfully uploaded file: "'.$_POST['qqfilename'].'".');
            }else{
                logger('Upload failed for: "'.$_POST['qqfilename'].'". Reason: '.$output['error'].'.');
            }
        }

        // Send response.
        die(json_encode($output));
    }

    /**------------------------------------------------------------------------\
    | Handle upload events fallback.                                           |
    \-------------------------------------------------------------------------*/
    private function upload_fallback(){
        foreach($_FILES as $file){

            // Verify error parameter.
            if(!isset($file['error'])){
                logger($file['name'].' upload failed: missing "error" parameter.');
                continue;
            }

            // Check for errors.
            switch($file['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    logger($file['name'].' upload failed: Missing file.');
                    continue 2;
                case UPLOAD_ERR_INI_SIZE:
                    logger($file['name'].' upload failed: Exceeded PHP\'s file size limit.');
                    continue 2;
                case UPLOAD_ERR_FORM_SIZE:
                    logger($file['name'].' upload failed: Exceeded form size limit.');
                    continue 2;
                default:
                    logger($file['name'].' upload failed: Unknown error.');
                    continue 2;
            }

            // Enforce size limit.
            if($this->file_size_max_enforce && $file['size'] > $this->file_size_max){
                logger($file['name'].' upload failed: Exceeded size limit ('.format_bytes($this->file_size_max).').');
                continue;
            }

            // Set variables based on privacy level.
            switch($_POST['privacy']){
                case 'password':
                    $privacy  = 'password';
                    $password = $_POST['password'] ?? '';
                    $target   = $this->dir_private.'/'.hash_file('sha512', $file['tmp_name']).'.dat';
                    break;
                case 'private':
                    $privacy  = 'private';
                    $password = NULL;
                    $target   = $this->dir_private.'/'.hash_file('sha512', $file['tmp_name']).'.dat';
                    break;
                default:
                    $privacy  = 'public';
                    $password = NULL;
                    $target   = $this->dir_public.'/'.hash_file('sha512', $file['tmp_name']).'.dat';
            }

            // Ensure the target directory exists.
            $target_dir = dirname($target);
            if(!is_dir($target_dir)) mkdir($target_dir, 0770, true);

            // Move the completed upload.
            move_uploaded_file($file['tmp_name'], $target);

            // Add to database.
            $user = load_controller('user');
            if(!$this->db) $this->db();
            $this->db->set('upload', [
                'privacy'  => $privacy,
                'name'     => $file['name'],
                'path'     => '/'.$target,
                'user_id'  => $user->id(),
                'password' => $password
            ]);
        }
    }
}
