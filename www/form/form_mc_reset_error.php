<?php 

include('header.php');

if (!empty($_GET['room_device_id']) && !empty($_GET['device_opt'])){
	$request =  new Api();
	$request -> add_request('mcResetError', array($_GET['room_device_id'], $_GET['device_opt']));
	$result  =  $request -> send_request();
}
?>