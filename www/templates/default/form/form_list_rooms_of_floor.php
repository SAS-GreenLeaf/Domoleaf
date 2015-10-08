<?php

include('header.php');

$listRooms = '<option value="0">'._('No Room selected').'</option>';

if (!empty($_GET['floor_id'])) {

	$request = new Api();
	$request -> add_request('confUserInstallation');
	$result  =  $request -> send_request();
	
	$install_info = $result->confUserInstallation;
	
	$floor_id = $_GET['floor_id'];
	$floor = $install_info->$floor_id;
	
	foreach ($floor->room as $room) {
		$listRooms.='<option value="'.$room->room_id.'">'.$room->room_name.'</option>';
	}
}

echo $listRooms;

?>