<?php

include('header.php');

if (!empty($_GET['iddevice'])) {
	$request =  new Api();
	$request -> send_request();
	$request -> add_request('mcVisible');
	$result  =  $request -> send_request();
	
	$listAllVisible = $result->mcVisible;
	$deviceallowed = $listAllVisible->ListDevice;
	
	$device = $deviceallowed->{$_GET['iddevice']};
	
	$device_id = $device->room_device_id;
	
	echo '<div class="col-xs-4 center tv-area">';
	
	if (!empty($device->device_opt->{445}) || !empty($device->device_opt->{446}) || !empty($device->device_opt->{447})) {
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 445)">1</button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 446)">2</button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 447)">3</button>';
		echo '<br/>';
	}
	if (!empty($device->device_opt->{448}) || !empty($device->device_opt->{449}) || !empty($device->device_opt->{450})) {
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 448)">4</button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 449)">5</button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 450)">6</button>';
		echo '<br/>';
	}
	if (!empty($device->device_opt->{451}) || !empty($device->device_opt->{452}) || !empty($device->device_opt->{453})) {
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 451)">7</button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 452)">8</button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 453)">9</button>';
		echo '<br/>';
	}
	
	if (!empty($device->device_opt->{444})){
		echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 0)"></button>';
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 444)">0</button>';
		echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 0)"></button>';
	}
	
	echo '</div>';
	echo '<div class="col-xs-4 center tv-area">';
	
	if (!empty($device->device_opt->{454}) || !empty($device->device_opt->{12})){
		if (!empty($device->device_opt->{454})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 454)">AV</button>';
		}
		if (!empty($device->device_opt->{454}) && !empty($device->device_opt->{12})){
			echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 0)">XX</button>';
		}
		if (!empty($device->device_opt->{12})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 12)"><span class="glyphicon glyphicon-off"></span></button>';
		}
		echo '<br/>';
	}
	
	if (!empty($device->device_opt->{455}) || !empty($device->device_opt->{459}) || !empty($device->device_opt->{457})) {
		if (!empty($device->device_opt->{455})){
			echo '<button class="btn btn-danger" onclick="launchGeneric('.$device_id.', 455)">&nbsp;&nbsp;&nbsp;</button>';
		}
		else {
			echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 455)">&nbsp;&nbsp;&nbsp;</button>';
		}
		if (!empty($device->device_opt->{459})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 459)"><span class="glyphicon glyphicon-triangle-top"></span></button>';
		}
		if (!empty($device->device_opt->{457})){
			echo '<button class="btn btn-primary" onclick="launchGeneric('.$device_id.', 457)">&nbsp;&nbsp;&nbsp;</button>';
		}
		else {
			echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 457)">&nbsp;&nbsp;&nbsp;</button>';
		}
		echo '<br/>';
	}
	
	if (!empty($device->device_opt->{461}) || !empty($device->device_opt->{470}) || !empty($device->device_opt->{462})) {
		if (!empty($device->device_opt->{461})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 461)">&nbsp;<span class="glyphicon glyphicon-triangle-left"></span></button>';
		}
		if (!empty($device->device_opt->{470})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 470)">OK</button>';
		}
		if (!empty($device->device_opt->{462})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 462)">&nbsp;<span class="glyphicon glyphicon-triangle-right"></span></button>';
		}
		echo '<br/>';
	}
	
	if (!empty($device->device_opt->{456}) || !empty($device->device_opt->{460}) || !empty($device->device_opt->{458})) {
		if (!empty($device->device_opt->{456})){
			echo '<button class="btn btn-success" onclick="launchGeneric('.$device_id.', 456)">&nbsp;&nbsp;&nbsp;</button>';
		}
		else {
			echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 456)">&nbsp;&nbsp;&nbsp;</button>';
		}
		if (!empty($device->device_opt->{460})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 460)"><span class="glyphicon glyphicon-triangle-bottom"></span></button>';
		}
		if (!empty($device->device_opt->{458})){
			echo '<button class="btn btn-warning" onclick="launchGeneric('.$device_id.', 458)">&nbsp;&nbsp;&nbsp;</button>';
		}
		else {
			echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 458)">&nbsp;&nbsp;&nbsp;</button>';
		}
	}
	
	echo '</div>';
	echo '<div class="col-xs-4 center tv-area">';
	
	if (!empty($device->device_opt->{463}) || !empty($device->device_opt->{368}) || !empty($device->device_opt->{465})) {
		if (!empty($device->device_opt->{463})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 463)"><span class="glyphicon glyphicon-plus"></span></button>';
		}
		if (!empty($device->device_opt->{368})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 368)"><span class="glyphicon glyphicon-volume-off"></span></button>';
		}
		if (!empty($device->device_opt->{465})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 465)"><span class="glyphicon glyphicon-plus"></span></button>';
		}
		echo '<br/>';
	}
	
	if (!empty($device->device_opt->{464}) || !empty($device->device_opt->{467}) || !empty($device->device_opt->{466})) {
		if (!empty($device->device_opt->{464})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 464)"><span class="glyphicon glyphicon-minus"></span></button>';
		}
		if (!empty($device->device_opt->{467})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 467)"><span class="glyphicon glyphicon-record"></span></button>';
		}
		if (!empty($device->device_opt->{466})){
			echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 466)"><span class="glyphicon glyphicon-minus"></span></button>';
		}
		echo '<br/>';
	}
	if (!empty($device->device_opt->{468})){
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 468)"><span class="glyphicon glyphicon-backward"></span></button>';
	}
	if (!empty($device->device_opt->{363})){
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 363)"><span class="glyphicon glyphicon-play"></span></button>';
	}
	if (!empty($device->device_opt->{364})){
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 364)"><span class="glyphicon glyphicon-pause"></span></button>';
	}
	if (!empty($device->device_opt->{469})){
		echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 469)"><span class="glyphicon glyphicon-forward"></span></button>';
	}
	echo '</div>';
	echo '<div class="clearfix"></div>';
}

?>