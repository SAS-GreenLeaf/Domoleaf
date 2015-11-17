<?php 

$request =  new Api();
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

$floorlist = $result->confFloorList;

?>