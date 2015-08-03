<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['optionid'])){
	if(empty($_GET['val'])) {
		$_GET['val'] = 0;
	}
	$request =  new Api();
	$request -> add_request('mcVarie', array($_GET['iddevice'], $_GET['val'], $_GET['optionid']));
	$result  =  $request -> send_request();
}

?>