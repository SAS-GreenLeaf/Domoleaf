<?php 

// No echo here !!!

function getIcon($iddevice = 1){
	$icons = array(
			1  => 'fi flaticon-chip',
			2  => 'fa fa-video-camera',
			3  => 'fa fa-lightbulb-o',
			4  => 'fa fa-lightbulb-o',
			5  => 'fa fa-tachometer',
			6  => 'fa fa-lightbulb-o',
			7  => 'fi flaticon-heating1',
			8  => 'fi flaticon-heating1',
			9  => 'fa fa-question',
			10 => 'fa fa-bars',
			11 => 'fa fa-bars',
			12 => 'fi flaticon-snowflake149',
			13 => 'fi flaticon-snowflake149',
			14 => 'fa fa-volume-up',
			15 => 'fa fa-volume-up',
			17 => 'fa fa-volume-up',
			18 => 'fa fa-tree',
			19 => 'fi flaticon-winds4',
			20 => 'fa fa-fire',
			21 => 'fa fa-question',
			22 => 'fi flaticon-engineering',
			23 => 'fi flaticon-person199',
			24 => 'fi flaticon-heating1',
			25 => 'fi flaticon-wind34',
			26 => 'fi flaticon-wind34',
			27 => 'fi flaticon-person206',
			28 => 'fi flaticon-person206',
			29 => 'fa fa-video-camera',
			30 => 'fi flaticon-sign35',
			31 => 'fa fa-sort-amount-asc rotate--90',
			32 => 'fi flaticon-password4',
			33 => 'fa fa-fire',
			34 => 'fi flaticon-snowflake149',
			35 => 'fa fa-question',
			36 => 'fa fa-question',
			37 => 'fa fa-question',
			38 => 'fa fa-question',
			39 => 'fa fa-question',
			40 => 'fa fa-question',
			41 => 'fa fa-question',
			42 => 'fa fa-question',
			43 => 'fa fa-question',
			44 => 'fa fa-question',
			45 => 'fa fa-question',
			46 => 'fa fa-question',
			47 => 'fa fa-bolt',
			48 => 'fa fa-question',
			49 => 'fi flaticon-thermometer2',
			50 => 'fa fa-volume-up',
			51 => 'fa fa-question',
			52 => 'fa fa-sort-amount-asc rotate--90',
			53 => 'fa fa-wifi',
			54 => 'fi flaticon-open203',
			55 => 'fa fa-lightbulb-o',
			56 => 'fa fa-lightbulb-o',
			57 => 'fa fa-lightbulb-o',
			58 => 'fa fa-question',
			59 => 'fa fa-question',
			60 => 'fa fa-question',
			61 => 'fi flaticon-measure20',
			62 => 'fi flaticon-minisplit',
			63 => 'fi flaticon-minisplit',
			65 => 'fi flaticon-minisplit',
			66 => 'fi flaticon-minisplit',
			67 => 'fi flaticon-minisplit',
			68 => 'fi flaticon-minisplit',
			69 => 'fi flaticon-minisplit',
			70 => 'fi flaticon-minisplit',
			71 => 'fi flaticon-minisplit',
			72 => 'fi flaticon-minisplit',
			73 => 'fi flaticon-minisplit',
			75 => 'fi flaticon-minisplit',
			76 => 'fi flaticon-minisplit',
			77 => 'fi flaticon-minisplit',
			78 => 'fa fa-lightbulb-o',
			79 => 'fa fa-question',
			80 => 'fa fa-volume-up',
			81 => 'fa fa-question',
			82 => 'fa fa-question',
			83 => 'fa fa-question'
	);
	
	return $icons[$iddevice];
}

