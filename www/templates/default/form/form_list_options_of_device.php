<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

$resOptions = '';

if (!empty($_GET['room_device_id'])) {

	$request = new Api();
	$request -> add_request('mcAllowed');
	$result  =  $request -> send_request();
	
	$install_info = $result->mcAllowed;
	
	$room_device_id = $_GET['room_device_id'];
	$listDevice = $install_info->ListDevice;

	foreach ($listDevice as $device) {
		if ($device->room_device_id == $room_device_id){
			foreach ($device->device_opt as $option){
				$resOptions.='<option value="'.$option->option_id.'">'.$option->name.'</option>';
			}
		}
	}
}

if (!empty($resOptions)){
	echo $resOptions;
}
else{
	echo '<option value="0">'._('No selectable option').'</option>';;
}

?>