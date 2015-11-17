<?php

include('header.php');

if (!empty($_GET['id_smartcmd'])) {

	$request = new Api();
	$request -> add_request('removeSmartcmd', array($_GET['id_smartcmd']));
	$result  =  $request -> send_request();
}
?>