function display_widget($info){
	
	$widget = array(
			2  => "display_cam",
			3  => "display_lampe",
			4  => "display_lampe",
			5  => "display_commande",
			6  => "display_lampe",
			7  => "display_warming",
			8  => "display_warming",
			9  => "display_warming",
			10 => "display_shutter",
			11 => "display_shutter",
			12 => "display_clim",
			13 => "display_clim",
			14 => "display_audio",
			15 => "display_audio",
			17 => "display_audio",
			18 => "display_garden",
			19 => "display_aspiration",
			20 => "display_furnace",
			21 => "display_furnace",
			22 => "display_garden",
			23 => "display_garden",
			24 => "display_warming",
			25 => "display_fan",
			26 => "display_fan",
			27 => "display_spa",
			28 => "display_spa",
			29 => "display_cam",
			30 => "display_alarm",
			31 => "display_portal",
			32 => "display_portal",
			33 => "display_furnace",
			34 => "display_clim",
			38 => "display_commande",
			43 => "display_commande",
			47 => "display_consumption",
			48 => "display_commande",
			49 => "display_warming",
			50 => "display_audio",
			51 => "display_commande",
			52 => "display_portal",
			53 => "display_audio",
			54 => "display_portal",
			55 => "display_lampe",
			56 => "display_lampe",
			57 => "display_lampe",
			58 => "display_warming",
			59 => "display_warming",
			60 => "display_commande",
			61 => "display_commande",
			62 => "display_clim",
			63 => "display_clim",
			64 => "display_clim",
			65 => "display_clim",
			66 => "display_clim",
			67 => "display_clim",
			68 => "display_clim",
			69 => "display_clim",
			70 => "display_clim",
			71 => "display_clim",
			72 => "display_clim",
			73 => "display_clim",
			74 => "display_clim",
			75 => "display_clim",
			76 => "display_clim",
			77 => "display_clim",
			78 => "display_lampe",
			80 => "display_audio",
			81 => "display_cam"
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
			<div class="info-widget">
				<button title="'._('More').'"
				        onclick="HandlePopup(2, '.$info->room_device_id.')"
				        class="btn btn-greenleaf"
				        type="button">
				        <span class="fa fa-key md"></span>
				</button>
			</div>
			<h3 class="title">'.$info->name.'</h3>';
	
	return $display;
}

// Widget fan
function display_fan($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}

	if (!empty($info->device_opt->{400}) || !empty($info->device_opt->{401}) || !empty($info->device_opt->{402}) ||
	    !empty($info->device_opt->{403}) || !empty($info->device_opt->{404}) || !empty($info->device_opt->{405}) || !empty($info->device_opt->{406})){
		$display.= '<select class="form-control center" onchange="changeSpeedFan('.$info->room_device_id.', 1)" id="speed-fan">';
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
		$display.= '</select>';
	}

	return $display;
}

// Widget warming
function display_warming($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
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
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';

	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}

	return $display;
}

