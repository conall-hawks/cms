<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Persistent data storage.                                                     |
\-----------------------------------------------------------------------------*/
abstract class Model {

    /** @var string: Type of database. */
    protected $db_type = DB_TYPE;

    /** @var string: Hostname of the database's server. */
    protected $db_host = DB_HOST;

    /** @var string: Database's password. */
    protected $db_user = DB_USER;

    /** @var string: Database's username. */
    protected $db_pass = DB_PASS;

    /** @var string: Name of the database. */
    protected $db_name = DB_NAME;

    /** @var object: Handle to the database. */
    protected $db = NULL;

    /**------------------------------------------------------------------------\
    | Startup.                                                                 |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Connect to the database.
        $this->db = $this->connect();
    }

    /**------------------------------------------------------------------------\
    | Connect to a database using PDO.                                         |
    +---------+-----------+-----------+----------------------------------------+
    | @param  | string    | $db_name  | Name of the database.                  |
    | @return | bool      |           | Status of database connection.         |
    \---------+-----------+-----------+---------------------------------------*/
    public function connect(){

        // Set the parameters and options.
        $params = $this->db_type.':host='.$this->db_host.';charset=utf8mb4';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
        ];

        // Connect to the server.
        try{
            $db = $this->db = new PDO($params, $this->db_user, $this->db_pass, $options);
        }catch(PDOException $e){
            if(ENVIRONMENT === "development") die($e);
            die("Database error.");
        }

        // Switch context to the database.
        $db->exec("CREATE DATABASE IF NOT EXISTS `".$this->db_name."` CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $db->exec('USE `'.$this->db_name.'`;');

        // Done.
        return $db;
    }

    /**------------------------------------------------------------------------\
    | Easy-mode SQL writes.                                                    |
    | If you provide an ID, it will update the existing record.                |
    |                                                                          |
    | Warning: dangerous if used improperly.                                   |
    +--------------------------------------------------------------------------+
    | Example usage (with provided ID):                                        |
    |     $this->db->set('user', [                                             |
    |         'id'    => '0',                                                  |
    |         'name'  => 'the_dude',                                           |
    |         'drink' => 'caucasian'                                           |
    |     ]);                                                                  |
    |                                                                          |
    | Will execute:                                                            |
    |     UPDATE `user` SET `name`='the_dude' WHERE `id`='0'                   |
    |     UPDATE `user` SET `drink`='caucasian' WHERE `id`='0'                 |
    +--------------------------------------------------------------------------+
    | Example usage (without ID):                                              |
    |     $this->db->set('user', [                                             |
    |         'name'  => 'jackie_treehorn',                                    |
    |         'drink' => 'scotch'                                              |
    |     ]);                                                                  |
    |                                                                          |
    | Will execute:                                                            |
    |     INSERT INTO `user`(`id`) VALUES(NULL);                               |
    |     UPDATE `user` SET `name`='jackie_treehorn' WHERE `id`='?'            |
    |     UPDATE `user` SET `drink`='scotch' WHERE `id`='?'                    |
    +---------+--------+--------+----------------------------------------------+
    | @param  | string | $input | Array of info to save into database.         |
    | @param  | string | $table | Name of the database.                        |
    | @return | void   |        |                                              |
    \---------+--------+--------+---------------------------------------------*/
    public function set($table, $input){

        // Validate parameters.
        if(!is_string($table)) throw new Exception('Expected string for 1st argument.', 1);
        if(!is_array($input))  throw new Exception('Expected array for 2nd argument.',  1);

        // Escape parameter.
        $table = trim($this->db->quote($table, PDO::PARAM_STR), "'");

        // Prepare for rollback on failure.
        $this->db->beginTransaction();

        // Create blank record. Caveat: Incompatible with NOT NULL fields.
        if(!isset($input['id']) || !is_numeric($input['id'])){
            try{
                $sql = $this->db->prepare("INSERT INTO `$table`(`id`) VALUES(NULL);");
                $sql->execute();
                $input['id'] = $this->db->lastInsertId();
            }catch(Exception $e){
                $this->db->rollback();
                logger($e);
                return false;
            }
        }

        // Insert each field one-by-one.
        if(isset($input['id']) && is_numeric($input['id'])){
            foreach($input as $key => $value){
                if($key === 'id') continue;
                try{
                    $key = trim($this->db->quote($key, PDO::PARAM_STR), "'");
                    $sql = $this->db->prepare("UPDATE `$table` SET `$key`=:value WHERE `id`=:id;");
                    $sql->bindValue(':id',    $input['id']);
                    $sql->bindValue(':value', $value);
                    $sql->execute();
                }catch(Exception $e){
                    $this->db->rollback();
                    logger($e);
                    return false;
                }
            }
        }

        // Done.
        $this->db->commit();
        return true;
    }
}
