<?php

include('header.php');

if (!empty($_GET['id'])) {
	$request =  new Api();
	$request -> add_request('confFloorRename', array($_GET['id'], $_GET['namefloor']));
	$result  =  $request -> send_request();
}
else {
	$request =  new Api();
	$request -> add_request('confFloorNew', array($_GET['namefloor'], $_GET['nameroom']));
	$result  =  $request -> send_request();
}

?>