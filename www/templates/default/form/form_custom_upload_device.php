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

$target_dir_abs = "/etc/domoleaf/www/templates/default/custom/device/";
$target_dir = "/templates/default/custom/device/";
$target_file = $target_dir_abs . basename($_FILES["fileToUpload"]["name"]);
$imageFileType = "jpg";

if (!empty($_POST['device']) && !empty($devices->$_POST['device']) && !empty($iduser)){
	$filename = $iduser.'_'.$_POST['device'].'_'.$_SERVER['REQUEST_TIME'].'.'.$imageFileType;
	$target_file = $target_dir_abs.$filename;
	if (empty($_FILES["fileToUpload"]["tmp_name"]) || empty($target_file)) {
		echo 0;
	}
	if (!(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))){
		echo 0;
	}
	$compressed = compress_image($target_file, $target_file, 90);
	if (!(rename($compressed, $target_file))){
		echo 0;
	}
	$current_device = $devices->$_POST['device'];
	if (!empty($current_device->device_bgimg)){
		unlink($target_dir_abs.$current_device->device_bgimg);
	}
	$request =  new Api();
	$request -> add_request('confUserDeviceBgimg',
							array($_POST['device'], $filename, $iduser));
	$result  =  $request -> send_request();
	$uploadOk = $target_dir.$filename;
}

echo $uploadOk;
?> 