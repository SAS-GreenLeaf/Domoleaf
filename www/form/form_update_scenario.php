<?php

include('header.php');

if (!empty($_GET['id_scenario']) && !empty($_GET['elem'])) {

	$elem = $_GET['elem'];
	if(empty($_GET['id_elem'])) {
		$id_elem = 0;
	}
	else {
		$id_elem = $_GET['id_elem'];
	}
	
	$request = new Api();
	if ($elem == 1) {
		$request -> add_request('updateScenarioSmartcmd', array($_GET['id_scenario'], $id_elem));
	}
	else if ($elem == 2) {
		$request -> add_request('updateScenarioTrigger', array($_GET['id_scenario'], $id_elem));
	}
	else if ($elem == 3) {
		$request -> add_request('updateScenarioSchedule', array($_GET['id_scenario'], $id_elem));
	}
	$result  =  $request -> send_request();
}
?>