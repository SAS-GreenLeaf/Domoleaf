<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

$resRooms = '';

if (!empty($_GET['floor_id'])) {

	$request = new Api();
	$request -> add_request('mcAllowed');
	$result  =  $request -> send_request();
	
	$install_info = $result->mcAllowed;
	
	$floor_id = $_GET['floor_id'];
	$listRoom = $install_info->ListRoom;

	foreach ($listRoom as $room) {
		if ($room->floor_id == $floor_id){
			$resRooms.='<option class="option_ok" value="'.$room->room_id.'">'.$room->room_name.'</option>';
		}
	}
}

if (!empty($resRooms)){
	echo $resRooms;
}
else{
	echo '<option value="0">'._('No selectable room').'</option>';;
}

?>