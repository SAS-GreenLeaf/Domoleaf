<?php 

$request = new Api();
$request -> add_request('listTriggers');
$result  =  $request -> send_request();

$triggersList = $result->listTriggers;

?>