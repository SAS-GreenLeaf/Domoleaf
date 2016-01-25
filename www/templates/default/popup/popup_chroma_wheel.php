<?php

include('header.php');

if (!empty($_GET['iddevice'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['iddevice']));
	$result  =  $request -> send_request();
	
	$device_opt = $result->confDeviceRoomOpt;
	
	echo '<div class="center">'.
		 '<form class="btn center padding-bottom">';
	if (!empty($device_opt->{392}->valeur) && !empty($device_opt->{393}->valeur) && !empty($device_opt->{394}->valeur)){
		echo '<input type="text" id="color" name="color" value="'.convertRGBToHexa($device_opt->{392}->valeur, $device_opt->{393}->valeur, $device_opt->{394}->valeur).'" disabled="disabled"/>';
	}
	else{
		echo '<input type="text" id="color" name="color" value="#000" disabled="disabled"/>';
	}
		echo '</form>';
		if (!empty($_GET['bg_color'])) {
			if ($_GET['bg_color'] == 1) {
				echo
				'<button class="btn btn-warning" onclick="updateBGColor(\'#eee\', '.$_GET['userid'].', 1)">'.
					_('Reset').
					'&nbsp<span class="glyphicon glyphicon-refresh"></span>'.
				'</button>';
			}
			else {
				echo
				'<button class="btn btn-warning" onclick="updateBGColor(\'#f5f5f5\', '.$_GET['userid'].', 2)">'.
					_('Reset').
					'&nbsp<span class="glyphicon glyphicon-refresh"></span>'.
				'</button>';
			}
		}
	if (!empty($device_opt->{410}->valeur)) {
		
		echo '<br/>'.
			 '<div onclick="Variation(\''.$_GET['iddevice'].'\', \'410\', -1, 1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor">'.
								'<i class="fa fa-certificate"></i>'.
							'</div>';
		if ($device_opt->{410}->valeur > 0){
			$val = ceil(($device_opt->{410}->valeur * 100) / 255);
			echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">'.
					'<output id="range-'.$_GET['iddevice'].'-popup" for="slider-value-'.$_GET['iddevice'].'-popup">'.$val.'%</output>'.
				 '</div>';
		}
		else {
			echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">'.
					'<output id="range-'.$_GET['iddevice'].'-popup" for="slider-value-'.$_GET['iddevice'].'-popup">50%</output>'.
				 '</div>';
		}
		echo '<div onclick="Variation(\''.$_GET['iddevice'].'\', \'410\', 1, 1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor">'.
				'<i class="fa fa-sun-o"></i>'.
			 '</div>';


		echo '<div class="col-md-10 col-md-offset-1 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
		if (!empty($device_opt->{410}->valeur)){
			echo '<input value="'.$device_opt->{410}->valeur.'" min="0" step="1" max="255"';
		}
		else{
			echo '<input value="128" min="0" step="1" max="255"';
		}
		echo		'oninput="outputUpdate(\''.$_GET['iddevice'].'\', value, 1)"'.
					'onchange="getVariation(\''.$_GET['iddevice'].'\', \''.$device_opt->{410}->option_id.'\', 1)"'.
					'id="slider-value-'.$_GET['iddevice'].'-popup" type="range" style="background: #FFFF66"/>'.
			 '</div>'.
			 '<div class="clearfix"></div>'.
			 '<br/>';
	}
	echo
	'</div>'.
	'<div id="colorpicker"></div>
	<div class="controls center">'.
		'<button class="btn btn-success"';
		if (empty($_GET['bg_color'])) {
			echo 'onclick="updateRGBColor('.$_GET['iddevice'].', $(\'#color\').val())"';
		}
		else if ($_GET['bg_color'] == 1) {
			echo 'onclick="updateBGColor($(\'#color\').val(), '.$_GET['userid'].', 1)"';
		}
		else {
			echo 'onclick="updateBGColor($(\'#color\').val(), '.$_GET['userid'].', 2)"';
		}
		echo '>'.
			_('Send').
			'&nbsp<span class="glyphicon glyphicon-ok"></span>'.
		'</button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('Close').
			'&nbsp<span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	'</div>'.
	
	'<script type="text/javascript">'.
		'$("#popupTitle").html("'._("Chroma Wheel").'");'.
		'$("#colorpicker").farbtastic("#color");'.
	'</script>';
}

?>