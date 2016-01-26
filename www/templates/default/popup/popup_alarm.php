<?php

session_start();

include('header.php');

include('../function/display_widget.php');

if(!empty($_GET['iddevice'])) {
	$display = '';
	$request =  new Api();
	$request -> add_request('mcVisible');
	$result  =  $request -> send_request();
	
	$listAllVisible = $result->mcVisible;
	$deviceallowed = $listAllVisible->ListDevice;
	
	$device = $deviceallowed->{$_GET['iddevice']};

	echo '<div class="center">';
	
	if(empty($_SESSION['widget']) || empty($_SESSION['widget'][$_GET['iddevice']])) {
		echo 
		'<div class="alert alert-danger center" id="popup-error" role="alert" style="display: none;">'.
			_('Bad password').
		'</div>'.
		
		'<label>'._('Enter the Password').'</label>'.
		'<input type="password" id="popup-password" class="form-control" />'.
		'<button class="btn btn-greenleaf" onclick="popupPassword('.$_GET['iddevice'].')">'._('Validate').'</button>';
		
	}
	else {
		if (!empty($device->device_opt->{12})){
			$display.= '<br/>';
			$display.=display_OnOff($device, 1);
			$display.= '<div class="clearfix"></div>';
		}
		
		$display = str_replace("\n", '', $display);
		echo $display;
	} 
	
	echo '</div>';
	echo '
<div class="controls center">'.
	'<button onclick="popupLock('.$_GET['iddevice'].')" class="btn btn-danger">'.
	_('Lock').
	'&nbsp<span class="glyphicon glyphicon-remove"></span>'.
	'</button>'.
	'</div>';
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
	echo '
	<div class="controls center">'.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('Close').
			'&nbsp<span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	'</div>';
}

?>