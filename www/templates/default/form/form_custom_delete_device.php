<?php 

include('header.php');

if (empty($_POST['userid'])) {
	$_POST['userid'] = 0;
}
$request =  new Api();
$request -> add_request('mcAllowed');
$request -> add_request('confUserDeviceEnable', array($_POST['userid']));
$request -> add_request('profileList');
$result  =  $request -> send_request();

$userList = $result->profileList;

if (empty($result -> confUserDeviceEnable) || sizeof($result -> confUserDeviceEnable) == 0) {
	$listAllVisible = $result->mcAllowed;
	$devices = $listAllVisible->ListDevice;
}
else {
	$devices = $result->confUserDeviceEnable;
}

$iduser = $_POST['userid'];
if (empty($iduser) || empty($userList->$iduser)) {
	$iduser = $request -> getId();
}

$target_dir = "/etc/domoleaf/www/templates/default/custom/device/";
$deleteOk = 1;

if (!empty($_POST["device"]) && !empty($devices->$_POST["device"]) && !empty($iduser)){
	
	$current_device = $devices->$_POST['device'];
	if (!empty($current_device->device_bgimg)){
		unlink($target_dir.$current_device->device_bgimg);
	}
	$request =  new Api();
	$request -> add_request('confUserDeviceBgimg', array($_POST['device'], '', $iduser));
	$result  =  $request -> send_request();
}
else {
	$deleteOk = 0;
}

echo $deleteOk;
?> 