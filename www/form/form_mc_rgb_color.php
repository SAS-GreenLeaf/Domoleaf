<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['value'])){
	
	$request =  new Api();
	$request -> add_request('mcRGB', array($_GET['iddevice'], $_GET['value']));
	$result  =  $request -> send_request();
}

?>