<?php

include('header.php');

$dir = "/etc/domoleaf/www/templates/default/custom/device/";

if (!empty($_GET['iduser'])){
	
	$request =  new Api();
	$request -> add_request('confUserInstallation', array($_GET['iduser']));
	$request -> add_request('profileRemove', array($_GET['iduser']));
	$result  =  $request -> send_request();
	
	$list = $result->confUserInstallation;
	if (!empty($result->profileRemove) && !empty($list)){
		
		foreach ($list as $elemFloor) {
			foreach ($elemFloor->room as $elemRoom) {
				foreach ($elemRoom->devices as $device) {
					if (!empty($device->device_bgimg)){
						unlink($dir.$device->device_bgimg);
					}
				}
			}
		}
	}
	
}



?>
