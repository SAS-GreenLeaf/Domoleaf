<?php 

class Link {
	protected static $instance=array();
	protected $db=array();
	
	function __construct($link) {
		switch ($link) {
			case 'mastercommand':
				$dns = 'mysql:host=localhost;dbname=mastercommand';
				$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
				$this->db = new PDO($dns, DB_USER, DB_PASSWORD, $option);
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