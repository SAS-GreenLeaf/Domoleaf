<?php

include('header.php');

if (!empty($_GET['id_scenario'])) {

	$request = new Api();
	$request -> add_request('changeScenarioState', array($_GET['id_scenario'], $_GET['scenario_state']));
	$result  =  $request -> send_request();
}
?>