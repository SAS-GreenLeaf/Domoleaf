<?php 

include('header.php');

if (empty($_GET['iddevice'])){
	redirect();
}

$request =  new Api();
$request -> add_request('mcVisible');
$result  =  $request -> send_request();

$listAllVisible = $result->mcVisible;
$deviceallowed = $listAllVisible->ListDevice;

$device = $deviceallowed->{$_GET['iddevice']};

echo '<div class="center">'.
		'<br/>';
if (!empty($device->device_opt) && !empty($device->device_opt->{153}) && $device->device_opt->{153}->opt_value == 1){
	echo ''._('Lightning default (open circuit)').' ';
	if ($request->getLevel() > 1){
		echo
		'<button onclick="resetError('.$_GET['iddevice'].', 153)" class="btn btn-danger">'.
			'<span class="glyphicon glyphicon-remove"></span>'.
		'</button>';
	}
}
else {
	echo ''._('No default detected').'';
}
echo '<div><br/><br/>';

echo '<div class="controls center">'.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('Close').
			'<span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	 '</div>';

echo '<script type="text/javascript">'.
		'$("#popupTitle").html("'.$device->name.'");'.
		'setTimeout(function(){'.
			'$("#popupTitle").html("'.$device->name.'");'.
		'}, 200);'.
	 '</script>';

?>