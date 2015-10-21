<?php

include('header.php');

if (!empty($_GET['id_trigger']) && !empty($_GET['room_id_device'])
	&& !empty($_GET['id_option']) && !empty($_GET['modif'])) {

	$request =  new Api();
	
	if ($_GET['modif'] == 1) {
		$request -> add_request('saveNewElemTrigger', array($_GET['id_trigger'], $_GET['id_condition'],
								$_GET['room_id_device'], $_GET['id_option'],
								$_GET['option_value'], $_GET['operator']));
	}
	else if ($_GET['modif'] == 2) {
		$request -> add_request('updateTriggerElemOptionValue', array($_GET['id_trigger'], $_GET['id_condition'],
								$_GET['option_value'], $_GET['id_option'], $_GET['operator']));
	}
	
	$result  =  $request -> send_request();
}
?>