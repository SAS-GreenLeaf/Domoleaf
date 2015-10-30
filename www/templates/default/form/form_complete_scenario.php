<?php

include('header.php');

if (!empty($_GET['id_scenario'])) {
	$request = new Api();
	$request -> add_request('completeScenario', array($_GET['id_scenario']));
	$result  =  $request -> send_request();
}

?>