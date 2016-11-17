<?php 

// No echo here !!!

function getIcon($iddevice = 1){
	$icons = array(
			2  => 'fa fa-video-camera',
			4  => 'fa fa-lightbulb-o',
			5  => 'fa fa-tachometer',
			7  => 'fi flaticon-heating1',
			9  => 'fa fa-plus-square-o',
			10 => 'fa fa-bars',
			11 => 'fa fa-bars',
			15 => 'fa fa-volume-up',
			16 => 'fa fa-television',
			17 => 'fa fa-volume-up',
			18 => 'fa fa-tree',
			20 => 'fa fa-fire',
			22 => 'fi flaticon-engineering',
			23 => 'fi flaticon-person199',
			24 => 'fi flaticon-heating1',
			25 => 'fi flaticon-wind34',
			28 => 'fi flaticon-person206',
			30 => 'fi flaticon-sign35',
			31 => 'fa fa-sort-amount-asc rotate--90',
			33 => 'fa fa-fire',
			35 => 'glyphicon glyphicon-globe',
			37 => 'glyphicon glyphicon-globe',
			38 => 'fi flaticon-switches4',
			43 => 'fa fa-hand-paper-o',
			47 => 'fa fa-bolt',
			49 => 'fi flaticon-thermometer2',
			50 => 'fa fa-volume-up',
			51 => 'fa fa-chain-broken',
			52 => 'fa fa-sort-amount-asc rotate--90',
			54 => 'fi flaticon-open203',
			60 => 'fa fa-sun-o',
			61 => 'fi flaticon-measure20',
			68 => 'fi flaticon-minisplit',
			85 => 'fa fa-lightbulb-o',
			86 => 'fi flaticon-switches4'
	);
	
	return $icons[$iddevice];
}

function display_widget($info){
	
	$widget = array(
			2  => "display_cam",
			4  => "display_lampe",
			5  => "display_commande",
			7  => "display_warming",
			9  => "display_warming",
			10 => "display_shutter",
			11 => "display_shutter",
			15 => "display_audio",
			16 => 'display_videoplayer',
			17 => "display_audio",
			18 => "display_garden",
			20 => "display_furnace",
			22 => "display_garden",
			23 => "display_garden",
			24 => "display_warming",
			25 => "display_fan",
			28 => "display_spa",
			30 => "display_alarm",
			31 => "display_portal",
			33 => "display_furnace",
			35 => 'display_webpage_internal',
			37 => 'display_webpage_external',
			38 => "display_commande",
			43 => "display_commande",
			47 => "display_consumption",
			49 => "display_warming",
			50 => "display_audio",
			51 => "display_commande",
			52 => "display_portal",
			54 => "display_portal",
			60 => "display_commande",
			61 => "display_commande",
			68 => "display_clim",
			85 => "display_lampe",
			86 => 'display_generic'
	);
	
	
	$widgeticon = 'fa fa-question';
	if (!empty(getIcon($info->device_id))){
		$widgeticon = getIcon($info->device_id);
	}

	$dir = '/templates/default/custom/device/';

	$display = '<div class="display-widget col-xs-12 col-sm-6 col-lg-4 room-'.$info->room_id.' app-'.$info->application_id.'">
					<div class="box">
						<div class="icon">
							<div id="image-widget-'.$info->room_device_id.'" class="image">
								<i id="icon-image-widget-'.$info->room_device_id.'" class="'.$widgeticon.'"></i>
							</div>
							<div class="info col-sm-12 col-xs-12 widget-content">';
								//display device
								if (!empty($widget[$info->device_id])){
									$display.=$widget[$info->device_id]($info);
								}
								$display.='
							</div>';
							if (!empty($info->device_bgimg)) {
								$display.='
								<div class="info-bg"
								     style="background-image: url(\''.$dir.$info->device_bgimg.'\')">
								</div>';
							}
							$display.='
						</div>
					</div>
				</div>';
	
	return $display;
}

/*   Widget   */

// Widget alarm
function display_alarm($info){
	$display = '
			<div class="info-widget foreground-widget">
				<button title="'._('More').'"
				        onclick="HandlePopup(7, '.$info->room_device_id.')"
				        class="btn btn-greenleaf"
				        type="button"
				        id="btn-'.$info->room_device_id.'">
				        <span class="fa fa-key md"></span>
				</button>
			</div>
			<h3 class="title">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{92})){
		$display.= display_led($info, 92, 5);
	}
	
	return $display;
}

