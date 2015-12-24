<?php

include('templates/default/function/display_widget.php');

if (empty($_GET['id_smartcmd'])) {
	redirect();
}

if (empty($_GET['id_scenario'])) {
	$id_scenario = 0;
}
else {
	$id_scenario = $_GET['id_scenario'];
}

$id_smartcmd = $_GET['id_smartcmd'];

$request = new Api();
$request -> add_request('confUserInstallation');
$request -> add_request('mcVisible');
$request -> add_request('searchSmartcmdById', array($id_smartcmd));
$result  =  $request -> send_request();

$listAllVisible = $result->mcVisible;

$floorallowed = $listAllVisible->ListFloor;
$roomallowed = $listAllVisible->ListRoom;
$deviceallowed = $listAllVisible->ListDevice;

$installation_info = $result->confUserInstallation;
$smartcmd_infos = $result->searchSmartcmdById;
if (empty($smartcmd_infos)) {
	redirect();
}
$name_smartcmd = $smartcmd_infos->name;
if (empty($smartcmd_infos->floor_id) || empty($smartcmd_infos->room_id)) {
	$smartcmd_infos->floor_id = 0;
	$smartcmd_infos->room_id = 0;
}

if(!empty($installation_info) && !empty($name_smartcmd) ||
	!empty($floorallowed) && !empty($roomallowed) && !empty($deviceallowed)) {
	
	$available_opt = array (
		"12", "13", "54", "96", 
		"357", "358", "359", "360", "361",
		"363", "364", "365", "366",
		"367", "368", "383", "388", "392", "393", "394",
		"400", "401", "402", "403", "404", "405", "406"
	);
	
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