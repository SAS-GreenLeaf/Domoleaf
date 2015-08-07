<?php 
echo '<title>'._('Master Command').'</title>';

$request =  new Api();
$request -> send_request();
$request -> add_request('mcDeviceAll');
$request -> add_request('mcVisible');
$request -> add_request('confApplicationAll');
$result  =  $request -> send_request();

$listAllVisible = $result->mcVisible;

$deviceallowed = $listAllVisible->ListDevice;
$roomallowed = $listAllVisible->ListRoom;
$floorallowed = $listAllVisible->ListFloor;
$allapp =  $listAllVisible->ListApp;

$alldevice = $result->mcDeviceAll;
$app = $result->confApplicationAll;

$icons = array(
			1 => 'fa fa-lightbulb-o',
			2 => 'fi flaticon-heating1',
			3 => 'fa fa-bars',
			4 => 'fa fa-bolt',
			5 => 'fi flaticon-snowflake149',
			6 => 'fa fa-volume-up',
			8 => 'fa fa-tree',
			9 => 'fi flaticon-winds4',
			10 => 'fa fa-fire',
			11 => 'fi flaticon-wind34',
			12 => 'fi flaticon-person206',
			13 => 'fa fa-video-camera',
			14 => 'fi flaticon-sign35',
			15 => 'fa fa-sort-amount-asc rotate--90',
			17 => 'fa fa-tachometer'
	);
<<<<<<< HEAD
	
=======

>>>>>>> 0291d28... added new templates + added features to upload, delete, update the background image
?>