// Widget Spa
function display_spa($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

// Widget Clim
function display_clim($info){
	$display = '';
	$display.= '<div class="info-widget"><button title="'._('More').'" onclick="HandlePopup(4, '.$info->room_device_id.')" class="btn btn-greenleaf" type="button"><span class="fa fa-plus md"></span></button></div>
				<h3 class="title">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	if (!empty($info->device_opt->{388})){
		$display.=display_minusplus($info);
	}
	
	return $display;
}

// Widget Aspiration
function display_aspiration($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

// widget Audio
function display_audio($info){
	$display = '';

	switch($info->protocol_id){
		case 1:
			// KNX
			break;
		case 6:
			$display = '<div class="info-widget"><button title="'._('More').'" onclick="HandlePopup(1, '.$info->room_device_id.')" class="btn btn-greenleaf" type="button"><span class="fa fa-plus md"></span></button></div>
				<h3 class="title">'.$info->name.'</h3>
				<div class="btn-group margin-bottom center">';
			if (!empty($info->device_opt->{364})){
				$display.='<button onclick="RemoteAudio(\'pause\', \''.$info->room_device_id.'\', \''.$info->device_opt->{364}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-pause"></span></button>';
			}
			if (!empty($info->device_opt->{363})){
				$display.='<button onclick="RemoteAudio(\'play\', \''.$info->room_device_id.'\', \''.$info->device_opt->{363}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-play"></span></button>';
			}
			if (!empty($info->device_opt->{368})){
				$display.=' <button onclick="RemoteAudio(\'mute\', \''.$info->room_device_id.'\', \''.$info->device_opt->{368}->option_id.'\')" class="btn btn-info">
										<span id="icon-mute" class="glyphicon glyphicon-volume-off"></span>
									</button>';
			}
			$display.='</div>';
			break;
		default :
			// TODO
			// infra rouge
			break;
	}

	if (!empty($info->device_opt->{383})){
		$display.='
				<div class="col-xs-12">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cursor"
					     onclick="Volume(\''.$info->room_device_id.'\', \''.$info->device_opt->{383}->option_id.'\', -1)">
						<i class="glyphicon glyphicon-volume-down"></i>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<output id="vol-'.$info->room_device_id.'" for="volume-'.$info->room_device_id.'">
							50%
						</output>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cursor"
					     onclick="Volume(\''.$info->room_device_id.'\', \''.$info->device_opt->{383}->option_id.'\', 1)">
						<i class="glyphicon glyphicon-volume-up"></i>
					</div>
					<div class="row">
						<input onchange="SetVolume(\''.$info->room_device_id.'\', \''.$info->device_opt->{383}->option_id.'\')"
						       value="50" min="0" step="1" max="100" id="volume-'.$info->room_device_id.'"
						       oninput="UpdateVol(\''.$info->room_device_id.'\', value)" type="range">
					</div>
				</div>';
	}
	return $display;
}

//widget portail
function display_portal($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12}) or !empty($info->device_opt->{96})){
		$display.=display_OpenClose($info);
	}
	
	return $display;
}

//widget store
function display_shutter($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{54})){
		$display.=display_UpDown($info);
	}
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	if (!empty($info->device_opt->{13})){
		$display.=display_varie($info, 2);
	}
	return $display;
}

//widget commande
function display_commande($info){
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{79})){
		$display.=display_luminosity($info);
	}
	if (!empty($info->device_opt->{72})){
		$display.= display_temperature($info);
	}
	if (!empty($info->device_opt->{6})){
		$display.=display_hygrometry($info);
	}

	return $display;
}

//widget lampe
function display_lampe($info){
	if (!empty($info->device_id) && $info->device_id == 78){
		$display = '<div class="info-widget">
						<button title="'._('More').'" onclick="HandlePopup(3, '.$info->room_device_id.')" class="btn btn-greenleaf" type="button">
							<span class="fa fa-plus md"></span>
						</button>
					</div>
					<h3 class="title">'.$info->name.'</h3>';
	}
	
	else{
		/*
		$display = '<div class="info-widget">
						<button title="'._('More').'"
								onclick="HandlePopup(2, '.$info->room_device_id.')"
								class="btn btn-greenleaf"
								type="button">
									<span class="fa fa-info-circle md"></span>
						</button>
					</div>
					<h3 class="title">'.$info->name.'</h3>';
		*/
		$display = '<h3 class="title margin-top">'.$info->name.'</h3>';
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
	$display = '<h3 class="title margin-top">'.$info->name.'</h3>
				<div>
					<button type="button" class="btn btn-info" onclick="HandlePopup(0, \''.$info->room_device_id.'\')">'._('View').'</button>
				</div>';

	return $display;
}

//widget chaudière
function display_furnace($info){
	$display= '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	if (!empty($info->device_opt->{12})){
		$display.=display_OnOff($info);
	}
	
	return $display; 
}

//widget electric consumption
function display_consumption($info){
	$display= '<h3 class="title margin-top">'.$info->name.'</h3>';
	
	//consumption option
	if (!empty($info->device_opt->{399})){
		$display.= display_consumption_option($info);
	}
	
	return $display;
}

