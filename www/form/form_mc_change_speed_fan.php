<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['optionid'])){
	if (empty($_GET['value'])){
		$value = 0;
	}
	else {
		$value = $_GET['value'];
	}
	
	$request =  new Api();
	$request -> add_request('mcAction', array($_GET['iddevice'], $value, $_GET['optionid']));
	$result  =  $request -> send_request();
}

?>