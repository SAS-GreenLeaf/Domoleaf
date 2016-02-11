<?php

if (!empty($_GET['floor'])){
	$request =  new Api();
	$request -> add_request('confFloorList');
	$request -> add_request('confRoomList', array($_GET['floor']));
	$request -> add_request('confMenuProtocol');
	$result  =  $request -> send_request();
	
	echo '<title>'._('Salle').'</title>';
	
	$floorlistroom = $result->confFloorList;
	$roomlist = $result->confRoomList;
	
	if(empty($roomlist) or empty($floorlistroom) or empty($roomlist->$_GET['room'])){
		redirect('/conf_installation');
	}
}
else {
	$request =  new Api();
	$request -> add_request('confMenuProtocol');
	$result  =  $request -> send_request();
}

$menuProtocol = $result->confMenuProtocol;

?>