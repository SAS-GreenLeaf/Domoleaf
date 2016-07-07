<?php 

include('header.php');

if (!empty($_GET['iddevice'])){
	
	$request =  new Api();
	$request -> add_request('mcDeviceInfo', array($_GET['iddevice']));
	$result  =  $request -> send_request();
	
	$deviceinfo = $result->mcDeviceInfo;
	
	if (!empty($deviceinfo) && $deviceinfo->port == 443){
		$proto = 'https://';
		$port = '';
	}
	else {
		$proto = 'http://';
		if (!empty($deviceinfo) && $deviceinfo->port == 80){
			$port = '';
		}
		else {
			$port = ':'.$deviceinfo->port;
		}
	}
	
	if (empty($deviceinfo) || empty($deviceinfo->addr)) {
		echo _('Unknown web page');
	}
	else {
		$host = $deviceinfo->addr;
		
		if(substr($host, 0, 8) == 'https://') {
			$host = substr($host, 8);
		}
		elseif(substr($host, 0, 7) == 'http://') {
			$host = substr($host, 7);
		}
		
		echo '<div class="embed-responsive embed-responsive-4by3">
			<iframe class="embed-responsive-item" src="'.$proto.$host.$port.'"></iframe>
		</div>';
	}
	
	echo '<div class="center">
			<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>
		</div>';
	
}

?>
