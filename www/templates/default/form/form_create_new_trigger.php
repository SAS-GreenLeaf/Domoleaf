<?php

include('header.php');

if (!empty($_GET['trigger_name'])) {
	$request =  new Api();
	$request -> add_request('createNewTrigger', array($_GET['trigger_name']));
	$result  =  $request -> send_request();

	if (empty($result->createNewTrigger) || $result->createNewTrigger == -1) {
		echo '-1';
	}
	else {
		echo $result->createNewTrigger;
	}
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
	echo '-2';
}

?>