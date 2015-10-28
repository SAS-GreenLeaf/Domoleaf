<?php

include('header.php');

if (!empty($_GET['scenario_name'])) {
	$request =  new Api();
	$request -> add_request('createNewScenario', array($_GET['scenario_name']));
	$result  =  $request -> send_request();

	if (empty($result->createNewScenario) || $result->createNewScenario == -1) {
		echo '-1';
	}
	else {
		echo $result->createNewScenario;
	}
}
else {
	echo '-2';
}

?>