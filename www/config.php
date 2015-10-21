<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('DB_USER', 'domoleaf');
define('DB_PASSWORD', 'domoleaf');
define('VERSION', '0.0.0');

if(isset($_SERVER['HTTP_USER_AGENT']) &&
(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
	header('X-UA-Compatible: IE=edge');
}

?>