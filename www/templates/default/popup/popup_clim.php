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
	
	$display = '<div class="center">';
	if (!empty($device->device_opt->{12})){
		$display.=display_OnOff($device, 1);
	}
	if (!empty($device->device_opt->{388})){
		$display.=display_minusplus($device, 1);
	}
	
	$display.= '</div>';
	
	$display = str_replace("\n", '', $display);
	
	echo $display;

	echo '<div class="center">';
	
	//if option 'type' actived
	echo '<button onclick="" class="btn btn-info">'._('Confort').'</button> ';
	echo '<button onclick="" class="btn btn-info">'._('Nuit').'</button> ';
	echo '<button onclick="" class="btn btn-info">'._('Eco').'</button> ';
	echo '<button onclick="" class="btn btn-info">'._('Hors Gel').'</button> ';
	
	//if fan > 0
	echo '<br/><br/><b>'._('Fans').'</b><br/>';
	
	//if 1 fan
	echo '<button onclick="" class="btn btn-info">'._('1').'</button> ';
	//if 2 fans
	echo '<button onclick="" class="btn btn-info">'._('2').'</button> ';
	//if 3 fan
	echo '<button onclick="" class="btn btn-info">'._('3').'</button> ';
	//if 4 fan
	echo '<button onclick="" class="btn btn-info">'._('4').'</button> ';
	//if x fans...
		
	 echo '</div>'.
		'</div>
		<div class="center">'.
			'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
}

?>