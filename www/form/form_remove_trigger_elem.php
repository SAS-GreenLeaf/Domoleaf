<?php

include('header.php');

if (!empty($_GET['id_trigger']) && !empty($_GET['condition_id'])) {

	$request = new Api();
	$request -> add_request('removeTriggerElem', array($_GET['id_trigger'], $_GET['condition_id']));
	$result  =  $request -> send_request();
}
?>