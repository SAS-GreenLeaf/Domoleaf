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
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 445)">1</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 446)">2</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 447)">3</button>';

	echo '<br/>';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 448)">4</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 449)">5</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 450)">6</button>';

	echo '<br/>';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 451)">7</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 452)">8</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 453)">9</button>';

	echo '<br/>';
	
	echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 0)"></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 444)">0</button>';
	echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 0)"></button>';

	echo '</div>';
	echo '<div class="col-xs-4 center tv-area">';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 454)">AV</button>';
	echo '<button class="btn btn-invisible" onclick="launchGeneric('.$device_id.', 0)">XX</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 12)"><span class="glyphicon glyphicon-off"></span></button>';
	
	echo '<br/>';
	
	echo '<button class="btn btn-danger" onclick="launchGeneric('.$device_id.', 0)">&nbsp;&nbsp;&nbsp;</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 459)"><span class="glyphicon glyphicon-triangle-top"></span></button>';
	echo '<button class="btn btn-primary" onclick="launchGeneric('.$device_id.', 0)">&nbsp;&nbsp;&nbsp;</button>';

	echo '<br/>';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 461)">&nbsp;<span class="glyphicon glyphicon-triangle-left"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 470)">OK</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 462)">&nbsp;<span class="glyphicon glyphicon-triangle-right"></span></button>';

	echo '<br/>';
	
	echo '<button class="btn btn-success" onclick="launchGeneric('.$device_id.', 0)">&nbsp;&nbsp;&nbsp;</button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 460)"><span class="glyphicon glyphicon-triangle-bottom"></span></button>';
	echo '<button class="btn btn-warning" onclick="launchGeneric('.$device_id.', 0)">&nbsp;&nbsp;&nbsp;</button>';

	echo '</div>';
	echo '<div class="col-xs-4 center tv-area">';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 463)"><span class="glyphicon glyphicon-plus"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 368)"><span class="glyphicon glyphicon-volume-off"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 465)"><span class="glyphicon glyphicon-plus"></span></button>';

	echo '<br/>';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 464)"><span class="glyphicon glyphicon-minus"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 467)"><span class="glyphicon glyphicon-record"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 466)"><span class="glyphicon glyphicon-minus"></span></button>';

	echo '<br/>';
	
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 468)"><span class="glyphicon glyphicon-backward"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 363)"><span class="glyphicon glyphicon-play"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 364)"><span class="glyphicon glyphicon-pause"></span></button>';
	echo '<button class="btn btn-info" onclick="launchGeneric('.$device_id.', 469)"><span class="glyphicon glyphicon-forward"></span></button>';
	
	echo '</div>';
	echo '<div class="clearfix"></div>';
}

?>