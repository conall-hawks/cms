<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Imageboard database.                                                         |
\-----------------------------------------------------------------------------*/
class Pix_model extends Model {

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Connect to the database.
        parent::__construct();

        // Rebuild tables automatically if they don't already exist.
        try{$this->db->query("SELECT 1 FROM `pix_post`  LIMIT 1");
            $this->db->query("SELECT 1 FROM `pix_board` LIMIT 1");
        }catch(Exception $e){$this->reset();}

        // Uncomment to rebuild manually.
        $this->reset();
    }

    /**------------------------------------------------------------------------\
    | Queries the database for the specified imageboard's name.                |
    +---------+-----------+-------+--------------------------------------------+
    | @param  | string    | $path | The name of an imageboard.                 |
    | @return | string[]  |       | Array of threads from imageboard.          |
    \---------+-----------+-------+-------------------------------------------*/
    public function get_board($path = ''){
        if(!$path){
            global $uri;
            $path = urldecode(array_filter(explode('/', $uri->path))[2] ?? '');
        }
        $sql = $this->db->prepare("SELECT * FROM `pix_board` WHERE `path` = :path LIMIT 1;");
        $sql->execute([':path' => $path]);
        return $sql->fetchAll()[0] ?? FALSE;
    }

    /**------------------------------------------------------------------------\
    | Queries the database for all boards.                                     |
    +---------+----------+--------+--------------------------------------------+
    | @param  | integer  | $limit | Query limit.                               |
    | @return | string[] |        | Array of boards from imageboard.           |
    \---------+----------+--------+-------------------------------------------*/
    public function get_boards($limit = 1024){
        $sql = $this->db->prepare("SELECT * FROM `pix_board` LIMIT :limit;");
        $sql->execute([':limit' => $limit]);
        return $sql->fetchAll() ?? FALSE;
    }

    /**--------------------------------------------------------------------\
    | Queries the database for the specified imageboard.                   |
    +---------+-----------+-----------+------------------------------------+
    | @param  | string    | $board    | The name of an imageboard.         |
    | @return | string[]  |           | Array of threads from imageboard.  |
    \---------+-----------+-----------+-----------------------------------*/
    public function get_post($id = ''){
        if(!$id){
            global $uri;
            $board = urldecode(array_filter(explode('/', $uri->path))[3] ?? '');
        }
        $sql = $this->db->prepare("
            SELECT * FROM `pix_post`
            WHERE `id` = :id
            LIMIT 1;
        ");

        $sql->bindValue(':id', $id);
        $sql->execute();
        return $sql->fetchAll()[0] ?? [];
    }

    /**--------------------------------------------------------------------\
    | Queries the database for the specified imageboard.                   |
    +---------+-----------+-----------+------------------------------------+
    | @param  | string    | $board    | The name of an imageboard.         |
    | @return | string[]  |           | Array of threads from imageboard.  |
    \---------+-----------+-----------+-----------------------------------*/
    public function get_replies($parent_id, $limit = 100){

        $sql = $this->db->prepare("
            SELECT * FROM `pix_post`
            WHERE `parent` = :parent_id
            LIMIT :limit;
        ");

        $sql->bindValue(':parent_id', $parent_id);
        $sql->bindValue(':limit', $limit);
        $sql->execute();
        return $sql->fetchAll();
    }

    /**------------------------------------------------------------------------\
    | Queries the database for the specified imageboard.                       |
    +---------+-----------+-----------+----------------------------------------+
    | @param  | string    | $board    | The name of an imageboard.             |
    | @return | string[]  |           | Array of threads from imageboard.      |
    \---------+-----------+-----------+---------------------------------------*/
    public function get_posts($board = '', $limit = 100){

        // Default $board argument.
        if(!$board){
            global $uri;
            $board = urldecode(array_filter(explode('/', $uri->path))[2] ?? '');
        }

        // Build query.
        if($board){
            $sql = $this->db->prepare("
                SELECT * FROM `pix_post`
                WHERE `board` = :board
                AND `parent` IS NULL
                ORDER BY `timestamp` DESC
                LIMIT :limit;
            ");
        }else{
            $sql = $this->db->prepare("
                SELECT * FROM `pix_post`
                WHERE `parent` IS NULL
                ORDER BY `timestamp` DESC
                LIMIT 100;
            ");
        }

        // Execute query; return results.
        $sql->bindValue(':board', $board);
        $sql->bindValue(':limit', $limit);
        $sql->execute();
        return $sql->fetchAll();
    }

    /**------------------------------------------------------------------------\
    | Modify a post or create one if it does not already exist.                |
    |                                                                          |
    | $post Array reference:                                                   |
    |     $post['id']           : ID of this post.                             |
    |     $post['board']        : Board this post belongs to.                  |
    |     $post['parent']       : Parent (if any) of this post.                |
    |     $post['time']         : Last modified timestamp.                     |
    |     $post['ip']           : User's IP address.                           |
    |     $post['author']       : (Supposedly) This person's name.             |
    |     $post['tripcode']     : Pseudonymous user's tripcode.                |
    |     $post['email']        : (Supposedly) This person's e-mail.           |
    |     $post['password']     : Password for modification/deletion.          |
    |     $post['title']        : Title.                                       |
    |     $post['message']      : Message.                                     |
    |     $post['file_path']    : URL to attached media.                       |
    |     $post['file_hash']    : Hash sum of attached media.                  |
    |     $post['file_name']    : Name of attached media.                      |
    |     $post['file_size']    : Size of attached media.                      |
    |     $post['image_width']  : X dimension of attached media.               |
    |     $post['image_height'] : Y dimension of attached media.               |
    |     $post['moderated']    : Post has been approved by a moderator?       |
    +---------+-----------+-----------+----------------------------------------+
    | @param  | string[]  | $post     | Array of the post.                     |
    | @return | boolean   |           | Result of database command.            |
    \---------+-----------+-----------+---------------------------------------*/
    public function update_post($post){

        // Create blank record. Caveat: Incompatible with NOT NULL fields.
        if(!isset($post['id']) || !is_numeric($post['id'])){
            $sql = $this->db->prepare("INSERT INTO `pix_post`(`id`) VALUES(NULL);");
            $sql->execute();
            $post['id'] = $this->db->lastInsertId();
        }

        /* Insert each field one-by-one. */
        if(isset($post['id']) && is_numeric($post['id'])){
            foreach($post as $key => $value){
                if($key === 'id' || $key === '' || $value === '') continue;
                $sql = $this->db->prepare("UPDATE `pix_post` SET `$key`=:value WHERE `id`=:id;");
                $sql->bindValue(':id'   , $post['id']);
                $sql->bindValue(':value', $value);
                $sql->execute();
            }
        }
        return $post['id'] ? $post['id'] : false;
    }

    /**------------------------------------------------------------------------\
    | Reset persistent data.                                                   |
    \-------------------------------------------------------------------------*/
    protected function reset(){

        // Drop imageboard tables.
        $this->db->exec("DROP TABLE IF EXISTS `pix_post`;");
        $this->db->exec("DROP TABLE IF EXISTS `pix_board`;");

        // Create board table.
        $this->db->exec("
            CREATE TABLE `pix_board` (
                `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID, unique index.',
                `title`          CHAR(255) COMMENT '\"Long\" name for title.',
                `path`           CHAR(255) COMMENT '\"Short\" name for URL.',
                `image`          CHAR(255) COMMENT 'Image associated with the board.',
                `description`    TEXT      COMMENT 'Description of board; message to users.',
                `file_types`     CHAR(255) COMMENT 'File type whitelist.',
                `min_resolution` CHAR(255) COMMENT 'Minimum resolution for images and videos.'
            ) AUTO_INCREMENT=".random_int(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ROW_FORMAT=COMPRESSED COMMENT 'Imageboard meta data.';
        ");

        // Add long prefix constraint.
        $this->db->exec("SET GLOBAL innodb_file_format    = `BARRACUDA`;");
        $this->db->exec("SET GLOBAL innodb_large_prefix   = `ON`;");
        $this->db->exec("SET GLOBAL innodb_file_per_table = `ON`;");
        $this->db->exec("ALTER TABLE `pix_board` ADD UNIQUE (`title`); ");
        $this->db->exec("ALTER TABLE `pix_board` ADD UNIQUE (`path`); ");

        /* Create some default posts. */
        $boards = [
            [
                'title'       => 'Anime',
                'path'        => 'a',
                'description' => 'Safe haven for <del>weaboos</del> anime girls.<br />'
            ], [
                'title'       => 'Random',
                'path'        => 'b',
                'description' => 'The stories and information posted here are artistic works of fiction and falsehood.<br />Only a fool would take anything posted here as fact.'
            ], [
                'title'       => 'GIFs',
                'path'        => 'gif',
                'description' => 'It\'s MOVING!',
                'file_types'  => 'gif, png'
            ], [
                'title'       => 'Investing',
                'path'        => '$',
                'description' => 'It\'s good business.'
            ], [
                'title'       => 'Music',
                'path'        => 'mu',
                'description' => 'Organized noise.',
                'file_types'  => 'gif, jpg, png, mp3'
            ], [
                'title'       => 'Professional Memeologists from the Memeological Society',
                'path'        => mb_convert_encoding('&#x1F643;', 'UTF-8', 'HTML-ENTITIES'),
                'description' => '<marquee class="red"><img src="/asset/image/fire.gif" /><img src="/asset/image/fire.gif" /><img src="/asset/image/fire.gif" />&nbsp;rEd&nbsp;hOt&nbsp;mEmEs&nbsp;<img src="/asset/image/fire.gif" /><img src="/asset/image/fire.gif" /><img src="/asset/image/fire.gif" /></marquee>'
            ], [
                'title'       => 'Television',
                'path'        => 'tv',
                'description' => 'Now in <span class="rainbow">TECHNICOLOR</span>!'
            ], [
                'title'       => 'Vectors',
                'path'        => 'svg',
                'description' => 'Vector graphics.',
                'file_types'  => 'svg'
            ], [
                'title'       => 'Video Games',
                'path'        => 'vg'
            ], [
                'title'          => 'Wallpapers',
                'path'           => 'wg',
                'description'    => 'Show me them papes boi.<br />1280x720 or higher required.',
                'min_resolution' => '1280x720'
            ]
        ];
        foreach($boards as $board){
            $this->db->exec("
                INSERT INTO `pix_board` (
                    `title`,
                    `path`,
                    `image`,
                    `description`,
                    `file_types`,
                    `min_resolution`
                ) VALUES(
                    ".$this->db->quote($board['title']).",
                    ".$this->db->quote($board['path']).",
                    ".$this->db->quote($board['image'] ?? '').",
                    ".$this->db->quote($board['description'] ?? '').",
                    ".$this->db->quote($board['file_types'] ?? '').",
                    ".$this->db->quote($board['min_resolution'] ?? '')."
                );
            ");
        }

        // Create imageboard table.
        $this->db->exec("
            CREATE TABLE `pix_post`(
                `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each post, unique index.',
                `board`        CHAR(255)                       COMMENT 'Short name of the board the post was made in.',
                `parent`       INT UNSIGNED                    COMMENT 'ID of the parent post. NULL means this post is a parent.',
                `timestamp`    INT UNSIGNED                    COMMENT 'Last modified time in UNIX format.',
                `replies`      INT UNSIGNED        DEFAULT '0' COMMENT 'Number of unique replies.',
                `ip`           CHAR(39)                        COMMENT 'IP address associated with the post.',
                `author`       CHAR(255)                       COMMENT 'Name of the post\'s author.',
                `tripcode`     CHAR(10)                        COMMENT 'Tripcode of a pseudonymous author.',
                `email`        CHAR(255)                       COMMENT 'Email of the post\'s author.',
                `password`     CHAR(255)                       COMMENT 'Password for post management & deletion.',
                `title`        CHAR(255)                       COMMENT 'Subject of the post.',
                `message`      TEXT                            COMMENT 'Message of the post.',
                `file_path`    CHAR(255)                       COMMENT 'Path to the file\'s URL.',
                `file_hash`    CHAR(64)                        COMMENT 'The file\'s hash value. Used for duplicate detection.',
                `file_name`    CHAR(255)                       COMMENT 'The file\'s original name.',
                `file_size`    INT UNSIGNED                    COMMENT 'The size of the file in bytes.',
                `image_width`  INT UNSIGNED                    COMMENT 'Width of the image in pixels.',
                `image_height` INT UNSIGNED                    COMMENT 'Height of the image in pixels.',
                `stickied`     TINYINT(1) UNSIGNED DEFAULT '0' COMMENT 'Is the post sticky?',
                `moderated`    TINYINT(1) UNSIGNED DEFAULT '0' COMMENT 'Has the post been moderated?'
            ) AUTO_INCREMENT=".random_int(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT 'Imageboard post data.';
        ");

        //$this->db->exec("ALTER TABLE `pix_post` ADD FOREIGN KEY (`board`) REFERENCES `pix_board`(`path`); ");

        // Mark a time within the last week.
        //--------+-----------+-----------+-------------+-------------+-----------+
        //        | Now       | Days      | Hours       | Minutes     | Seconds   |
        $basetime = (time() - (rand(1, 7) * rand(1, 24) * rand(1, 60) * rand(1, 60)));
        //--------+-----------+-----------+-------------+-------------+-----------+

        /* Create some default posts. */
        $this->db->exec("
            INSERT INTO `pix_post` (
                `board`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                'b',
                ".$basetime.",
                '127.0.0.1',
                'Admin',
                'Test',
                'This is a test.',
                '7d96235737dee0da57a7c85d09f74da00be9edc85b162a3fcd487b617ffde64a6fd6f7219e698c39cd75a0b5f32f436e64bf4c00e58344daac084107d1043007',
                'candy.jpg',
                '872646',
                '1920',
                '1080',
                '1'
            );
        ");

        $this->db->exec("
            INSERT INTO `pix_post` (
                `id`,
                `board`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                '1',
                'gif',
                '".time()."',
                '127.0.0.1',
                '',
                'Nyan~',
                '> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\n> Nyan~\nNyan~\n>> 1',
                '7a1259b1f4616247fb8e4d4af48269b85b1be7f519b69279ac7be744f0055cf001a0f28477629dec4eb295a764679c59756ab79e18f2d4352907427478d2739b',
                'nyan.gif',
                '464500',
                '2036',
                '1424',
                '1'
            );
        ");

        $this->db->exec("
            INSERT INTO `pix_post` (
                `board`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                'vg',
                '".time()."',
                '127.0.0.1',
                '',
                'Fallout',
                'I\'m not that kind of girl, mister. Go find a Brahmin or something.',
                'a92dfaea251f88732fa91ca5bc51d740ef9aab0ca5059b44706e55f9989541e76886df041589bd389dd55180cd679addead28642910a8a1a833540ca2eab2105',
                '1485747073968.jpg',
                '864997',
                '2048',
                '1365',
                '1'
            );
        ");
        $parent = $this->db->lastInsertId();
        $this->db->exec("
            INSERT INTO `pix_post` (
                `parent`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                '".$parent."',
                '".time()."',
                '127.0.0.1',
                '',
                'Fallout',
                'I\'m not that kind of girl, mister. Go find a Brahmin or something.',
                'a92dfaea251f88732fa91ca5bc51d740ef9aab0ca5059b44706e55f9989541e76886df041589bd389dd55180cd679addead28642910a8a1a833540ca2eab2105',
                '1485747073968.jpg',
                '864997',
                '2048',
                '1365',
                '1'
            );
        ");
        $this->db->exec("
            INSERT INTO `pix_post` (
                `parent`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                '".$parent."',
                '".time()."',
                '127.0.0.1',
                '',
                'Fallout',
                'I\'m not that kind of girl, mister. Go find a Brahmin or something.',
                'a92dfaea251f88732fa91ca5bc51d740ef9aab0ca5059b44706e55f9989541e76886df041589bd389dd55180cd679addead28642910a8a1a833540ca2eab2105',
                '1485747073968.jpg',
                '864997',
                '2048',
                '1365',
                '1'
            );
        ");
        $this->db->exec("
            INSERT INTO `pix_post` (
                `parent`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                '".$parent."',
                '".time()."',
                '127.0.0.1',
                '',
                'Fallout',
                'I\'m not that kind of girl, mister. Go find a Brahmin or something.',
                'a92dfaea251f88732fa91ca5bc51d740ef9aab0ca5059b44706e55f9989541e76886df041589bd389dd55180cd679addead28642910a8a1a833540ca2eab2105',
                '1485747073968.jpg',
                '864997',
                '2048',
                '1365',
                '1'
            );
        ");

        $this->db->exec("
            INSERT INTO `pix_post` (
                `board`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                'wg',
                '".time()."',
                '127.0.0.1',
                '',
                'I am a cat.',
                'Meow.',
                'd9787c4b5d7ee350848ea7da8e24255904c357827a04dfa6bb3cd50e3dafa596e945bc802acbc4182296d254263a3560a1e5c1443453a5ac6993040905577fda',
                'ravecat.jpg',
                '464500',
                '2036',
                '1424',
                '1'
            );
        ");

        $this->db->exec("
            INSERT INTO `pix_post` (
                `board`,
                `timestamp`,
                `ip`,
                `author`,
                `title`,
                `message`,
                `file_hash`,
                `file_name`,
                `file_size`,
                `image_width`,
                `image_height`,
                `moderated`
            ) VALUES(
                'hidden',
                '".time()."',
                '127.0.0.1',
                '',
                'I am a cat.',
                'Meow.',
                'd9787c4b5d7ee350848ea7da8e24255904c357827a04dfa6bb3cd50e3dafa596e945bc802acbc4182296d254263a3560a1e5c1443453a5ac6993040905577fda',
                'ravecat.jpg',
                '464500',
                '2036',
                '1424',
                '1'
            );
        ");

        #$this->db->exec("INSERT INTO `pix_post` (`id`, `board`, `parent`, `timestamp`, `ip`, `author`, `title`, `message`, `file`, `file_hash`, `file_name`, `file_size`, `image_width`, `image_height`, `moderated`) VALUES('24979', 'gif', '0', '1456198176', '127.0.0.1', 'Cat'  , 'I am a cat.'    , 'Meow.',                                                               '1485567176672.gif', 'f339afd6cae98b4b926621f9aadc514b', 'nyan-cat.gif',           '94384',  '500',  '198', '1');");
        #$this->db->exec("INSERT INTO `pix_post` (`id`, `board`, `parent`, `timestamp`, `ip`          , `title`, `message`, `file`, `file_hash`, `file_name`, `file_size`, `image_width`, `image_height`, `moderated`) VALUES('24980', 'w'  , '0', '1456197314', '127.0.0.1',          'Fallout'        , 'I\'m not that kind of girl, mister. Go find a Brahmin or something.', '1485747073968.jpg', 'd2cb9787c00629a34b00a4342ba2c3a2', 'fallout-4-cosplay.jpg', '865058', '2048', '1365', '1');");
    }

}
