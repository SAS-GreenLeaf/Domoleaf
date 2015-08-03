<?php

include('header.php');


if (!empty($_GET['room']) && !empty($_GET['floor']))
{
	$request =  new Api();
	$request -> add_request('confRoomFloor', array($_GET['room'], $_GET['floor']));
	$result  =  $request -> send_request();
}

?>