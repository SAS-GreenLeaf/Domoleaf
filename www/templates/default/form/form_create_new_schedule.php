<?php

include('header.php');

if (!empty($_GET['schedule_name'])) {
	$request =  new Api();
	$request -> add_request('createNewSchedule', array($_GET['schedule_name']));
	$result  =  $request -> send_request();

	if (empty($result->createNewSchedule) || $result->createNewSchedule == -1) {
		echo '-1';
	}
	else {
		echo $result->createNewSchedule;
	}
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
	echo '-2';
}

?>