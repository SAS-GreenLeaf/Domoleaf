<?php 

include('header.php');

if (!empty($_GET['iddevice'])){
	
	$request =  new Api();
	$request -> add_request('mcDeviceInfo', array($_GET['iddevice']));
	$result  =  $request -> send_request();
	
	$deviceinfo = $result->mcDeviceInfo;
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
		$proto = 'https://';
	}
	else {
		$proto = 'http://';
	}
	if (!empty($deviceinfo->login)) {
		if (!empty($deviceinfo->mdp)){
			$auth = $deviceinfo->login.':'.$deviceinfo->mdp.'@';
		}
		else {
			$auth = $deviceinfo->login.'@';
		}
	}
	else {
		$auth = '';
	}
	
	$host = $_SERVER['HTTP_HOST'];
	
	if (empty($deviceinfo)){
		echo _('Unknown web page');
	}
	else {
		echo '<div class="embed-responsive embed-responsive-4by3">
			<iframe class="embed-responsive-item" src="'.$proto.$auth.$host.'/device/'.$deviceinfo->room_device_id.'"></iframe>
		</div>';
	}
	
	echo '<div class="center">
			<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>
		</div>';
	
}

?>
