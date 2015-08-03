<?php 

include('header.php');

if (!empty($_GET['name']) && !empty($_GET['serial']) && !empty($_GET['skey'])){
	$request =  new Api();
	$request -> add_request('confDaemonNew', array($_GET['name'], $_GET['serial'], $_GET['skey']));
	$result  =  $request -> send_request();
}

?>