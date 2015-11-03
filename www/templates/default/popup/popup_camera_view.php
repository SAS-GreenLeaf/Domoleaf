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
		echo 'no camera';
	}
	else {
		if (!empty($deviceinfo->device_opt->{'356'}) && !empty($deviceinfo->device_opt->{'356'}->addr)){
			$opt = $deviceinfo->device_opt->{'356'};
			if ($opt->addr[0] != '/'){
				$opt->addr = '/'.$opt->addr;
			}
			echo '<div class="carousel thumbnail">';
			if (!empty($deviceinfo->device_opt->{'357'})){
			echo '<a id="cmd-camera-top" class="up carousel-control-camera cursor">'.
					'<span class="glyphicon glyphicon-chevron-up" aria-hidden="true" onclick="cmd_camera('.$_GET['iddevice'].', \'top\', \''.$deviceinfo->device_opt->{'357'}->option_id.'\')"></span>'.
				'</a>';
			}
			
			/*
			if (!empty($info->device_opt->{363})){
				$display.='<button onclick="RemoteAudio(\'play\', \''.$info->room_device_id.'\', \''.$info->device_opt->{363}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-play"></span></button>';
			}
			*/
			
			if (!empty($deviceinfo->device_opt->{'358'})){
			echo '<a id="cmd-camera-bottom" class="down carousel-control-camera cursor">'.
						'<span class="glyphicon glyphicon-chevron-down" aria-hidden="true" onclick="cmd_camera('.$_GET['iddevice'].', \'bottom\', \''.$deviceinfo->device_opt->{'358'}->option_id.'\')"></span>'.
				'</a>';
			}
			if (!empty($deviceinfo->device_opt->{'359'})){
			 echo '<a id="cmd-camera-left" class="left carousel-control-camera cursor">'.
					'<span style="z-index:500;" class="glyphicon glyphicon-chevron-left" aria-hidden="true" onclick="cmd_camera('.$_GET['iddevice'].', \'left\', \''.$deviceinfo->device_opt->{'359'}->option_id.'\')"></span>'.
				  '</a>';
			}
			if (!empty($deviceinfo->device_opt->{'360'})){
			 echo '<a id="cmd-camera-right" class="right carousel-control-camera cursor">'.
					'<span style="z-index:500;" class="glyphicon glyphicon-chevron-right" aria-hidden="true" onclick="cmd_camera('.$_GET['iddevice'].', \'right\', \''.$deviceinfo->device_opt->{'360'}->option_id.'\')"></span>'.
				  '</a>';
			}
			
		 echo '<img id="cmd-camera-display" class="media-object" src="'.$proto.$auth.$host.'/camera/'.$deviceinfo->room_device_id.$opt->addr.'">'.
			'</div>';
			 if (!empty($deviceinfo->device_opt->{'361'})){
			 	echo '<div class="center"><button class="btn btn-info" type="button" onclick="cmd_camera('.$_GET['iddevice'].', \'home\', \''.$deviceinfo->device_opt->{'361'}->option_id.'\')"><span class="glyphicon glyphicon-home"></span></button></div>';
			 }
		}
	 	else if (!empty($deviceinfo->device_opt->{'355'}) && !empty($deviceinfo->device_opt->{'355'}->addr)){
	 		$opt = $deviceinfo->device_opt->{'355'};
	 		if ($opt->addr[0] != '/'){
	 			$opt->addr = '/'.$opt->addr;
	 		}
			echo '<div class="thumbnail">'.
					'<img class="media-object" id="refresh" src="'.$proto.$auth.$host.'/camera/'.$deviceinfo->room_device_id.$opt->addr.'">'.
				'</div>'.
				'<script type="text/javascript">'.
					'var src= $("#refresh").attr("src");'.
					'setInterval(function() { $("#refresh").attr("src", src+"?date="+Date.now()); }, 1000);'.
				'</script>';
		}
		else {
			echo '
					<div class="alert alert-danger" role="alert">'.
						  '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'.
						  '<span class="sr-only">'._('Error:').'</span> '._('No compatible stream available').'</div>';
		}
		
	}
	
	echo '<div class="center">
			<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>
		</div>';
	
}

?>