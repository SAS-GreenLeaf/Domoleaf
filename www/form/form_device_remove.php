<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['idroom'])){
	$request =  new Api();
	$request -> add_request('confRoomDeviceRemove', array($_GET['iddevice'], $_GET['idroom']));
	$result  =  $request->send_request();
}

?>