// Widget fan
function display_fan($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}

	if (!empty($info->device_opt->{400}) || !empty($info->device_opt->{401}) || !empty($info->device_opt->{402}) ||
	    !empty($info->device_opt->{403}) || !empty($info->device_opt->{404}) || !empty($info->device_opt->{405}) || !empty($info->device_opt->{406})){
		$display.=
			'<select class="form-control center selectpicker" onchange="changeSpeedFan('.$info->room_device_id.', 1, 0)" id="speed-fan">';
	}

	if (!empty($info->device_opt->{400})){
		$display.= '<option value="400">'._('Speed 0').'</option>';
	}
	if (!empty($info->device_opt->{401})){
		$display.= '<option value="401">'._('Speed 1').'</option>';
	}
	if (!empty($info->device_opt->{402})){
		$display.= '<option value="402">'._('Speed 2').'</option>';
	}
	if (!empty($info->device_opt->{403})){
		$display.= '<option value="403">'._('Speed 3').'</option>';
	}
	if (!empty($info->device_opt->{404})){
		$display.= '<option value="404">'._('Speed 4').'</option>';
	}
	if (!empty($info->device_opt->{405})){
		$display.= '<option value="405">'._('Speed 5').'</option>';
	}
	if (!empty($info->device_opt->{406})){
		$display.= '<option value="406">'._('Speed 6').'</option>';
	}
	
	if (!empty($info->device_opt->{400}) || !empty($info->device_opt->{401}) || !empty($info->device_opt->{402}) ||
		!empty($info->device_opt->{403}) || !empty($info->device_opt->{404}) || !empty($info->device_opt->{405}) || !empty($info->device_opt->{406})){
			$display.=
	 		'</select>';
	}
	return $display;
}

// Widget warming
function display_warming($info){
	$display = '';
	$display.=
	'<div class="info-widget foreground-widget">
					<button title="'._('More').'" onclick="HandlePopup(5, '.$info->room_device_id.')"
					        class="btn btn-greenleaf" type="button">
						<span class="fa fa-plus md"></span>
					</button>
				</div>
				<h3 class="title foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	if (!empty($info->device_opt->{13})){
		$display.=display_varie($info);
	}
	if (!empty($info->device_opt->{72})){
		$display.= display_temperature($info);
	}
	if (!empty($info->device_opt->{388})){
		$display.=display_minusplus($info);
	}
	
	return $display;
}

// Widget garden
function display_garden($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';

	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}

	return $display;
}

