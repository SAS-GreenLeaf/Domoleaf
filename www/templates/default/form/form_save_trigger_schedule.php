<?php

include('header.php');

if (!empty($_GET['id_schedule'])) {

	$request = new Api();
	$request -> add_request('updateSchedule',
							array($_GET['id_schedule'], $_GET['months'],
									$_GET['weekdays'], $_GET['days'],
									$_GET['hours'], $_GET['mins']));
	$result  =  $request -> send_request();
}
?>