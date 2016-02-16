<?php 

$request =  new Api();
$request -> add_request('confApplicationAll');
$request -> add_request('confDaemonList');
$request -> add_request('confFloorList');
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

$Applist      = $result->confApplicationAll;
$devfloorlist = $result->confFloorList;
$daemonlist   = $result->confDaemonList;
$menuProtocol = $result->confMenuProtocol;

?>