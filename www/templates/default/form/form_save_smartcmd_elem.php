<?php

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['room_id_device'])
	&& !empty($_GET['id_option']) && !empty($_GET['room_id_device'])
	&& !empty($_GET['modif'])) {

	$request =  new Api();
	
	if ($_GET['modif'] == 1) {
		$request -> add_request('saveNewElemSmartcmd', array($_GET['id_smartcmd'], $_GET['id_exec'],
								$_GET['room_id_device'], $_GET['id_option'],
								$_GET['option_value'], $_GET['time_lapse']));
	}
	else if ($_GET['modif'] == 2) {
		$request -> add_request('updateSmartcmdElemOptionValue', array($_GET['id_smartcmd'], $_GET['id_exec'],
								$_GET['option_value'], $_GET['id_option']));
	}
	
	$result  =  $request -> send_request();
}
?>