// Widget Spa
function display_spa($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

// Widget Clim
function display_clim($info){
	$display = '';
	$display.=
				'<div class="info-widget foreground-widget">
					<button title="'._('More').'" onclick="HandlePopup(4, '.$info->room_device_id.')"
					        class="btn btn-greenleaf" type="button">
						<span class="fa fa-plus md"></span>
					</button>
				</div>
				<h3 class="title foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	
	if (!empty($info->device_opt->{400}) || !empty($info->device_opt->{401}) || !empty($info->device_opt->{402}) ||
		!empty($info->device_opt->{403}) || !empty($info->device_opt->{404})){
		$display.= '<div class="center foreground-widget">';
	}
	
	if (!empty($info->device_opt->{400})){
		$display.= '<button onclick="changeSpeedFan('.$info->room_device_id.', 1, 400)" class="btn btn-info">'._('0').'</button> ';
	}
	if (!empty($info->device_opt->{401})){
		$display.= '<button onclick="changeSpeedFan('.$info->room_device_id.', 1, 401)" class="btn btn-info">'._('1').'</button> ';
	}
	if (!empty($info->device_opt->{402})){
		$display.= '<button onclick="changeSpeedFan('.$info->room_device_id.', 1, 402)" class="btn btn-info">'._('2').'</button> ';
	}
	if (!empty($info->device_opt->{403})){
		$display.= '<button onclick="changeSpeedFan('.$info->room_device_id.', 1, 403)" class="btn btn-info">'._('3').'</button> ';
	}
	if (!empty($info->device_opt->{404})){
		$display.= '<button onclick="changeSpeedFan('.$info->room_device_id.', 1, 404)" class="btn btn-info">'._('4').'</button> ';
	}
	
	if (!empty($info->device_opt->{400}) || !empty($info->device_opt->{401}) || !empty($info->device_opt->{402}) ||
		!empty($info->device_opt->{403}) || !empty($info->device_opt->{404})){
		$display.= '</div><br/>';
	}
	
	if (!empty($info->device_opt->{388})){
		$display.=display_minusplus($info);
	}
	
	return $display;
}

// widget Audio
function display_audio($info){
	$display =
			'<div class="info-widget foreground-widget">
				<button title="'._('More').'" onclick="HandlePopup(1, '.$info->room_device_id.')" class="btn btn-greenleaf" type="button">
					<span class="fa fa-plus md"></span>
				</button>
			</div>
			<h3 class="title">'.$info->name.'</h3>
			<div class="btn-group margin-bottom center">';
		
		if (!empty($info->device_opt->{12})){
			$display.=
			'<button onclick="launchGeneric('.$info->room_device_id.', 12)"
					         class="btn btn-info">
						<span class="glyphicon glyphicon-off"></span>
					</button>';
		}
		
		if (!empty($info->device_opt->{364})){
			$display.=
				'<button onclick="launchGeneric('.$info->room_device_id.', 364)"
				         class="btn btn-info">
					<span class="glyphicon glyphicon-pause"></span>
				</button>';
		}
		if (!empty($info->device_opt->{363})){
			$display.=
				'<button onclick="launchGeneric('.$info->room_device_id.', 363)"
				         class="btn btn-info">
					<span class="glyphicon glyphicon-play"></span>
				</button>';
		}
		if (!empty($info->device_opt->{368})){
			$display.=
			'<button onclick="launchGeneric('.$info->room_device_id.', 368)" class="btn btn-info">
				<span id="icon-mute" class="glyphicon glyphicon-volume-off"></span>
			</button>';
		}
	$display.='</div>';

	if (!empty($info->device_opt->{383})){
		$display.='
				<div class="col-xs-12 foreground-widget">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cursor"
					     onclick="Volume(\''.$info->room_device_id.'\', 383, -1)">
						<i class="glyphicon glyphicon-volume-down"></i>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<output id="vol-'.$info->room_device_id.'" for="volume-'.$info->room_device_id.'">
							50%
						</output>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cursor"
					     onclick="Volume(\''.$info->room_device_id.'\', 383, 1)">
						<i class="glyphicon glyphicon-volume-up"></i>
					</div>
					<div class="row">
						<input onchange="SetVolume(\''.$info->room_device_id.'\', 383)"
						       value="50" min="0" step="1" max="100" id="volume-'.$info->room_device_id.'"
						       oninput="UpdateVol(\''.$info->room_device_id.'\', value)" type="range">
					</div>
				</div>';
	}
	return $display;
}

function display_videoplayer($info){
	$display = '';
	switch($info->protocol_id){
		case 1:
			// KNX
			$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
			break;
		case 6:
			$display =
			'<div class="info-widget foreground-widget">
						<button title="'._('More').'" onclick="HandlePopup(8, '.$info->room_device_id.')" class="btn btn-greenleaf" type="button">
							<span class="fa fa-plus md"></span>
						</button>
					</div>
					<h3 class="title">'.$info->name.'</h3>
					<div class="btn-group margin-bottom center">';
	
			if (!empty($info->device_opt->{12})){
				$display.=
				'<button onclick="launchGeneric('.$info->room_device_id.', 12)"
							         class="btn btn-info">
								<span class="glyphicon glyphicon-off"></span>
							</button>';
			}
	
			if (!empty($info->device_opt->{364})){
				$display.=
				'<button onclick="launchGeneric('.$info->room_device_id.', 364)"
						         class="btn btn-info">
							<span class="glyphicon glyphicon-pause"></span>
						</button>';
			}
			if (!empty($info->device_opt->{363})){
				$display.=
				'<button onclick="launchGeneric('.$info->room_device_id.', 363)"
						         class="btn btn-info">
							<span class="glyphicon glyphicon-play"></span>
						</button>';
			}
			if (!empty($info->device_opt->{368})){
				$display.=
				'<button onclick="launchGeneric('.$info->room_device_id.', 368)" class="btn btn-info">
						<span id="icon-mute" class="glyphicon glyphicon-volume-off"></span>
					</button>';
			}
			$display.='</div>';
			break;
		default :
			// TODO
			$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
			break;
	}
	
	if (!empty($info->device_opt->{383})){
		$display.='
				<div class="col-xs-12 foreground-widget">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cursor"
					     onclick="Volume('.$info->room_device_id.', 383, -1)">
						<i class="glyphicon glyphicon-volume-down"></i>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<output id="vol-'.$info->room_device_id.'" for="volume-'.$info->room_device_id.'">
							50%
						</output>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cursor"
					     onclick="Volume('.$info->room_device_id.', 383, 1)">
						<i class="glyphicon glyphicon-volume-up"></i>
					</div>
					<div class="row">
						<input onchange="SetVolume('.$info->room_device_id.', 383)"
						       value="50" min="0" step="1" max="100" id="volume-'.$info->room_device_id.'"
						       oninput="UpdateVol('.$info->room_device_id.', value)" type="range">
					</div>
				</div>';
	}
	return $display;
}

//widget portail
function display_portal($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12}) or !empty($info->device_opt->{96})){
		$display.=display_OpenClose($info);
	}
	$open = 0;
	$close= 0;
	if(!empty($info->device_opt->{471})) {
		$open = 1;
	}
	if(!empty($info->device_opt->{472})) {
		$close = 1;
	}
	if ($open){
		$display.=display_Open($info, $close);
	}
	if ($close){
		$display.=display_Close($info, $open);
	}
	
	return $display;
}

