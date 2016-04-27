<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

$resDevices = '';
$graphOptions = array(
	6, 72, 73, 79, 173, 174, 399, 407, 441
);

if (!empty($_GET['room_id'])) {

	$request = new Api();
	$request -> add_request('mcAllowed');
	$result  =  $request -> send_request();
	
	$install_info = $result->mcAllowed;
	
	$room_id = $_GET['room_id'];
	$listDevice = $install_info->ListDevice;

	foreach ($listDevice as $device) {
		if ($device->room_id == $room_id){
			foreach ($device->device_opt as $option){
				if(in_array($option->option_id, $graphOptions)) {
					$resDevices.='<option value="'.$device->room_device_id.'">'.$device->name.'</option>';
					break;
				}
			}
		}
	}
}

if (!empty($resDevices)){
	echo $resDevices;
}
else{
	echo '<option value="0">'._('No selectable device').'</option>';;
}

?>