/*   Option   */

//Open Close
function display_OpenClose($info){
	$display ='
			<div class="margin-bottom btn-group btn-group-greenleaf">
				<button type="button" class="btn btn-onoff-widget btn-primary" onclick="onOff(\''.$info->room_device_id.'\', 1, 96)">'._('Open').'</button>
				<button type="button" class="btn btn-onoff-widget btn-default" onclick="onOff(\''.$info->room_device_id.'\', 0, 96)">'._('Close').'</button>
			</div>';
	
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
			<div class="margin-bottom btn-group btn-group-greenleaf">
				<button type="button" class="btn btn-warning"
				        onclick="onOff(\''.$info->room_device_id.'\', 1, \''.$info->device_opt->{54}->option_id.'\')">
					<span class="'.$icon_up.'"></span>
				</button>';
				if (!empty($info->device_opt->{365})){
					$display.='<button type="button" class="btn btn-warning"
								       onclick="onOff(\''.$info->room_device_id.'\', 0, \''.$info->device_opt->{365}->option_id.'\')">
									<span class="'.$icon_pause.'"></span>
								</button>';
				}
	$display.='<button type="button" class="btn btn-warning"
				       onclick="onOff(\''.$info->room_device_id.'\', 0, \''.$info->device_opt->{54}->option_id.'\')">
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
				$display .= '<div class="checkbox">';
						if (!empty($info->device_opt->{12}->valeur)){
						 	$display.='<input data-on-color="greenleaf"
						 				      data-label-width="0"
										      data-on-text="'._('On').'"
										      data-off-text="'._('Off').'"
						 				      checked ';
						 						if ($popup == 0){
						 				      		$display.='id="onoff-'.$info->room_device_id.'" ';
						 						}
						 				      	else{ 
						 				      		$display.='id="onoff-popup-'.$info->room_device_id.'" ';
						 				      	}	      			
						 		   $display.='class="onoff-switch"
						 				      type="checkbox"
						 				      onchange="onOffToggle(\''.$info->room_device_id.'\', \''.$info->device_opt->{12}->option_id.'\', '.$popup.')"
						 				/>';
						}
						else {
							$display.='<input data-on-color="greenleaf"
										      data-label-width="0"
										      data-on-text="'._('On').'"
										      data-off-text="'._('Off').'" ';
						 						if ($popup == 0){
						 				      		$display.='id="onoff-'.$info->room_device_id.'" ';
						 						}
						 				      	else{ 
						 				      		$display.='id="onoff-popup-'.$info->room_device_id.'" ';
						 				      	}	      			
						 		   $display.='class="onoff-switch"
										      type="checkbox"
										      onchange="onOffToggle(\''.$info->room_device_id.'\', \''.$info->device_opt->{12}->option_id.'\', '.$popup.')"
										/>';
						}
				$display.='</div>';
			}
			else {
				$display .= '<div class="margin-bottom btn-group btn-group-greenleaf">
								<button type="button" class="btn btn-onoff-widget btn-greenleaf" onclick="onOff(\''.$info->room_device_id.'\', 1, \''.$info->device_opt->{12}->option_id.'\')">'._('On').'</button>
								<button type="button" class="btn btn-onoff-widget btn-danger" onclick="onOff(\''.$info->room_device_id.'\', 0, \''.$info->device_opt->{12}->option_id.'\')" >'._('Off').'</button>
							</div>';
			}
		break;
		// EnOcean
		case 2:
			$display .= '<div class="btn-group">
							<button type="button" class="btn btn-greenleaf">'._('On').'</button>
							<button type="button" class="btn btn-danger">'._('Off').'</button>
						</div>';
		break;
		default:
			$display.='';
		break;
	}

	$display.='<script type="text/javascript">
					$(".onoff-switch").bootstrapSwitch();
				</script>';
	return $display;
}

