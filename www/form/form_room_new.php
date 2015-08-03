<?php

include('header.php');

$request =  new Api();
$request -> add_request('confRoomList');
$result  =  $request -> send_request();


if (!empty($_GET['id']))
{
	$request =  new Api();
	$request -> add_request('confRoomRename', array($_GET['id'], $_GET['name']));
	$result  =  $request -> send_request();
}
else
{
	$request =  new Api();
	$request -> add_request('confRoomNew', array($_GET['name'], $_GET['idfloor']));
	$result  =  $request -> send_request();
}

?>