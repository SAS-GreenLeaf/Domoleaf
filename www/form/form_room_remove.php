<?php

include('header.php');

if (!empty($_GET['roomid']) && !empty($_GET['floorid'])){
	$request =  new Api();
	$request -> add_request('confRoomRemove', array($_GET['roomid'], $_GET['floorid']));
	$result  =  $request -> send_request();
}

?>