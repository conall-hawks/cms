<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	abstract class Model{
		/* Database credentials. */
		protected $db_type = DB_TYPE;
		protected $db_host = DB_HOST;
		protected $db_user = DB_USER;
		protected $db_pass = DB_PASS;
		protected $db_name = NULL;
		
		/* Handle to the database. */
		protected $db = NULL;
		
		/* Error and status messages. */
		public $feedback = 'Nothing to see here.';
		
		/* Connect to the database. */
		public function __construct(){
			$this->set_db();
		}
		
		/*
		 * Connect to the database using PDO.
		 *
		 * @param	$db_name	string:	Name of the database.
		 * @return				bool:	Success status of database connection.
		 */
		protected function set_db($db_name = NULL){
			if($this->db != NULL && $db_name == NULL){
				return true;
			}else{
				try {
					$this->db = new PDO($this->db_type.':host='.$this->db_host.';dbname=;charset=UTF8', $this->db_user, $this->db_pass);
					// Get the database name.
					if($db_name == NULL){
						if($this->db_name == NULL){
							$this->db_name = get_class($this);
						}else{
							$db_name = $this->db_name;
						}
					}
					// If the database doesn't exist, rebuild it.
					$exists = $this->db->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '".$db_name."'");
					$exists = $exists->fetchAll();
					if(!$exists && method_exists($this, 'rebuild')) $this->rebuild();
					if(!$exists && method_exists($this, 'reset')) $this->reset();
					$this->db->exec('USE '.$db_name);
					return true;
				}catch(PDOException $e){
					$this->feedback = $e->getMessage();
				}
            }
			return false;
		}
	}