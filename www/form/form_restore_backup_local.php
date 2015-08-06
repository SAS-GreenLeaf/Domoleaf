<?php

include('header.php');

if (empty($_GET['filename']) or !($_GET['filename'] > 0)){
	redirect();
}

$request =  new Api();
$request -> add_request('confDbRestoreLocal', array($_GET['filename']));
$result  =  $request -> send_request();

?>