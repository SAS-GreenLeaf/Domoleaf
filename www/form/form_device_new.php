<?php 

include('header.php');

if (!empty($_GET['ipaddr'])){
	$addr = gethostbyname($_GET['ipaddr']);
	
	if (!(filter_var($addr, FILTER_VALIDATE_IP))){
		echo '1';
		return;
	}
	$request =  new Api();
	$request ->add_request('confRoomList');
	$request -> add_request('confDeviceNewIp', array($_GET['name'], $_GET['proto'], 
	                                                 $_GET['room'], $_GET['device'], 
	                                                 $_GET['ipaddr'], $_GET['port'], 
	                                                 $_GET['login'], $_GET['pass'],
													 $_GET['macaddr']));
	$result  =  $request -> send_request();
	$listroom = $result->confRoomList;
	$room_device_id = $result->confDeviceNewIp; 
	echo $listroom->{$_GET['room']}->floor.'/'.$_GET['room'].'/'.$room_device_id;
}
else if (!empty($_GET['knxaddr'])){
	$request =  new Api();
	$request ->add_request('confRoomList');
	$request -> add_request('confDeviceNewKnx', array($_GET['name'], $_GET['proto'], 
	                                                  $_GET['room'], $_GET['device'], 
	                                                  $_GET['knxaddr'], $_GET['daemon']));
	$result  =  $request -> send_request();
	$listroom = $result->confRoomList;
	$room_device_id = $result->confDeviceNewKnx;
	echo $listroom->{$_GET['room']}->floor.'/'.$_GET['room'].'/'.$room_device_id;
}
else if (!empty($_GET['enoceanaddr'])){
	$request =  new Api();
	$request ->add_request('confRoomList');
	$request -> add_request('confDeviceNewEnocean', array($_GET['name'], $_GET['proto'], 
	                                                      $_GET['room'], $_GET['device'], 
	                                                      $_GET['enoceanaddr'], $_GET['daemon']));
	$result  =  $request -> send_request();
	$listroom = $result->confRoomList;
	$room_device_id = $result->confDeviceNewEnocean;
	echo $listroom->{$_GET['room']}->floor.'/'.$_GET['room'].'/'.$room_device_id;
}

?>