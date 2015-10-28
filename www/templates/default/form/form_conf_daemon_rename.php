<?php 

include('header.php');

if (!empty($_GET['id']) && !empty($_GET['name']) && !empty($_GET['serial'])){
	$request =  new Api();
	$request -> add_request('confDaemonRename', array($_GET['id'], $_GET['name'], $_GET['serial'], $_GET['skey']));
	if(!empty($_GET['proto'])){
		$request -> add_request('confDaemonProtocol', array($_GET['id'], explode('_', $_GET['proto']), $_GET['interface_knx'], $_GET['interface_EnOcean']));
	}
	$result  =  $request -> send_request();
}

?>