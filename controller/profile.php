<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| User profiles.                                                               |
\-----------------------------------------------------------------------------*/
class Profile extends Controller {

    public function __construct(){
        parent::__construct();
        global $uri;
        $user = load_controller('user');
        $profile = $user->model->get_user(urldecode(explode('/', $uri->path)[2] ?? $user->username()), 'username');
        if(!$profile) http_response_code(404);
        else $this->user = $profile;
    }

    public function upload($file = NULL){

        // Connect to the database.
        if(!$this->db) $this->db();

        // Handle deletions.
        if(!empty($_POST['delete']) && !empty($_POST['file'])){
            $this->db->upload_delete($_POST['file']);
        }

        // Handle uploads.
        $this->db->upload();

        // Special stuff.
        if(in_array($file, ['progress', 'select'])) $this->view->html();
        if($file === 'cancel'){
            $key = ini_get('session.upload_progress.prefix').'upload';
            if(!empty($_SESSION[$key])) $_SESSION[$key]['cancel_upload'] = true;
            $this->view->html(TEMPLATE.'/layout/profile/upload/select.php');
        }

        // View uploads.
        if($file === 'list') $this->view->html();
        if($file){
            $uploads = $this->db->get_uploads($this->view->user['id']);
            foreach($uploads as $upload){
                if($upload['name'] === rawurldecode($file)){
                    $path = ltrim($upload['path'], '/');
                    if(is_file($path)) View::file($path);
                }
            }
            die('File not found.');
        }else{
            $this->view->use_explorer = true;
            $this->view->html();
        }
    }
}
