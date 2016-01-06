<?php 

class Link {
	protected static $instance=array();
	protected $db=array();
	
	function __construct($link) {
		switch ($link) {
			case 'domoleaf':
				$dns = 'mysql:host=localhost;dbname=domoleaf';
				$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
				try {
					$this->db = new PDO($dns, DB_USER, DB_PASSWORD, $option);
				} catch (PDOException $e) {
					die();
				}
			break;
		}
	}

	static function get_link($link=NULL) {
		
		if(!self::$instance || !self::$instance[$link]) {
			self::$instance[$link] = new Link($link);
		}
	
		return self::$instance[$link]->db;
	}
}

?>