//Varie
function display_varie($info, $var_icon = 1){
	$display = '<div class="col-xs-12">';
	if ($var_icon == 1) {
		$left_icon = "fa-certificate";
		$right_icon = "fa-sun-o";
	}
	else if ($var_icon == 2) {
		$left_icon = "fa-sort-amount-asc";
		$right_icon = "fa-sort-amount-desc rotate-180";
	}
	switch($info->protocol_id){
		// KNX
		case 1:
				$display .= 
							'<div onclick="Variation(\''.$info->room_device_id.'\', \'13\', -1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor">
								<i class="fa '.$left_icon.'"></i>
							</div>';
							if ($info->device_opt->{13}->valeur > 0){
								$val = ceil(($info->device_opt->{13}->valeur * 100) / 255);
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
										'<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<output id="range-'.$info->room_device_id.'"
											        for="slider-value-'.$info->room_device_id.'">
												50%
											</output>
										</div>';
							}
							$display.=
										'<div onclick="Variation(\''.$info->room_device_id.'\', \'13\', 1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor">
											<i class="fa '.$right_icon.'"></i>
										</div>';
							if (!empty($info->device_opt->{13}->valeur)){
								$display.=
										'<div class="row">
											<input value="'.$info->device_opt->{13}->valeur.'" min="0" step="1" max="255"
											       oninput="outputUpdate(\''.$info->room_device_id.'\', value)"
											       onchange="getVariation(\''.$info->room_device_id.'\', \''.$info->device_opt->{13}->option_id.'\')"
											       id="slider-value-'.$info->room_device_id.'" type="range">
										</div>';
							}
							else {
								$display.=
										'<div class="row">
											<input value="128" min="0" step="1" max="255"
											       oninput="outputUpdate(\''.$info->room_device_id.'\', value)"
											       onchange="getVariation(\''.$info->room_device_id.'\', \''.$info->device_opt->{13}->option_id.'\')"
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
	$temp = $info->device_opt->{388}->valeur;
	if (empty($temp)){
		$temp = '0.0';
	}
	$display = '<div class="input-group">
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
	if (!empty($info->device_opt->{72}->valeur)){
		$tmp = $info->device_opt->{72}->valeur;
	}
	$display = '<div>
					<i class="fi flaticon-thermometer2"></i>
					<span id="widget-'.$info->room_device_id.'-'.$info->device_opt->{72}->option_id.'">'.$tmp.'</span>
					<span>'.$info->device_opt->{72}->unit.'</span>
				</div>';

	return $display;
}

//Consumption 
function display_consumption_option($info){
	$tmp = '0';
	if (!empty($info->device_opt->{399}->valeur)){
		$tmp = $info->device_opt->{399}->valeur;
	}
	$display = '<div>
					<i class="fa fa-bolt"></i>
					<span id="widget-'.$info->room_device_id.'-'.$info->device_opt->{399}->option_id.'">'.$tmp.'</span>
					<span>'.$info->device_opt->{399}->unit.'</span>
				</div>';

	return $display;
}

//Hygrometry
function display_hygrometry($info){
	$hygro = '0';
	if (!empty($info->device_opt->{6}->valeur)){
		$hygro = $info->device_opt->{6}->valeur;
	}
	$display = '<div>
					<i class="fa fa-tint"></i>
					<span id="widget-'.$info->room_device_id.'-'.$info->device_opt->{6}->option_id.'">'.$hygro.'</span> %
				</div>';

	return $display;
}

//Luminosity
function display_luminosity($info){
	$current_id = 79;
	$lum = '0';
	if (!empty($info->device_opt->{$current_id}->valeur)){
		$lum = $info->device_opt->{$current_id}->valeur;
	}
	$display = '<div>
					<i class="fa fa-sun-o"></i>
					<span id="widget-'.$info->room_device_id.'-'.$current_id.'">'.$lum.'</span>
					<span>'.$info->device_opt->{$current_id}->unit.'</span>
				</div>';

	return $display;
}

?>