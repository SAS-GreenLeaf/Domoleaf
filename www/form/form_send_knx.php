<?php

include('header.php');

if (empty($_GET['type']) or empty($_GET['addr']) or empty($_GET['daemon'])){
	exit();
}

if (empty($_GET['value'])){
	$_GET['value'] = 0;
}

if ($_GET['type'] == 2){
	$request =  new Api();
	$request -> add_request('knx_write_l', array($_GET['daemon'], $_GET['addr'], $_GET['value']));
	$result  =  $request -> send_request();
}
else if($_GET['type'] == 1){
	$request =  new Api();
	$request -> add_request('knx_write_s', array($_GET['daemon'], $_GET['addr'], $_GET['value']));
	$result  =  $request -> send_request();
}
else{
	$request =  new Api();
	$request -> add_request('knx_read', array($_GET['daemon'], $_GET['addr']));
	$result  =  $request -> send_request();
}
?>