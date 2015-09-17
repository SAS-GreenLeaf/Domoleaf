<?php

include('templates/default/function/display_widget.php');

if (empty($_GET['id_smartcmd'])) {
	redirect();
}

$id_smartcmd = $_GET['id_smartcmd'];

$request = new Api();
$request -> add_request('confUserInstallation',array(''));
$request -> add_request('mcVisible');
$request -> add_request('searchSmartcmdById',array($id_smartcmd));
$result  =  $request -> send_request();

$listAllVisible = $result->mcVisible;

$floorallowed = $listAllVisible->ListFloor;
$roomallowed = $listAllVisible->ListRoom;
$deviceallowed = $listAllVisible->ListDevice;

$installation_info = $result->confUserInstallation;
$name_smartcmd = $result->searchSmartcmdById;

if(!empty($installation_info) || !empty($name_smartcmd) ||
	!empty($floorallowed) || !empty($roomallowed) || !empty($deviceallowed)) {
	
	$available_opt = array ("12", "13", "54", "96", "363", "364", "365", "366",
							"367", "368", "383", "388", "392", "393", "394");
	
	foreach ($deviceallowed as $dev) {
		foreach ($dev->device_opt as $dev_opt) {
			if (!in_array($dev_opt->option_id, $available_opt)) {
				unset($dev->device_opt->{$dev_opt->option_id});
			}
		}
		$dev->device_opt = array_filter((array) $dev->device_opt);
	}
	
	foreach ($installation_info as $floor) {
		foreach ($floor->room as $room) {
			foreach ($room->devices as $device) {
				if (empty($deviceallowed->{$device->room_device_id})) {
					unset($room->devices->{$device->room_device_id});
				}
				else {
					foreach ($deviceallowed as $dev) {
						if ($dev->room_device_id == $device->room_device_id) {
							if (empty($dev->device_opt)) {
								unset($room->devices->{$device->room_device_id});
							}
						}
					}
				}
				
			}
		}
	}
	
	foreach ($installation_info as $floor) {
		foreach ($floor->room as $room) {
			if (empty($roomallowed->{$room->room_id}) || empty($room->devices)) {
				unset($floor->room->{$room->room_id});
			}
		}
	}
	
	foreach ($installation_info as $floor) {
		if (empty($floorallowed->{$floor->floor_id}) || empty($floor->room)) {
			unset($installation_info->{$floor->floor_id});
		}
	}
}

?>