<?php 

include('header.php');

include('../function/display_widget.php');

if(!empty($_GET['iddevice'])){

	$request =  new Api();
	$request -> send_request();
	$request -> add_request('mcVisible');
	$result  =  $request -> send_request();

	$listAllVisible = $result->mcVisible;
	$deviceallowed = $listAllVisible->ListDevice;

	$device = $deviceallowed->{$_GET['iddevice']};
	
	echo '<div class="center">';
	
	$display = '';
	
	if (!empty($device->device_opt->{12})){
		$display.=display_OnOff($device);
		$display.='<br/>';
		$display.='<div class="clearfix"></div>';
	}
	
	if (!empty($device->device_opt->{442})){
		$display.='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10 col-xs-12">';
		$display.=display_varie($device, 3, 442);
		$display.='</div>';
		$display.='<div class="clearfix"></div>';
	}
	
	if (!empty($device->device_opt->{54})){
		$display.=display_UpDown($device);
		$display.='<div class="clearfix"></div>';
	}

	if (!empty($device->device_opt->{13})){
		$display.='<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10 col-xs-12">';
		$display.=display_varie($device, 2);
		$display.='</div>';
		$display.='<div class="clearfix"></div><br/>';
	}
	
	$display = str_replace("\n", '', $display);
	echo $display;

	echo '<div class="clearfix"></div><br/>';
	echo '<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
			'</div>';
}

?>