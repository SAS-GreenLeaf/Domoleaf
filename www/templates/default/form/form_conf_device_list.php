<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confDeviceAll');
$result  =  $request -> send_request();

$devices = $result->confDeviceAll;


if (!empty($_GET['idapp']) && !empty($devices)){
	echo '<option value="0">'._('Choose your device').'</option>';
	foreach($devices as $elem){
		if ($elem->application_id == $_GET['idapp']){
			echo '<option value="'.$elem->device_id.'">'.$elem->name.'</option>';
		}
	}
}

?>