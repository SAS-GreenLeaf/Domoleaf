<?php 

include('header.php');

$request =  new Api();
$request -> add_request('mcAllowed');
$request -> add_request('confUserDeviceEnable');
$result  =  $request -> send_request();

if (empty($result -> confUserDeviceEnable) || sizeof($result -> confUserDeviceEnable) == 0) {
	$listAllVisible = $result->mcAllowed;
	$devices = $listAllVisible->ListDevice;
}
else {
	$devices = $result->confUserDeviceEnable;
}

$target_dir = "/etc/domoleaf/www/templates/default/custom/device/";
$deleteOk = 1;

if (!empty($_POST["device"]) && !empty($devices->$_POST["device"])){
	
	$current_device = $devices->$_POST['device'];
	if (!empty($current_device->device_bgimg)){
		unlink($target_dir.$current_device->device_bgimg);
	}
	$request =  new Api();
	$request -> add_request('confUserDeviceBgimg', array($_POST['device']));
	$result  =  $request -> send_request();
}
else {
	$deleteOk = 0;
}

echo $deleteOk;
?> 