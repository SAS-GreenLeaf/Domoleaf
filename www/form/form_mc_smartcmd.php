<?php 

include('header.php');

if (!empty($_GET['smartcmd_id'])){
	$request =  new Api();
	$request -> add_request('mcSmartcmd', array($_GET['smartcmd_id']));
	$result  =  $request -> send_request();
}

?>