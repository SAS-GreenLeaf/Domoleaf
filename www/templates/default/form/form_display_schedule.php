<?php

include('header.php');
include('../function/show_time_select_schedule.php');

if (!empty($_GET['schedule_id'])) {
	$id_schedule = $_GET['schedule_id'];
	
	$request = new Api();
	$request -> add_request('getSchedule', array($id_schedule));
	$result  =  $request->send_request();
	
	$schedule_infos = $result->getSchedule;
	
	$months = $schedule_infos->months;
	$months = str_split(sprintf("%'.012s\n", decbin($months)));
	
	$weekdays = $schedule_infos->weekdays;
	$weekdays = str_split(sprintf("%'.07s\n", decbin($weekdays)));
	$sunday = array_shift($weekdays);
	array_pop($weekdays);
	array_push($weekdays, $sunday);
	
	$days = $schedule_infos->days;
	$days = str_split(sprintf("%'.031s\n", decbin($days)));
	
	$hours = $schedule_infos->hours;
	$hours = str_split(sprintf("%'.024s\n", decbin($hours)));
	
	$mins = $schedule_infos->mins;
	$mins = str_split($mins);
	
	echo showtimeSelect($months, $weekdays, $days, $hours, $mins);
}
else {
	echo '';
}
?>