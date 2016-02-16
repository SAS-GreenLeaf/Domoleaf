<?php
session_start();
include('header.php');

if(!empty($_GET['iddevice'])) {
	$request =  new Api();
	$request -> add_request('popupPassword',array($_GET['iddevice'], $_GET['password']));
	$result  =  $request -> send_request();
	$_SESSION['widget'][$_GET['iddevice']] = $result->popupPassword;
	echo $result->popupPassword;
}

?>