<?php

include('header.php');

if (!empty($_GET['id_schedule'])) {

	$request = new Api();
	$request -> add_request('removeSchedule', array($_GET['id_schedule']));
	$result  =  $request -> send_request();
}
?>