<?php 

include('header.php');

include('../function/display_widget.php');

if (!empty($_GET['iddevice'])){
	
	$request =  new Api();
	$request -> add_request('mcVisible');
	$result  =  $request -> send_request();
	
	$listAllVisible = $result->mcVisible;
	$deviceallowed = $listAllVisible->ListDevice;
	
	$device = $deviceallowed->{$_GET['iddevice']};
	
	echo '<div class="center">';
	
	if (!empty($device->device_opt->{400}) || !empty($device->device_opt->{401}) || !empty($device->device_opt->{402}) ||
	!empty($device->device_opt->{403}) || !empty($device->device_opt->{404}) || !empty($device->device_opt->{405}) || !empty($device->device_opt->{406})){
		echo ''._('Fans').'<br/><br/>';
	}
	
	if (!empty($device->device_opt->{400})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 400)" class="btn btn-info">'._('0').'</button> ';
	}
	if (!empty($device->device_opt->{401})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 401)" class="btn btn-info">'._('1').'</button> ';
	}
	if (!empty($device->device_opt->{402})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 402)" class="btn btn-info">'._('2').'</button> ';
	}
	if (!empty($device->device_opt->{403})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 403)" class="btn btn-info">'._('3').'</button> ';
	}
	if (!empty($device->device_opt->{404})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 404)" class="btn btn-info">'._('4').'</button> ';
	}
	if (!empty($device->device_opt->{405})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 405)" class="btn btn-info">'._('5').'</button> ';
	}
	if (!empty($device->device_opt->{406})){
		echo '<button onclick="changeSpeedFan('.$device->room_device_id.', 1, 406)" class="btn btn-info">'._('6').'</button> ';
	}
	
	if (!empty($device->device_opt->{400}) || !empty($device->device_opt->{401}) || !empty($device->device_opt->{402}) ||
	!empty($device->device_opt->{403}) || !empty($device->device_opt->{404}) || !empty($device->device_opt->{405}) || !empty($device->device_opt->{406})){
		echo '<br/>';
	}
	
	$display = '';
	
	if (!empty($device->device_opt->{12})){
		$display.= '<br/>';
		$display.=display_OnOff($device, 1);
		$display.= '<div class="clearfix"></div>';
	}

	if (!empty($device->device_opt->{388})){
		$display.= '<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10 col-xs-12">';
		$display.=display_minusplus($device, 1);
		$display.= '<br/></div>';
		$display.= '<div class="clearfix"></div>';
	}
	
	$display = strtr($display, "\n", '');
	
	echo $display;
	
	if (!empty($device->device_opt->{412})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 412)" class="btn btn-info">'._('Comfort').'</button> ';
	}
	if (!empty($device->device_opt->{413})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 413)" class="btn btn-info">'._('Night').'</button> ';
	}
	if (!empty($device->device_opt->{414})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 414)" class="btn btn-info">'._('Eco').'</button> ';
	}
	if (!empty($device->device_opt->{415})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 415)" class="btn btn-info">'._('Frost free').'</button> ';
	}
	
	if (!empty($device->device_opt->{425})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 425)" class="btn btn-info">'._('Auto').'</button> ';
	}
	if (!empty($device->device_opt->{426})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 426)" class="btn btn-info">'._('Heat').'</button> ';
	}
	if (!empty($device->device_opt->{427})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 427)" class="btn btn-info">'._('Morning Warmup').'</button> ';
	}
	if (!empty($device->device_opt->{428})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 428)" class="btn btn-info">'._('Cool').'</button> ';
	}
	if (!empty($device->device_opt->{429})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 429)" class="btn btn-info">'._('Night Purge').'</button> ';
	}
	if (!empty($device->device_opt->{430})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 430)" class="btn btn-info">'._('Precool').'</button> ';
	}
	if (!empty($device->device_opt->{431})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 431)" class="btn btn-info">'._('Off').'</button> ';
	}
	if (!empty($device->device_opt->{432})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 432)" class="btn btn-info">'._('Test').'</button> ';
	}
	if (!empty($device->device_opt->{433})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 433)" class="btn btn-info">'._('Chauffage rapide').'</button> ';
	}
	if (!empty($device->device_opt->{434})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 434)" class="btn btn-info">'._('Ventilateur seulement').'</button> ';
	}
	if (!empty($device->device_opt->{435})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 435)" class="btn btn-info">'._('Restitution de froid').'</button> ';
	}
	if (!empty($device->device_opt->{436})){
		echo '<button onclick="launchGeneric('.$device->room_device_id.', 436)" class="btn btn-info">'._('Froid intense').'</button> ';
	}
	
	echo '<div class="clearfix"></div><br/>';
	echo '<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
}

?>