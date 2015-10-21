<?php

include('header.php');

if (!empty($_GET['id_trigger'])) {

	$request = new Api();
	$request -> add_request('removeTrigger', array($_GET['id_trigger']));
	$result  =  $request -> send_request();
}
?>