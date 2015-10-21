<?php

include('header.php');

$listDevices ='<option value="0">'._('No Device selected').'</option>';

if (!empty($_GET['floor_id']) && !empty($_GET['room_id'])) {

	$request = new Api();
	$request -> add_request('confUserInstallation', array(''));
	$result  =  $request -> send_request();
	
	$install_info = $result->confUserInstallation;
	
	$floor_id = $_GET['floor_id'];
	$room_id = $_GET['room_id'];
	$room = $install_info->$floor_id->room->$room_id;
	
	foreach ($room->devices as $device) {
		$listDevices.='<option value="'.$device->room_device_id.'">'.$device->name.'</option>';
	}
}

echo $listDevices;

?>