//widget store
function display_shutter($info){
	$display = '';
	if (!empty($info->device_id) && !empty($info->device_opt->{442})){
		$display .=
		'<div class="info-widget foreground-widget">
			<button id="widget_info-'.$info->room_device_id.'" title="'._('More').'"
			        onclick="HandlePopup(6, '.$info->room_device_id.')"
			        class="btn btn-greenleaf"
			        type="button">
				<span class="fa fa-plus md"></span>
			</button>
		</div>';
		$display .= '<h3 class="title foreground-widget">'.$info->name.'</h3>';
	}
	else {
		$display .= '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	}
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	if (!empty($info->device_opt->{13})){
		$display.=display_varie($info, 2);
	}
	if (!empty($info->device_opt->{54})){
		$display.=display_UpDown($info);
	}
	
	return $display;
}

//widget commande
function display_commande($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	if (!empty($info->device_opt->{13})){
		$display.=display_varie($info);
	}
	if (!empty($info->device_opt->{54})){
		$display.=display_UpDown($info);
	}
	
	$tab = array();
	if (!empty($info->device_opt->{6})){
		$tab[] = display_hygrometry($info);
	}
	if (!empty($info->device_opt->{173})){
		$tab[] = display_rain($info);
	}
	if (!empty($info->device_opt->{79})){
		$tab[] = display_luminosity($info);
	}
	if (!empty($info->device_opt->{72})){
		$tab[] = display_temperature($info);
	}
	if (!empty($info->device_opt->{174})){
		$tab[] = display_wind($info);
	}
	if (!empty($info->device_opt->{73})){
		$tab[] = display_co2($info);
	}
	if (!empty($info->device_opt->{441})){
		$tab[] = display_voc($info);
	}
	
	$size = sizeof($tab);
	if($size == 1) {
		$display.= $tab[0];
	}
	elseif($size > 1) {
		for($i=0;$i<$size;++$i) {
			if($i > 0 && $i%2 == 0) {
				$display.= '<div class="clearfix"></div>';
			}
			$display.= '<div class="col-xs-6">';
			$display.= $tab[$i];
			$display.= '</div>';
		}
	}
	
	if (!empty($info->device_opt->{437})){
		if (!empty($info->device_opt->{438})){
			$display.= '<div class="col-xs-6">';
		}
		else {
			$display.= '<div class="col-xs-12">';
		}
		$display.= display_led($info, 437, 4);
		$display.= '</div>';
	}
	if (!empty($info->device_opt->{438})){
		$display.= '<div class="col-xs-6">';
		$display.= display_led($info, 438, 4);
		$display.= '</div>';
	}
	if (!empty($info->device_opt->{439})){
		$display.= '<div class="col-xs-6">';
		$display.= display_led($info, 439, 4);
		$display.= '</div>';
	}
	if (!empty($info->device_opt->{440})){
		$display.= '<div class="col-xs-6">';
		$display.= display_led($info, 440, 4);
		$display.= '</div>';
	}
	
	$tab = array();
	if (!empty($info->device_opt->{97})){
		$tab[] = 97;
	}
	if (!empty($info->device_opt->{112})){
		$tab[] = 112;
	}
	if (!empty($info->device_opt->{113})){
		$tab[] = 113;
	}
	$size = sizeof($tab);
	
	if($size == 1) {
		$display.= display_led($info, $tab[0], 5);
	}
	elseif($size >= 2) {
		$display.= '<div class="col-xs-6">';
		$display.= display_led($info, $tab[0], 4);
		$display.= '</div>';
		$display.= '<div class="col-xs-6">';
		$display.= display_led($info, $tab[1], 4);
		$display.= '</div>';
		
		if($size == 3) {
			$display.= '<div class="col-xs-12">';
			$display.= display_led($info, $tab[2], 4);
			$display.= '</div>';
		}
	}
	
	return $display;
}

//widget lampe
function display_lampe($info){
	$display = '';
	if (!empty($info->device_id) && $info->device_id == 85){
		$display .= '<div class="info-widget foreground-widget">';
		
		$display .= '<button title="'._('More').'" onclick="popupChromaWheel('.$info->room_device_id.', 0, 0)" class="btn btn-greenleaf" type="button">';
		$display .= '<span class="fa fa-plus md"></span>';
		$display .= '</button>
					</div>';
	}
	elseif (!empty($info->device_id) && !empty($info->device_opt->{153})){
		$display .=
					'<div class="info-warning foreground-widget">
						<button id="widget_info-'.$info->room_device_id.'-153" title="'._('More').'"
						        onclick="HandlePopup(2, '.$info->room_device_id.')"';
		if ($info->device_opt->{153}->opt_value == 1){
			$display .= '       class="btn btn-danger"';
		}
		else{
			$display .= '       class="btn btn-greenleaf"';
		}
		$display .=
					           ' type="button">
						<span class="fa fa-info-circle md"></span>
						</button>
					</div>';
	}
	
	if ($info->device_id == 85 || !empty($info->device_opt->{153})){
		$display .= '<h3 class="title foreground-widget">'.$info->name.'</h3>';
	}
	else {
		$display .= '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	}

	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	if (!empty($info->device_opt->{13})){
		$display.=display_varie($info);
	}
	
	return $display;
}

//widget camera
function display_cam($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>
				<div class="foreground-widget">
					<button type="button" class="btn btn-info" onclick="HandlePopup(0, \''.$info->room_device_id.'\')">'._('View').'</button>
				</div>';

	return $display;
}

//widget chaudière
function display_furnace($info){
	$display= '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	
	return $display; 
}

//widget electric consumption
function display_consumption($info){
	$display= '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>';
	
	//consumption option
	if (!empty($info->device_opt->{399})){
		$display.= display_consumption_option($info);
	}
	
	//power option
	if (!empty($info->device_opt->{407})){
		$display.= display_power_option($info);
	}
	
	return $display;
}

/*   Option   */

//Open Close
function display_OpenClose($info){
	$display ='
			<div class="margin-bottom btn-group btn-group-greenleaf foreground-widget">
				<button type="button" class="btn btn-onoff-widget btn-primary" onclick="onOff(\''.$info->room_device_id.'\', 1, 96)">'._('Open').'</button>
				<button type="button" class="btn btn-onoff-widget btn-default" onclick="onOff(\''.$info->room_device_id.'\', 0, 96)">'._('Close').'</button>
			</div>
			<br/>';
	
	return $display;
}
function display_Open($info, $close){
	$display = '';
	if(!empty($close)) {
		$display .= '<div class="margin-bottom btn-group btn-group-greenleaf foreground-widget">';
	}
	$display .='<button type="button" class="btn btn-onoff-widget btn-primary" onclick="onOff(\''.$info->room_device_id.'\', 1, 471)">'._('Open').'</button>';

	return $display;
}
function display_Close($info, $open){
	$display = '';
	$display .='<button type="button" class="btn btn-onoff-widget btn-default" onclick="onOff(\''.$info->room_device_id.'\', 1, 472)">'._('Close').'</button>';
	if(!empty($open)) {
		$display .= '</div>';
	}

	return $display;
}
//Up Down
function display_UpDown($info){
	if (!empty($info->device_opt->{13})){
		$icon_up = "fa fa-angle-double-up";
		$icon_pause = "fa fa-pause";
		$icon_down = "fa fa-angle-double-down";
	}
	else {
		$icon_up = "fa fa-angle-double-up lg";
		$icon_pause = "fa fa-pause lg";
		$icon_down = "fa fa-angle-double-down lg";
	}
	$display ='
			<div class="margin-bottom btn-group btn-group-greenleaf foreground-widget">
				<button type="button" class="btn btn-warning"
				        onclick="onOff(\''.$info->room_device_id.'\', 1, 54)">
					<span class="'.$icon_up.'"></span>
				</button>';
				if (!empty($info->device_opt->{365})){
					$display.='<button type="button" class="btn btn-warning"
								       onclick="launchGeneric('.$info->room_device_id.', 365)">
									<span class="'.$icon_pause.'"></span>
								</button>';
				}
	$display.='<button type="button" class="btn btn-warning"
				       onclick="onOff(\''.$info->room_device_id.'\', 0, 54)">
					<span class="'.$icon_down.'"></span>
				</button>
			</div>';
	 
	return $display;
}

// On/Off
function display_OnOff($info, $popup = 0){
	$display = '';
	switch($info->protocol_id){
		// KNX
		case 1:
			if (!empty($info->device_opt->{12}->addr_plus)){
				$display .= '<div class="checkbox foreground-widget">';
						if (!empty($info->device_opt->{12}->opt_value)){
						 	$display.='<input data-on-color="greenleaf"'
										      .'data-label-width="0"'
										      .'data-on-text="'._('On').'"'
										      .'data-off-text="'._('Off').'"'
										      .'checked ';
						 						if ($popup == 0){
						 				      		$display.='id="onoff-'.$info->room_device_id.'" ';
						 						}
						 				      	else{ 
						 				      		$display.='id="onoff-popup-'.$info->room_device_id.'" ';
						 				      	}
						 		   $display.='class="onoff-switch"'
									      .'type="checkbox"'
									      .'onchange="onOffToggle(\''.$info->room_device_id.'\', 12, '.$popup.')"'
									      .'/>';
						}
						else {
							$display.='<input data-on-color="greenleaf"'
										      .'data-label-width="0"'
										      .'data-on-text="'._('On').'"'
										      .'data-off-text="'._('Off').'" ';
						 						if ($popup == 0){
						 				      		$display.='id="onoff-'.$info->room_device_id.'" ';
						 						}
						 				      	else{ 
						 				      		$display.='id="onoff-popup-'.$info->room_device_id.'" ';
						 				      	}	      			
						 		   $display.='class="onoff-switch"'
										      .'type="checkbox"'
										      .'onchange="onOffToggle(\''.$info->room_device_id.'\', 12, '.$popup.')"'
										.'/>';
						}
				$display.='</div>';
			}
			else {
				$display .= '<div class="margin-bottom btn-group btn-group-greenleaf foreground-widget">'
								.'<button type="button" class="btn btn-onoff-widget btn-greenleaf" onclick="onOff(\''.$info->room_device_id.'\', 1, 12)">'._('On').'</button>'
								.'<button type="button" class="btn btn-onoff-widget btn-danger" onclick="onOff(\''.$info->room_device_id.'\', 0, 12)" >'._('Off').'</button>'
							.'</div>';
			}
		break;
		// EnOcean
		case 2:
			$display .= '<div class="btn-group foreground-widget">'
							.'<button type="button" class="btn btn-greenleaf">'._('On').'</button>'
							.'<button type="button" class="btn btn-danger">'._('Off').'</button>'
						.'</div>';
		break;
		default:
			$display.='';
		break;
	}

	$display.='<script type="text/javascript">'
			   .'$(".onoff-switch").bootstrapSwitch();'
			   .'</script>';
	return $display;
}

//Varie
function display_varie($info, $var_icon = 1, $option_id=13){
	$display = '<div class="col-xs-12">';
	if ($var_icon == 1) {
		$left_icon = "fa-certificate";
		$right_icon = "fa-sun-o";
	}
	else if ($var_icon == 2) {
		$left_icon = "fa-sort-amount-asc";
		$right_icon = "fa-sort-amount-desc rotate-180";
	}
	else if ($var_icon == 3) {
		$left_icon = "fa-sort-amount-asc fa-flip-vertical-rotate-270";
		$right_icon = "fa-sort-amount-asc fa-rotate-270";
	}
	
	switch($info->protocol_id){
		// KNX
		case 1:
				$display .= 
							'<div onclick="Variation(\''.$info->room_device_id.'\', '.$option_id.', -1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor foreground-widget">
								<i class="fa '.$left_icon.'"></i>
							</div>';
							if ($info->device_opt->{$option_id}->opt_value > 0){
								$val = ceil(($info->device_opt->{$option_id}->opt_value * 100) / 255);
								$display.=
										'<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<output id="range-'.$info->room_device_id.'"
											        for="slider-value-'.$info->room_device_id.'">
												'.$val.'%
											</output>
										</div>';
							}
							else {
								$display.=
										'<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 foreground-widget">
											<output id="range-'.$info->room_device_id.'"
											        for="slider-value-'.$info->room_device_id.'">
												50%
											</output>
										</div>';
							}
							$display.=
										'<div onclick="Variation(\''.$info->room_device_id.'\', '.$option_id.', 1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor foreground-widget">
											<i class="fa '.$right_icon.'"></i>
										</div>';
							if (!empty($info->device_opt->{$option_id}->opt_value)){
								$display.=
										'<div class="row foreground-widget">
											<input value="'.$info->device_opt->{$option_id}->opt_value.'" min="0" step="1" max="255"
											       onMouseDown="LockWidget('.$info->room_device_id.', '.$option_id.')"
											       onMouseUp="UnlockWidget('.$info->room_device_id.', '.$option_id.')"
											       ontouchstart="LockWidget('.$info->room_device_id.', '.$option_id.')"
											       ontouchend="UnlockWidget('.$info->room_device_id.', '.$option_id.')"
											       oninput="outputUpdate(\''.$info->room_device_id.'\', value)"
											       onchange="getVariation(\''.$info->room_device_id.'\', '.$option_id.')"
											       id="slider-value-'.$info->room_device_id.'" type="range">
										</div>';
							}
							else {
								$display.=
										'<div class="row foreground-widget">
											<input value="128" min="0" step="1" max="255"
											       onMouseDown="LockWidget('.$info->room_device_id.', '.$option_id.')"
											       onMouseUp="UnlockWidget('.$info->room_device_id.', '.$option_id.')"
											       ontouchstart="LockWidget('.$info->room_device_id.', '.$option_id.')"
											       ontouchend="UnlockWidget('.$info->room_device_id.', '.$option_id.')"
											       oninput="outputUpdate(\''.$info->room_device_id.'\', value)"
											       onchange="getVariation(\''.$info->room_device_id.'\', '.$option_id.')"
											       id="slider-value-'.$info->room_device_id.'" type="range">
										</div>';
							}
		break;
			//  EnOcean
		case 2:
			// TODO enocean
		break;
		default:
			//TODO ip
		break;
	}
	return $display.'</div>';
}

// Minus plus

function display_minusplus($info, $popup = 0){
	$temp = $info->device_opt->{388}->opt_value;
	if (empty($temp)){
		$temp = '0.0';
	}
	$display = '<div class="input-group foreground-widget">
					<span onclick="UpdateTemp(\''.$info->room_device_id.'\', 388, -1)" class="btn btn-warning input-group-addon"><i class="fa fa-minus md"></i></span> ';
					if ($popup == 0){
						$display.='<output class="margin-top-4" id="output-mp-'.$info->room_device_id.'">'.$temp.'</output> ';
					}
					else{
						$display.='<output class="margin-top-4" id="output-mp-popup-'.$info->room_device_id.'">'.$temp.'</output> ';
					}
					$display.='<span onclick="UpdateTemp(\''.$info->room_device_id.'\', 388, 1)" class="btn btn-warning input-group-addon"><i class="fa fa-plus md"></i></span>
			    </div>';
	
	return  $display;
}

//Temperature
function display_temperature($info){
	$tmp = '0';
	if (!empty($info->device_opt->{72}->opt_value)){
		$tmp = $info->device_opt->{72}->opt_value;
	}
	$display = '<div class="foreground-widget">
					<i class="fi flaticon-thermometer2"></i>
					<span id="widget-'.$info->room_device_id.'-72">'.$tmp.'</span>
					<span>'.$info->device_opt->{72}->unit.'</span>
				</div>';

	return $display;
}

function display_wind($info){
	$tmp = '0';
	if (!empty($info->device_opt->{174}->opt_value)){
		$tmp = $info->device_opt->{174}->opt_value;
	}
	$display = '
	<div class="foreground-widget">
		<i class="fa fa-flag-o "></i>
		<span id="widget-'.$info->room_device_id.'-174">'.$tmp.'</span>
		<span>'.$info->device_opt->{174}->unit.'</span>
	</div>';

	return $display;
}

function display_co2($info){
	$tmp = '0';
	if (!empty($info->device_opt->{73}->opt_value)){
		$tmp = $info->device_opt->{73}->opt_value;
	}
	$display = '
	<div class="foreground-widget">
		'._('CO2').'
		<span id="widget-'.$info->room_device_id.'-73">'.$tmp.'</span>
		<span>'.$info->device_opt->{73}->unit.'</span>
	</div>';

	return $display;
}

function display_voc($info){
	$tmp = '0';
	if (!empty($info->device_opt->{441}->opt_value)){
		$tmp = $info->device_opt->{441}->opt_value;
	}
	$display = '
	<div class="foreground-widget">
		'._('VOC').'
		<span id="widget-'.$info->room_device_id.'-441">'.$tmp.'</span>
		<span>'.$info->device_opt->{441}->unit.'</span>
	</div>';

	return $display;
}

// Led
function display_led($info, $option_id, $size=5){
	$tmp = '0';
	if (!empty($info->device_opt->{$option_id}->opt_value)){
		$tmp = $info->device_opt->{$option_id}->opt_value;
	}
	if ($tmp == '0'){
		$display = '
		<div class="foreground-widget">
			<i id="command-'.$info->room_device_id.'-'.$option_id.'" 
			   class="glyphicon glyphicon-record led-off glyphicon-'.$size.'"></i>
		</div>';
	}
	else{
		$display = '
		<div class="foreground-widget">
			<i id="command-'.$info->room_device_id.'-'.$option_id.'" 
			   class="glyphicon glyphicon-record led-on glyphicon-'.$size.'"></i>
		</div>';
	}
	return $display;
}

//Consumption 
function display_consumption_option($info){
	$tmp = '0';
	if (!empty($info->device_opt->{399}->opt_value)){
		$tmp = $info->device_opt->{399}->opt_value;
	}
	
	$cost = $info->device_opt->{399}->highCost;
	
	$h1 = explode("-", $info->device_opt->{399}->lowField1)[0];
	$h2 = explode("-", $info->device_opt->{399}->lowField1)[1];
	
	if ($h1 < $h2){
		if ($info->device_opt->{399}->time >= $h1 && $info->device_opt->{399}->time < $h2){
			$cost = $info->device_opt->{399}->lowCost;;
		}
	}
	else if ($h1 > $h2){
		if ($info->device_opt->{399}->time >= $h1 && $info->device_opt->{399}->time < 24){
			$cost = $info->device_opt->{399}->lowCost;;
		}
		else if ($info->device_opt->{399}->time >= 0 && $info->device_opt->{399}->time < $h2){
			$cost = $info->device_opt->{399}->lowCost;;
		}
	}

	$h1 = explode("-", $info->device_opt->{399}->lowField2)[0];
	$h2 = explode("-", $info->device_opt->{399}->lowField2)[1];
	
	if ($h1 < $h2){
		if ($info->device_opt->{399}->time >= $h1 && $info->device_opt->{399}->time < $h2){
			$cost = $info->device_opt->{399}->lowCost;;
		}
	}
	else if ($h1 > $h2){
		if ($info->device_opt->{399}->time >= $h1 && $info->device_opt->{399}->time < 24){
			$cost = $info->device_opt->{399}->lowCost;;
		}
		else if ($info->device_opt->{399}->time >= 0 && $info->device_opt->{399}->time < $h2){
			$cost = $info->device_opt->{399}->lowCost;;
		}
	}
	
	$display = '<div class="foreground-widget">
					<i class="fa fa-bolt"></i>
					<span id="widget-'.$info->room_device_id.'-399">'.$tmp.'</span>
					<span>'.$info->device_opt->{399}->unit.'</span>
					<span id="widget-'.$info->room_device_id.'-399-cost">&nbsp;-&nbsp;'.
					($tmp * $cost).$info->device_opt->{399}->currency
					.'</span>
				</div>';

	return $display;
}

//Power
function display_power_option($info){
	$tmp = '0';
	if (!empty($info->device_opt->{407}->opt_value)){
		$tmp = $info->device_opt->{407}->opt_value;
	}
	$display = '<div class="foreground-widget">
					<i class="fa fa-bolt"></i>
					<span id="widget-'.$info->room_device_id.'-407">'.$tmp.'</span>
					<span>'.$info->device_opt->{407}->unit.'</span>
				</div>';

	return $display;
}

//Hygrometry
function display_hygrometry($info){
	$hygro = '0';
	if (!empty($info->device_opt->{6}->opt_value)){
		$hygro = $info->device_opt->{6}->opt_value;
	}
	$display = '<div class="foreground-widget">
					<i class="fa fa-tint"></i>
					<span id="widget-'.$info->room_device_id.'-6">'.$hygro.'</span> %
				</div>';

	return $display;
}
function display_rain($info){
	$rain = '0';
	if (!empty($info->device_opt->{173}->opt_value)){
		$rain = $info->device_opt->{173}->opt_value;
	}
	$display = '<div class="foreground-widget">
					<i class="fa fa-umbrella"></i>
					<span id="widget-'.$info->room_device_id.'-173">'.($rain).'</span>
					<span>'.$info->device_opt->{173}->unit.'</span>
				</div>';

	return $display;
}

//Luminosity
function display_luminosity($info){
	$current_id = 79;
	$lum = '0';
	if (!empty($info->device_opt->{$current_id}->opt_value)){
		$lum = $info->device_opt->{$current_id}->opt_value;
	}
	$display = '<div class="foreground-widget">
					<i class="fa fa-sun-o"></i>
					<span id="widget-'.$info->room_device_id.'-'.$current_id.'">'.$lum.'</span>
					<span>'.$info->device_opt->{$current_id}->unit.'</span>
				</div>';

	return $display;
}

// Generic
function display_generic($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>
				<div class="foreground-widget">';
	$btn = '';
	$btnNb = 0;
	$btnOld= 0;
	
	if (!empty($info->device_opt->{181})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 181)" class="btn btn-info">'._('1').'</button> ';
		++$btnNb;
	}
	if (!empty($info->device_opt->{182})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 182)" class="btn btn-info">'._('2').'</button> ';
		++$btnNb;
	}
	if (!empty($info->device_opt->{189})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 189)" class="btn btn-info">'._('3').'</button> ';
		++$btnNb;
	}
	if($btnNb > 0) {
		$btnOld = $btnNb;
		$btn.= '<br/>';
	}
	if (!empty($info->device_opt->{191})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 191)" class="btn btn-info">'._('4').'</button> ';
		++$btnNb;
	}
	if (!empty($info->device_opt->{195})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 195)" class="btn btn-info">'._('5').'</button> ';
		++$btnNb;
	}
	if (!empty($info->device_opt->{196})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 196)" class="btn btn-info">'._('6').'</button> ';
		++$btnNb;
	}
	if($btnNb > $btnOld) {
		$btn.= '<br/>';
	}
	if (!empty($info->device_opt->{197})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 197)" class="btn btn-info">'._('7').'</button> ';
		++$btnNb;
	}
	if (!empty($info->device_opt->{198})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 198)" class="btn btn-info">'._('8').'</button> ';
		++$btnNb;
	}
	if (!empty($info->device_opt->{199})){
		$btn.= '<button onclick="launchGeneric('.$info->room_device_id.', 199)" class="btn btn-info">'._('9').'</button> ';
		++$btnNb;
	}
	
	
	if ($btnNb == 1 &&!empty($info->device_opt->{181})) {
		$btn = '<button onclick="launchGeneric('.$info->room_device_id.', 181)" class="btn btn-info">&nbsp;&nbsp;&nbsp;</button> ';
	}
	
	$display .= $btn;
	$display .= '</div>';
	
	return $display;
}

//Web page
function display_webpage_internal($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>
				<div class="foreground-widget">
					<button type="button" class="btn btn-info" onclick="HandlePopup(9, \''.$info->room_device_id.'\')">'._('View').'</button>
				</div>';
	return $display;
}
function display_webpage_external($info){
	$display = '<h3 class="title margin-top foreground-widget">'.$info->name.'</h3>
				<div class="foreground-widget">
					<button type="button" class="btn btn-info" onclick="HandlePopup(10, \''.$info->room_device_id.'\')">'._('View').'</button>
				</div>';
	return $display;
}

?>