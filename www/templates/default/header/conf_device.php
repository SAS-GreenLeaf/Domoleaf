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
	$request -> add_request('confOptionDptList', array($_GET['device']));
	$request -> add_request('confManufacturerList', array($_GET['device']));
	$request -> add_request('confMenuProtocol');
	$result  =  $request -> send_request();
	
	$floorlistroom = $result->confFloorList;
	$roomdevice = $result->confRoomDeviceList;
	$roomlist = $result->confRoomList;
	$daemonlist = $result->confDaemonList;
	$deviceall = $result->confDeviceAll;
	$listopt = $result->confOptionList;
	$listdpt = $result->confOptionDptList;
	$listoptdevice = $result->confDeviceRoomOpt;
	$manufacturerList = $result->confManufacturerList;
	$menuProtocol = $result->confMenuProtocol;
	
	$device = $roomdevice->{$_GET['device']};
	$deviceconf = $deviceall->{$device->device_id};
	
	$tabopt = array();
	
	$exceptionaddress = array(
		 72 => 1,  79 => 1, 153 => 1, 174 => 1, 355 => 1, 356 => 1, 357 => 1, 
		358 => 1, 359 => 1, 360 => 1, 361 => 1, 363 => 1, 364 => 1, 365 => 1, 
		366 => 1, 367 => 1, 368 => 1, 383 => 1, 399 => 1, 407 => 1, 425 => 1, 
		426 => 1, 427 => 1, 428 => 1, 429 => 1, 430 => 1, 431 => 1, 432 => 1, 
		433 => 1, 434 => 1, 435 => 1, 436 => 1, 437 => 1, 438 => 1, 439 => 1,
		440 => 1, 471 => 1, 472 => 1
	);
	
	if(!empty($deviceconf->protocol_option)) {
		foreach ($deviceconf->protocol_option as $option){
			$tabopt[$option->option_id] = array(
				'id' => $option->option_id,
				'name' => $option->name
			);
		}
	}
	
	echo '<title>'._('Device configuration').'</title>';
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
}

/*** Modify options name ***/
$option_overload = array(
	181 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	182 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	189 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	191 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	195 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	196 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	197 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	198 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	199 => array(
		2  => _('1 bit'),
		51 => _('8 bits'),
		73 => _('2 bytes °C')
	),
	400 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	401 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	402 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	403 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	404 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	405 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	406 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	412 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	413 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	414 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	415 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	416 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	),
	417 => array(
		2  => _('1 bit'),
		51 => _('8 bits')
	)
);

?>