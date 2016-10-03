<?php 

/**
 * Manage Database connection
 * @author virgil
 */
class Link {
	protected static $instance=array();/*!< Access to the DB */
	protected $db=array();/*!< Store the DB connection */
	
	/**
	 * Build the DB link
	 * @param string $link DB code name
	 */
	function __construct($link) {
		switch ($link) {
			case 'domoleaf':
				$dns = 'mysql:host=localhost;dbname=domoleaf';
				$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
				try {
					$this->db = new PDO($dns, DB_USER, DB_PASSWORD, $option);
				} catch (PDOException $e) {
					if(file_exists('templates/default/include/nosql.php')) {
						include('templates/default/include/nosql.php');
					}
					die();
				}
			break;
		}
	}
	
	/**
	 * Get the link to the DB
	 * @param string $link DB code name
	 * @return DB link
	 */
	static function get_link($link=NULL) {
		if(!self::$instance || !self::$instance[$link]) {
			self::$instance[$link] = new Link($link);
		}
		return self::$instance[$link]->db;
	}
}

?>