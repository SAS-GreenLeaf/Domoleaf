<?php

include('header.php');

if (!empty($_GET['id_trigger'])) {

	$request = new Api();
	$request -> add_request('changeTriggerState', array($_GET['id_trigger'], $_GET['trigger_state']));
	$result  =  $request -> send_request();
}
?>