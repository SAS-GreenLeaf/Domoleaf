<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

if ($request->getLevel() < 2){
	redirect();	
}

if (empty($_GET['file']) or !($_GET['file'] > 0)){
	redirect();
}

$path = '/etc/domoleaf/sql/backup/';
$file = 'domoleaf_backup_'.$_GET['file'].'.sql.tar.gz';

if (!file_exists($path.$file)){
	redirect();
}

ob_clean();
header("content-type: application/x-gzip");
header("Content-Disposition: filename=".$file);
flush();
readfile($path.$file);

?>