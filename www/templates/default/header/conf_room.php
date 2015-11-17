<?php

if (!empty($_GET['floor'])){
	$request =  new Api();
	$request -> add_request('confFloorList');
	$request -> add_request('confRoomList', array($_GET['floor']));
	$result  =  $request -> send_request();
	
	$floorlistroom = $result->confFloorList;
	$roomlist = $result->confRoomList;
	
	if(empty($roomlist) or empty($floorlistroom) or empty($roomlist->$_GET['room'])){
		redirect('/conf_installation');
	}
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
}

?>