<?php 

if (!empty($_GET['room']) && !empty($_GET['floor']) && !empty($_GET['device'])){
	
	$request =  new Api();
	$request -> add_request('confFloorList');
	$request -> add_request('confRoomList', array($_GET['floor']));
	$request -> add_request('confRoomDeviceList', array(''));
	$request -> add_request('confDaemonList');
	$request -> add_request('confDeviceRoomOpt', array($_GET['device']));
	$request -> add_request('confDeviceAll');
	$request -> add_request('confOptionList');
	$request -> add_request('confOptionDptList');
	$result  =  $request -> send_request();
	
	$floorlistroom = $result->confFloorList;
	$roomdevice = $result->confRoomDeviceList;
	$roomlist = $result->confRoomList;
	$daemonlist = $result->confDaemonList;
	$deviceall = $result->confDeviceAll;
	$listopt = $result->confOptionList;
	$listdpt = $result->confOptionDptList;
	$listoptdevice = $result->confDeviceRoomOpt;
	
	$device = $roomdevice->$_GET['device'];
	$deviceconf = $deviceall->{$device->device_id};
	
	$tabopt = array();
	
	$exceptionaddress = array(72 => 1, 79 => 1, 355 => 1, 356 => 1, 357 => 1, 358 => 1, 359 => 1, 360 => 1, 361 => 1, 
							  363 => 1, 364 => 1, 365 => 1, 366 => 1, 367 => 1, 368 => 1, 383 => 1, 399 => 1);

	if (!empty($deviceconf->protocol_option->{0})){
		foreach ($deviceconf->protocol_option->{0} as $option){
			$tabopt[$option->option_id] = array(
				'id' => $option->option_id,
				'name' => $option->name
			);
		}
	}
	else if (is_array($deviceconf->protocol_option) && !empty($deviceconf->protocol_option[0])){
		foreach ($deviceconf->protocol_option[0] as $option){
			$tabopt[$option->option_id] = array(
				'id' => $option->option_id,
				'name' => $option->name
			);
		}
	}
	
	if(!empty($deviceconf->protocol_option->{$device->protocol_id})) {
		foreach ($deviceconf->protocol_option->{$device->protocol_id} as $option){
			$tabopt[$option->option_id] = array(
				'id' => $option->option_id,
				'name' => $option->name
			);
		}
	}
}

?>