<?php

include('header.php');

if (!empty($_GET['trigger_name']) && !empty($_GET['smartcmd_id'])) {
	$request =  new Api();
	$request -> add_request('createNewTrigger', array($_GET['trigger_name'], $_GET['smartcmd_id']));
	$result  =  $request -> send_request();

	if (empty($result->createNewTrigger) || $result->createNewTrigger == -1) {
		echo '-1';
	}
	else {
		echo $result->createNewTrigger;
	}
}
else {
	echo '-2';
}

?>