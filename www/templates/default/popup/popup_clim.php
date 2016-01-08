<?php 

include('header.php');

include('../function/display_widget.php');

if (!empty($_GET['iddevice'])){
	
	$request =  new Api();
	$request -> send_request();
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
	
	$display = str_replace("\n", '', $display);
	
	echo $display;
	
	//if option 'type' actived
	echo '<button onclick="" class="btn btn-info">'._('Comfort').'</button> ';
	echo '<button onclick="" class="btn btn-info">'._('Night').'</button> ';
	echo '<button onclick="" class="btn btn-info">'._('Eco').'</button> ';
	echo '<button onclick="" class="btn btn-info">'._('Frost free').'</button> ';
	echo '<div class="clearfix"></div><br/>';
	echo '<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
}

?>