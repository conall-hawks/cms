<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Login database.                                                              |
\-----------------------------------------------------------------------------*/
class Upload_model extends Model {

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        /* Connect to the database. */
        parent::__construct();

        /* Rebuild tables if they don't already exist. */
        try{$this->db->query('SELECT 1 FROM `upload` LIMIT 1');}
        catch(Exception $e){ $this->reset(); }

        /* Rebuild tables if they are empty. */
        $sql = $this->db->query("SELECT COUNT(1) AS `count` FROM `upload` WHERE `privacy` IN ('public', 'password');");
        $count = $sql->fetchAll();
        if(empty($count[0]['count'])) $this->reset();

        /* Manually reset the database. */
        #$this->reset();
    }

    /**------------------------------------------------------------------------\
    | Get uploaded files.                                                      |
    \-------------------------------------------------------------------------*/
    public function upload_select($id = NULL, $privacy = NULL){

        // Get by ID.
        if(is_numeric($id)){
            $sql = $this->db->prepare("
                SELECT *
                FROM  `upload`
                WHERE `id` = :id
                LIMIT 1;
            ");
            $sql->execute([':id' => $id]);
        }

        // Filter by privacy.
        else{
            switch($privacy){

                // Password protected.
                case 'password':
                    $sql = $this->db->prepare("
                        SELECT *
                        FROM     `upload`
                        WHERE    `privacy` = 'password'
                        ORDER BY `id` DESC;
                    ");
                    $sql->execute();
                    break;

                // Private; user only.
                case 'private':
                    $sql = $this->db->prepare("
                        SELECT *
                        FROM  `upload`
                        WHERE `privacy` = 'private'
                        AND   `user_id` = :id
                        ORDER BY `id` DESC;
                    ");
                    $user = load_controller('user');
                    $sql->execute([':id' => $user->id()]);
                    break;

                // Public.
                case 'public':
                    $sql = $this->db->prepare("
                        SELECT *
                        FROM     `upload`
                        WHERE    `privacy` = 'public'
                        ORDER BY `id` DESC;
                    ");
                    $sql->execute();
                    break;

                default:
                    $sql = $this->db->prepare("
                        SELECT *
                        FROM     `upload`
                        ORDER BY `id` DESC;
                    ");
                    $sql->execute();
                    break;
            }
        }

        // Done.
        return $sql->fetchAll();
    }

    /**------------------------------------------------------------------------\
    | Delete an upload.                                                        |
    \-------------------------------------------------------------------------*/
    public function upload_delete($id){

        // Query database entry.
        $sql = $this->db->prepare("SELECT * FROM `upload` WHERE `id` = :id");
        $sql->execute([':id' => $id]);
        $upload = $sql->fetchAll()[0];

        // Delete database entry.
        $sql = $this->db->prepare("DELETE FROM `upload` WHERE `id` = :id");
        $sql->execute([':id' => $id]);

        // Delete file.
        $file = ltrim($upload['path'], '/');
        if(is_file($file)) unlink($file);
        $dir = dirname($file);
        if(is_empty_dir($dir)) rmdir($dir);
    }

    /**------------------------------------------------------------------------\
    | Get the login information of a user.                                     |
    \-------------------------------------------------------------------------*/
    public function upload_update($upload){

        // Add path to database.
        if(!$this->set('upload', [
            'user_id' => $_SESSION['user']['id'],
            'name'    => $_FILES['upload']['name'],
            'path'    => '/'.$path,
            'privacy' => $privacy
        ])){
            logger('Updating '.basename($_FILES['upload']['name']).' failed in database.');
            return false;
        }

        // Done!
        logger('Updated '.basename($_FILES['upload']['name']).' in database.');
        return true;
    }

    /**--------------------------------------------------------------------\
    | Rebuild the databases.                                               |
    +---------+-----------+-----------+------------------------------------+
    | @param  | string    | $db_name  | Name of the database.              |
    \---------------------------------------------------------------------*/
    private function reset(){

        // Erase upload table.
        $this->db->exec("SET FOREIGN_KEY_CHECKS=0;");
        $this->db->exec("DROP TABLE IF EXISTS `upload`;");
        $this->db->exec("SET FOREIGN_KEY_CHECKS=1;");

        // Create upload table.
        $this->db->exec("
            CREATE TABLE `upload` (
            `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each upload, unique index.',
            `privacy`  CHAR(255)    COMMENT 'Privacy type.',
            `name`     CHAR(255)    COMMENT 'Upload filename.',
            `path`     CHAR(255)    COMMENT 'Path to upload.',
            `user_id`  INT UNSIGNED COMMENT 'User ID, if available.',
            `password` CHAR(255)    COMMENT 'Password for upload protection; in a salted and hashed format.',
            `reported` INT UNSIGNED COMMENT 'Number of times this has been flagged as bad by users.'
            ) AUTO_INCREMENT=".rand(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ROW_FORMAT=COMPRESSED COMMENT 'Upload data.';
        ");

        // Add long prefix constraint.
        $this->db->exec("SET GLOBAL innodb_file_format    = `BARRACUDA`;");
        $this->db->exec("SET GLOBAL innodb_large_prefix   = `ON`;");
        $this->db->exec("SET GLOBAL innodb_file_per_table = `ON`;");
        $this->db->exec("ALTER TABLE `upload` ADD UNIQUE (`path`); ");

        // Add constraints.
        $this->db->exec("ALTER TABLE `upload` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`); ");

        // Reset AUTO_INCREMENT.
        $this->db->exec("ALTER TABLE `user` AUTO_INCREMENT=".random_int(32768, 65536).";");

        // Seed the database with a public test file.
        $example_dir = ASSET.'/upload/public';
        $example     = $example_dir.'/'.bin2hex(random_bytes(32)).'.dat';
        if(!is_dir($example_dir)) mkdir($example_dir, 0770, true);
        file_put_contents($example, 'Hello world!'.PHP_EOL);
        $example_new = $example_dir.'/'.hash_file('sha512', $example).'.dat';
        rename($example, $example_new);
        $this->db->exec("
            INSERT INTO `upload` (
                `privacy`,
                `name`,
                `path`,
                `user_id`
            ) VALUES(
                'public',
                'test.txt',
                '".$example_new."',
                '1'
            );
        ");

        // Seed the database with a password-protected test file.
        $example_dir = ASSET.'/upload/password';
        $example     = $example_dir.'/'.bin2hex(random_bytes(32)).'.dat';
        if(!is_dir($example_dir)) mkdir($example_dir, 0770, true);
        file_put_contents($example, 'Hello world!'.PHP_EOL);
        $password    = password_hash(ROOTPASS, PASSWORD_DEFAULT);
        $example_new = $example_dir.'/'.hash('sha512', hash_file('sha512', $example).$password).'.dat';
        rename($example, $example_new);
        $this->db->exec("
            INSERT INTO `upload` (
                `privacy`,
                `name`,
                `path`,
                `user_id`,
                `password`
            ) VALUES(
                'password',
                'test.txt',
                '".$example_new."',
                '1',
                '".$password."'
            );
        ");

        // Seed the database with a private test file.
        $example_dir = ASSET.'/upload/private';
        $example     = $example_dir.'/'.bin2hex(random_bytes(32)).'.dat';
        if(!is_dir($example_dir)) mkdir($example_dir, 0770, true);
        file_put_contents($example, 'Hello world!'.PHP_EOL);
        $example_new = $example_dir.'/'.hash_file('sha512', $example).'.dat';
        rename($example, $example_new);
        $this->db->exec("
            INSERT INTO `upload` (
                `privacy`,
                `name`,
                `path`,
                `user_id`
            ) VALUES(
                'private',
                'test.txt',
                '".$example_new."',
                '1'
            );
        ");

        // Feedback.
        logger('Rebuilt database "upload".');
    }
}
