<?php 

include('header.php');

if (!empty($_GET['iddevice'])){
	
	$request =  new Api();
	$request -> send_request();
	$request -> add_request('mcVisible');
	$result  =  $request -> send_request();
	
	$listAllVisible = $result->mcVisible;
	$deviceallowed = $listAllVisible->ListDevice;
	
	$device = $deviceallowed->{$_GET['iddevice']};
		if (!empty($device->device_opt->{383})){
			echo '
				<div class="center col-xs-12"><output id="vol-popup-'.$device->room_device_id.'" for="volume-popup-'.$device->room_device_id.'">50%</output></div>'.
				'<div class="center col-xs-12">'.
					'<div class="col-xs-2 cursor" onclick="Volume(\'popup-'.$device->room_device_id.'\', \''.$device->device_opt->{383}->option_id.'\', -1)"><i class="glyphicon glyphicon-volume-down"></i></div>'.
					'<div class="col-xs-8" ><input onchange="SetVolume(\'popup-'.$device->room_device_id.'\', \''.$device->device_opt->{383}->option_id.'\')" value="50" min="0" step="1" max="100" id="volume-popup-'.$device->room_device_id.'" oninput="UpdateVol(\'popup-'.$device->room_device_id.'\', value)" type="range"></div>'.
					'<div class="col-xs-2 cursor" onclick="Volume(\'popup-'.$device->room_device_id.'\', \''.$device->device_opt->{383}->option_id.'\', 1)"><i class="glyphicon glyphicon-volume-up"></i></div>'.
				'</div>'.
			 '<div class="clearfix"></div>&nbsp;';
		}
		echo	'<div class="center">'.
			 '<div class="btn-group">';
		if (!empty($device->device_opt->{367})){
			echo '<button onclick="RemoteAudio(\'prev\', \''.$device->room_device_id.'\', \''.$device->device_opt->{367}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-backward"></span></button>';
		}
		if (!empty($device->device_opt->{364})){
			echo '<button onclick="RemoteAudio(\'pause\', \''.$device->room_device_id.'\', \''.$device->device_opt->{364}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-pause"></span></button>';
		}
		if (!empty($device->device_opt->{363})){
		 echo '<button onclick="RemoteAudio(\'play\', \''.$device->room_device_id.'\', \''.$device->device_opt->{363}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-play"></span></button>';
		}
		if (!empty($device->device_opt->{365})){
			echo '<button onclick="RemoteAudio(\'stop\', \''.$device->room_device_id.'\', \''.$device->device_opt->{365}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-stop"></span></button>';
		}
		if (!empty($device->device_opt->{368})){
			echo '<button onclick="RemoteAudio(\'mute\', \''.$device->room_device_id.'\', \''.$device->device_opt->{368}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-volume-off"></span></button>';
		}
		if (!empty($device->device_opt->{366})){
			echo '<button onclick="RemoteAudio(\'next\', \''.$device->room_device_id.'\', \''.$device->device_opt->{366}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-forward"></span></button>';
		}
	 echo '</div>'.
		'</div>
		<div class="center">'.
			'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
}

?>