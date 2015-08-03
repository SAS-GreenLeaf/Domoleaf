<?php

include('header.php');

$request =  new Api();
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

if (!empty($_GET['id']))
{
	$request =  new Api();
	$request -> add_request('confFloorRename', array($_GET['id'], $_GET['name']));
	$result  =  $request -> send_request();
}
else
{
	$request =  new Api();
	$request -> add_request('confFloorNew', array($_GET['name']));
	$result  =  $request -> send_request();
}

?>