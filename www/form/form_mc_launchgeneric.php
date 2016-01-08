<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['optionid'])){
	$request =  new Api();
	$request -> add_request('mcAction', array($_GET['iddevice'], 0, $_GET['optionid']));
	$result  =  $request -> send_request();
}

?>