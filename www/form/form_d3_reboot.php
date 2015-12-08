<?php 

include('header.php');

if (!empty($_GET['id'])){
	$request =  new Api();
	$request -> add_request('confD3Reboot', array($_GET['id'], $_GET['opt']));
	$result  =  $request -> send_request();
}
?>