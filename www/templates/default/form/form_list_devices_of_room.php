<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

$resDevices ='<option value="0">'._('No device selected').'</option>';

if (!empty($_GET['room_id'])) {

	$request = new Api();
	$request -> add_request('mcAllowed', array(''));
	$result  =  $request -> send_request();
	
	$install_info = $result->mcAllowed;
	
	$room_id = $_GET['room_id'];
	$listDevice = $install_info->ListDevice;
	foreach ($listDevice as $device) {
		if ($device->room_id == $room_id){
			$resDevices.='<option value="'.$device->room_device_id.'">'.$device->name.'</option>';
		}
	}
}

echo $resDevices;

?>