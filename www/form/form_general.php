<?php 

include('header.php');

if (!empty($_GET['httpval']) && !empty($_GET['httpsval'])){
	if (empty($_GET['securemode'])){
		$_GET['securemode'] = 0;
	}
	if (empty($_GET['httpval'])){
		$_GET['httpval'] = 0;
	}
	if (empty($_GET['httpsval'])){
		$_GET['httpsval'] = 0;
	}	
	
	$request =  new Api();
	$request -> add_request('confRemote', array($_GET['httpval'], $_GET['httpsval'], $_GET['securemode']));
	$result  =  $request -> send_request();
}

?>