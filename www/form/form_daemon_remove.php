<?php 

include('header.php');

if (!empty($_GET['id'])){
	$request =  new Api();
	$request -> add_request('confDaemonRemove', array($_GET['id']));
	$result  =  $request -> send_request();
}
?>