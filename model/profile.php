<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Login database.                                                              |
\-----------------------------------------------------------------------------*/
class Profile_model extends Model {
    protected $db_name = 'user';

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Connect to the database.
        parent::__construct();

        // Use to rebuild and reset the database.
        #$this->rebuild();
        #$this->reset();
    }

    /**------------------------------------------------------------------------\
    | Get the login information of a user.                                     |
    \-------------------------------------------------------------------------*/
    public function get_uploads($id = NULL){

        /* Begin a session if one has not already started. */
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        /* Default ID. */
        if(!is_numeric($id)){
            if(!isset($_SESSION['user']['id'])) return;
            $id = $_SESSION['user']['id'];
        }

        /* Query uploads. */
        $sql = $this->db->prepare("SELECT * FROM `upload` WHERE `user_id` = :id;");
        $sql->execute([':id' => $id]);
        return $sql->fetchAll();
    }

    public function upload_delete($name){

        /* Begin a session if one has not already started. */
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        if(!isset($_SESSION['user']['id'])) return;
        $id = $_SESSION['user']['id'];

        /* Query uploads. */
        $sql = $this->db->prepare("SELECT * FROM `upload` WHERE `user_id` = :id AND `name` = :name;");
        $sql->execute([':id' => $id, ':name' => $name]);
        $upload = $sql->fetchAll()[0];

        /* Delete file. */
        $file = ltrim($upload['path'], '/');
        unlink($file);
        $dir = dirname($file);
        if(is_empty_dir($dir)) rmdir($dir);

        /* Delete database entry. */
        $sql = $this->db->prepare("DELETE FROM `upload` WHERE `user_id` = :id AND `name` = :name;");
        $sql->execute([':id' => $id, ':name' => $name]);
    }

    /**------------------------------------------------------------------------\
    | Get the login information of a user.                                     |
    \-------------------------------------------------------------------------*/
    public function upload(){

        // Process file upload form.
        if(!empty($_FILES['upload']) && is_file($_FILES['upload']['tmp_name'])){

            // Validate privacy parameter.
            switch($_POST['privacy']){
                case 'password':
                case 'private':
                case 'public':
                    $privacy = $_POST['privacy'];
                    break;
                default:
                    logger('Invalid privacy parameter.');
                    return false;
            }

            // Verify error parameter.
            if(!isset($_FILES['upload']['error'])){
                logger('Storage upload failed: missing error parameter.');
                return false;
            }

            // Check for errors.
            switch($_FILES['upload']['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    logger('Storage upload failed: No file.');
                    return false;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    logger('Storage upload failed: Exceeded file size limit.');
                    return false;
                default:
                    logger('Storage upload failed: Unknown error.');
                    return false;
            }

            // Enforce size. Multiplier in MiB.
            #$max = 1048576 * 10;
            #ini_set('post_max_size', $max);
            #ini_set('upload_max_filesize', $max);
            #if($_FILES['upload']['size'] > $max){
            #    logger('Storage upload failed: Exceeded file size limit.');
            #    return false;
            #}

            /* Begin a session if one has not already started. */
            if(session_status() !== PHP_SESSION_ACTIVE) session_start();

            /* Ensure the user is logged in. */
            if(!isset($_SESSION['user']['id'])){
                header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden', true, 403);
                if(ENVIRONMENT === 'development'){
                    throw new Exception('Not logged in.');
                }
                die();
            }

            // Build file name and path.
            global $user;
            $dir  = ASSET.'/upload/user/'.$_SESSION['user']['id'];
            $ext  = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
            $name = hash_file('sha512', $_FILES['upload']['tmp_name']).($ext ? '.'.$ext : '');
            $path = $dir.'/'.$name;

            // Ensure parent directories exist.
            if(!is_dir($dir)){
                $mask = umask(0);
                mkdir($dir, 0775, true);
                umask($mask);
            }

            // Rename and move upload.
            if(!file_exists($path)){
                if(!move_uploaded_file($_FILES['upload']['tmp_name'], $path)){
                    logger('Storage upload failed: Failed to move uploaded file.');
                    return false;
                }
            }else{
                logger('Storage upload canceled: Duplicate file exists.');
                return false;
            }

            // Storage size limits.
            #$files = $this->get_uploads();
            #$size = 0;
            #foreach($files as $file){
            #    $size += filesize(ltrim($file['path'], '/'));
            #    logger('Storage upload failed: Exceeded storage limit.');
            #    if($size > $max) return false;
            #}

            // Add path to database.
            if(!$this->set('upload', [
                'user_id' => $_SESSION['user']['id'],
                'name'    => $_FILES['upload']['name'],
                'path'    => '/'.$path,
                'privacy' => $privacy
            ])){
                logger('Storage change failed: Failed to add path to database.');
                return false;
            }

            // Done!
            logger('Successfully uploaded: '.basename($_FILES['upload']['name']).'.');
            return true;
        }
    }

    /**--------------------------------------------------------------------\
    | Rebuild the databases.                                               |
    +---------+-----------+-----------+------------------------------------+
    | @param  | string    | $db_name  | Name of the database.              |
    \---------------------------------------------------------------------*/
    protected function rebuild($db_name = ''){

        // Resolve database name.
        if(!$db_name) $db_name = $this->db_name;
        $this->db->exec("USE `".$db_name."`;");

        // Erase upload table.
        $this->db->exec("DROP TABLE IF EXISTS `upload`;");

        // Create upload table.
        $this->db->exec("
            CREATE TABLE `upload` (
            `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each user, unique index.',
            `user_id`  INT UNSIGNED COMMENT 'User ID of owner.',
            `name`     CHAR(255)    COMMENT 'Upload filename.',
            `path`     CHAR(255)    COMMENT 'Path to upload.',
            `privacy`  CHAR(255)    COMMENT 'Privacy type.',
            `password` CHAR(255)    COMMENT 'Password in a salted and hashed format.'
            ) AUTO_INCREMENT=".random_int(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Upload data.';
        ");

        // Add constraints.
        $this->db->exec("ALTER TABLE `upload` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`); ");
        $this->db->exec("ALTER TABLE `upload` ADD UNIQUE (`path`(191)); ");

        // Remove orphaned uploads.
        #$this->clean_uploads();
    }

    /**--------------------------------------------------------------------\
    | Reset the databases.                                                 |
    +---------+-----------+-----------+------------------------------------+
    | @param  | string    | $db_name  | Name of the database.              |
    \---------------------------------------------------------------------*/
    protected function reset($db_name = ''){

        // Resolve database name.
        if(!$db_name) $db_name = $this->db_name;
        $this->db->exec("USE `".$db_name."`;");

        // Wipe tableS.
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->exec("TRUNCATE TABLE `upload`;");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1;");
        #$this->db->exec("DELETE FROM `upload`;");
    }

}
