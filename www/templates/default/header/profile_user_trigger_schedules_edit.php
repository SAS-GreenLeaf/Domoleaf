<?php

include('templates/default/function/display_widget.php');
include('templates/default/function/show_time_select_schedule.php');

if (empty($_GET['id_schedule'])) {
	redirect();
}

if (empty($_GET['id_scenario'])) {
	$id_scenario = 0;
}
else {
	$id_scenario = $_GET['id_scenario'];
}

$id_schedule = $_GET['id_schedule'];

$request = new Api();
$request -> add_request('searchScheduleById', array($id_schedule));
$result  =  $request->send_request();

$name_schedule = $result->searchScheduleById->schedule_name;

?>