<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['action']) && !empty($_GET['optionid'])){
	$request =  new Api();
	$request -> add_request('mcCamera', array($_GET['iddevice'], $_GET['action'], $_GET['optionid']));
	$result  =  $request -> send_request();
}

?>