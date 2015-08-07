<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['idoption']) && !empty($_GET['action'])){
	
	$request =  new Api();
	$request -> add_request('mcValueDef', array($_GET['iddevice'], $_GET['idoption'], $_GET['action']));
	$result  =  $request -> send_request();
	
	echo $result->mcValueDef;
}

?>