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

if (empty($_POST['userid'])) {
	$iduser = $request -> getId();
}
else {
	$iduser = $_POST['userid'];
}

$target_dir_abs = "/etc/domoleaf/www/templates/default/custom/device/";
$target_dir = "/templates/default/custom/device/";
$target_file = $target_dir_abs . basename($_FILES["fileToUpload"]["name"]);
$imageFileType = "jpg";
$uploadOk = 0;

if (!empty($_POST['id_elem']) && !empty($devices->$_POST['id_elem']) && !empty($iduser)){
	$filename = $iduser.'_'.$_POST['id_elem'].'_'.$_SERVER['REQUEST_TIME'].'.'.$imageFileType;
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
	$current_device = $devices->$_POST['id_elem'];
	if (!empty($current_device->device_bgimg)){
		unlink($target_dir_abs.$current_device->device_bgimg);
	}
	$request = new Api();
	$request -> add_request('confUserDeviceBgimg',
							array($_POST['id_elem'], $filename, $iduser));
	$result  =  $request -> send_request();
	$uploadOk = $target_dir.$filename;
}

echo $uploadOk;
?> 