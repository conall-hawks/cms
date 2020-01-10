<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| User database.                                                               |
\-----------------------------------------------------------------------------*/
class User_model extends Model {

    /* @var int: Hashing algorithm factor (higher the better, costing more CPU). */
    private $hash_factor = 10;

    /* @var int: Username maximum length. */
    public $username_length_max = 64;

    /* @var int: Username minimum length. */
    public $username_length_min = 4;

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Connect to the database.
        parent::__construct();

        // Rebuild user table if it doesn't already exist.
        try{$this->db->query("SELECT 1 FROM `user` LIMIT 1");}
        catch(Exception $e){ $this->reset();}

        // Rebuild user table if it is empty.
        $sql = $this->db->query('SELECT COUNT(1) AS `count` FROM `user`;');
        $count = $sql->fetchAll();
        if(empty($count[0]['count'])) $this->reset();

        // Uncomment to rebuild manually.
        #$this->reset();
    }

    /**------------------------------------------------------------------------\
    | Check the upload directories for orphaned files and remove them.         |
    \-------------------------------------------------------------------------*/
    public function clean_uploads(){
        foreach(['avatar', 'photo'] as $key){

            /* Build path to upload directory. */
            $dir = IMAGE.'/user/'.$key;

            /* Get files in upload directory. */
            $inodes = [];
            if(is_dir($dir)){
                $iterator = new FilesystemIterator($dir, FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::SKIP_DOTS);
                foreach($iterator as $filename => $fileinfo){
                    $inodes[] = '/'.$dir.'/'.$filename;
                }

                /* Get files in database. */
                $column = trim($this->db->quote($key, PDO::PARAM_STR), "'");
                $sql = $this->db->prepare("SELECT `$column` FROM `user` WHERE `$column` IS NOT NULL;");
                $sql->execute();

                /* Remove orphaned uploads. */
                $orphans = array_diff($inodes, $sql->fetchAll(PDO::FETCH_COLUMN));
                foreach($orphans as $orphan){
                    unlink(ltrim($orphan, '/'));
                }

                /* Remove empty directories. */
                if(is_empty_dir($dir)) rmdir($dir);
            }
        }
    }

    /**------------------------------------------------------------------------\
    | Get the login information of a user.                                     |
    \-------------------------------------------------------------------------*/
    public function get_user($input, $by = 'username'){
        switch($by){
            case 'id':
                $sql = $this->db->prepare("SELECT * FROM `user` WHERE `id` = :input LIMIT 1;");
                break;
            default:
                $sql = $this->db->prepare("SELECT * FROM `user` WHERE `username` = :input LIMIT 1;");
                break;
        }
        $sql->execute([':input' => $input]);
        return $sql->fetch();
    }

    /**------------------------------------------------------------------------\
    | Update the number of failed logins. Call without second argument to      |
    | increment by 1.                                                          |
    +---------+-----------+----------------+-----------------------------------+
    | @param  | string    | $username      | Name of the user.                 |
    | @param  | string    | $failed_logins | Number of failed logins to set.   |
    | @return | boolean   |                | Result of the operation.          |
    \---------+-----------+----------------+----------------------------------*/
    public function failed_login($username, $failed_logins = NULL){

        // Validate failed_logins parameter.
        if($failed_logins !== NULL){
            if(!is_numeric($failed_logins)){
                throw new Exception('Argument #2 must be numeric.');
            }
            if($failed_logins < 0) $failed_logins = 0;
        }else{
            $failed_logins = 0;
        }

        // Locate user.
        $sql = $this->db->prepare("SELECT * FROM `user` WHERE `username` = :username LIMIT 1;");
        $sql->execute([':username' => $username]);
        $user = $sql->fetch();

        // Update failed logins.
        if($user){
            $sql = $this->db->prepare("
                UPDATE `user` SET
                    `failed_logins`          = :failed_logins,
                    `failed_login_timestamp` = :failed_login_timestamp
                WHERE `id` = :id;
            ");
            return $sql->execute([
                ':id'                     => $user['id'],
                ':failed_logins'          => $failed_logins,
                ':failed_login_timestamp' => time()
            ]);
        }

        // Fatal error; we should be able to set failed logins.
        if(ENVIRONMENT === 'development'){
            throw new Exception('Unable to set login failure.');
        }
    }

    /**------------------------------------------------------------------------\
    | Reset persistent data.                                                   |
    \-------------------------------------------------------------------------*/
    protected function reset(){

        // Create user table.
        $this->db->exec("SET FOREIGN_KEY_CHECKS=0;");
        $this->db->exec("DROP TABLE IF EXISTS `user`;");
        $this->db->exec("SET FOREIGN_KEY_CHECKS=1;");
        $this->db->exec("
            CREATE TABLE `user` (
            `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each user, unique index.',
            `username`               CHAR(255)                       COMMENT 'Username, unique.',
            `password`               CHAR(255)                       COMMENT 'Password in a salted and hashed format.',
            `email`                  CHAR(255)                       COMMENT 'Email, unique.',
            `avatar`                 CHAR(255)                       COMMENT 'Path to avatar file.',
            `photo`                  CHAR(255)                       COMMENT 'Path to photograph file.',
            `status`                 CHAR(255)                       COMMENT 'Status message.',
            `location`               CHAR(255)                       COMMENT 'Location message.',
            `active`                 TINYINT(1) UNSIGNED DEFAULT 0   COMMENT 'Activation status.',
            `activation_hash`        CHAR(255)                       COMMENT 'Email verification hash.',
            `privilege_level`        TINYINT    UNSIGNED DEFAULT 254 COMMENT 'Privilege level.',
            `pass_reset_hash`        CHAR(255)                       COMMENT 'Password reset hash.',
            `pass_reset_timestamp`   INT        UNSIGNED             COMMENT 'Timestamp of the password reset request.',
            `remember_me_token`      CHAR(255)                       COMMENT 'Remember-me cookie token.',
            `failed_logins`          INT        UNSIGNED DEFAULT 0   COMMENT 'Number of consecutive failed login attempts.',
            `failed_login_timestamp` INT        UNSIGNED             COMMENT 'UNIX timestamp of the last failed login attempt.',
            `registration_timestamp` INT        UNSIGNED             COMMENT 'UNIX timestamp of user\'s registration.',
            `ip`                     CHAR(45)                        COMMENT 'IP address.'
            ) AUTO_INCREMENT=".random_int(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ROW_FORMAT=COMPRESSED COMMENT 'User data.';
        ");

        // Add long prefix constraint.
        $this->db->exec("SET GLOBAL innodb_file_format    = `BARRACUDA`;");
        $this->db->exec("SET GLOBAL innodb_large_prefix   = `ON`;");
        $this->db->exec("SET GLOBAL innodb_file_per_table = `ON`;");
        $this->db->exec("ALTER TABLE `user` ADD UNIQUE (`username`); ");
        $this->db->exec("ALTER TABLE `user` ADD UNIQUE (`email`); ");
        $this->db->exec("ALTER TABLE `user` ADD UNIQUE (`avatar`); ");
        $this->db->exec("ALTER TABLE `user` ADD UNIQUE (`photo`); ");

        // Create a root account.
        $this->db->exec("
            INSERT INTO `user` (
                `id`,
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '1',
                'root',
                '".password_hash(ROOTPASS, PASSWORD_DEFAULT)."',
                'root@".mb_strtolower(TITLE)."',
                '1',
                '1',
                '".time()."',
                '127.0.0.1'
            );
        ");

        // Create some moderators.
        $this->db->exec("
            INSERT INTO `user` (
                `id`,
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '2',
                'mod_1',
                '".password_hash(ADMINPASS, PASSWORD_DEFAULT)."',
                'mod1@".mb_strtolower(TITLE)."',
                '1',
                '10',
                '".time()."',
                '127.0.0.1'
            );
        ");

        $this->db->exec("
            INSERT INTO `user` (
                `id`,
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '3',
                'mod_2',
                '".password_hash(ADMINPASS, PASSWORD_DEFAULT)."',
                'mod2@".mb_strtolower(TITLE)."',
                '1',
                '10',
                '".time()."',
                '127.0.0.1'
            );
        ");

        // Create a guest with the default password: 'guest'.
        $this->db->exec("
            INSERT INTO `user` (
                `id`,
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '4',
                'guest',
                '".password_hash('guest', PASSWORD_DEFAULT)."',
                'guest@".mb_strtolower(TITLE)."',
                '1',
                '254',
                '".time()."',
                '127.0.0.1'
            );
        ");

        // Reset AUTO_INCREMENT.
        $this->db->exec("ALTER TABLE `user` AUTO_INCREMENT=".random_int(32768, 65536).";");

        // Create an example account with the very first UTF-8 characters.
        $this->db->exec("
            INSERT INTO `user` (
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '".mb_convert_encoding('&#x1;&#x1;&#x1;&#x1;', 'UTF-8', 'HTML-ENTITIES')."',
                '".password_hash('pass', PASSWORD_DEFAULT)."',
                '".mb_convert_encoding('&#x1;&#x1;&#x1;&#x1;', 'UTF-8', 'HTML-ENTITIES')."@".mb_strtolower(TITLE)."',
                '0',
                '254',
                '".time()."',
                '127.0.0.1'
            );
        ");

        // Create an example account with the very last UTF-8 characters.
        $this->db->exec("
            INSERT INTO `user` (
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '".mb_convert_encoding('&#x10FFFF;&#x10FFFF;&#x10FFFF;&#x10FFFF;', 'UTF-8', 'HTML-ENTITIES')."',
                '".password_hash('pass', PASSWORD_DEFAULT)."',
                '".mb_convert_encoding('&#x10FFFF;&#x10FFFF;&#x10FFFF;&#x10FFFF;', 'UTF-8', 'HTML-ENTITIES')."@".mb_strtolower(TITLE)."',
                '0',
                '254',
                '".time()."',
                '127.0.0.1'
            );
        ");

        // Create an example account with smiley faces.
        $this->db->exec("
            INSERT INTO `user` (
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '".mb_convert_encoding('&#x1F642;&#x1F643;&#x1F642;&#x1F643;', 'UTF-8', 'HTML-ENTITIES')."',
                '".password_hash('pass', PASSWORD_DEFAULT)."',
                '".mb_convert_encoding('&#x1F642;&#x1F643;&#x1F642;&#x1F643;', 'UTF-8', 'HTML-ENTITIES')."@".mb_strtolower(TITLE)."',
                '0',
                '254',
                '".time()."',
                '127.0.0.1'
            );
        ");

        // Create an example account with some Japanese characters.
        $this->db->exec("
            INSERT INTO `user` (
                `username`,
                `password`,
                `email`,
                `active`,
                `privilege_level`,
                `registration_timestamp`,
                `ip`
            ) VALUES(
                '".mb_convert_encoding('&#x6B7B;&#x795E;&#x6B66;&#x58EB;', 'UTF-8', 'HTML-ENTITIES')."',
                '".password_hash('pass', PASSWORD_DEFAULT)."',
                '".mb_convert_encoding('&#x6B7B;&#x795E;&#x6B66;&#x58EB;', 'UTF-8', 'HTML-ENTITIES')."@".mb_strtolower(TITLE)."',
                '0',
                '254',
                '".time()."',
                '127.0.0.1'
            );
        ");
    }
}
