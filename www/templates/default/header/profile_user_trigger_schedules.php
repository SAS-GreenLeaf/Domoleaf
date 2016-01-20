<?php 

$request = new Api();
$request -> add_request('listSchedules');
$result  =  $request -> send_request();

$schedulesList = $result->listSchedules;

echo '<title>'._('Schedules').'</title>';

?>