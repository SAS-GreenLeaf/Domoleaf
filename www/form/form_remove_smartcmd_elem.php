<?php

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['id_exec'])) {

	$request = new Api();
	$request -> add_request('removeSmartcmdElem', array($_GET['id_smartcmd'], $_GET['id_exec']));
	$result  =  $request -> send_request();
}
?>