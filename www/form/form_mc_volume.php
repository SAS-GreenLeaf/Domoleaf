<?php 

include('header.php');

if (!empty($_GET['iddevice']) && !empty($_GET['val']) && !empty($_GET['opt'])){
	$request =  new Api();
	$request -> add_request('mcAudio', array($_GET['iddevice'], $_GET['val'], $_GET['opt']));
	$result  =  $request -> send_request();
}

?>