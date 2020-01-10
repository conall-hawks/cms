<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**------------------------------------------------------------------------\
| CAPTCHA generator.                                                       |
\-------------------------------------------------------------------------*/
class Pix extends Controller {

    /* Garbage collection probability in percentage chance. */
    public $cleanup_probability = 0.01;

    /* Where to store uploads. */
    public $dir_upload = ASSET.'/upload/pix';

    /* Where to store thumbnails. */
    public $dir_thumb = ASSET.'/upload/pix/thumb';

    /* Where to store temporary files. */
    public $dir_tmp = ASSET.'/upload/tmp';

    /* Upload's file size maximum. */
    public $file_size_max = PHP_INT_MAX;
    public $file_size_max_enforce = false;

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){
        parent::__construct();
        if(!empty($_POST[$this->view->form_token('board')])){
            header('Location: /pix/'.urlencode($_POST[$this->view->form_token('board')]), true, 301);
            die();
        }

        // Handle uploads.
        if(!empty($_FILES['qqfile'])) $this->upload();
        if(!empty($_FILES['upload'])) $this->upload_fallback();
    }

    private function upload(){

        /* Replies are in JSON. */
        header('Content-Type: application/json');

        /* Validate CAPTCHA. */
        if(!$security->validate_captcha()) die(json_encode(['success' => false, 'error' => 'Invalid CAPTCHA.']));

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

            // FIXME
            $target = $this->dir_public.'/'.hash_file('sha512', $source).'.dat';

            // Ensure the target directory exists.
            $target_dir = dirname($target);
            if(!is_dir($target_dir)) mkdir($target_dir, 0770, true);

            // Move the completed upload.
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

    private function upload_fallback(){
        print_r($_FILES['upload']);
    }
}
