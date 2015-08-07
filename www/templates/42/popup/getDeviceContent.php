<?php
	if ($_SERVER['REQUEST_METHOD'] != 'POST'){ exit; }
	
	include('../../../config.php');
	include('../../../functions.php');
	include('../../../libs/Link.class.php');
	
	include('../../../libs/Guest.class.php');
	include('../../../libs/User.class.php');
	include('../../../libs/Admin.class.php');
	include('../../../libs/Root.class.php');
	include('../../../libs/Api.class.php');
	
	$id = $_POST['id'];
	
	if (!is_numeric($id)){ echo json_encode(array("error"=>_('An error occured, please reload the page.'))); exit; }
	
		$api_request = new Api();
		$api_result  = $api_request -> send_request();
		
		$link = Link::get_link('mastercommand');
	
	if (!$api_request->is_co()){
		echo json_encode(array('error'=>_('You have to be logged on to do that.')));
		exit;
	}
	
	$user_id = $api_request->getId();
	
	$sql = 'SELECT room_id FROM room_device WHERE room_device_id = :room_device_id';
	$req = $link->prepare($sql);
	$req->bindValue(':room_device_id', $id, PDO::PARAM_INT);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	
	if ($req->rowCount() != 1){ echo json_encode(array('error'=>_('This device doesn\'t exist to you.'))); exit; }
	
	$do = $req->fetch(PDO::FETCH_OBJ);
	
	$sql = 'SELECT * FROM user_room WHERE user_id = :user_id AND room_id = :room_id';
	$req = $link->prepare($sql);
	$req->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
	$req->execute() or die(error_log(serialize($req->errorIngo())));
	
	if ($req->rowCount() != 1){ echo json_encode(array('error' => _('This device doesn\'t belong to you.'))); exit; }
	
	
	// script
		$link = Link::get_link('mastercommand');
		$list = array();
		
		$sql = 'SELECT room_device_id, room_device.protocol_id, room_id, 
		               room_device.device_id, room_device.name, addr, plus1, 
		               plus2, plus3, device.application_id
		        FROM room_device
		        JOIN device ON room_device.device_id = device.device_id  WHERE room_device_id = :id
		        ORDER BY name';
		$req = $link->prepare($sql);
		$req->bindValue(':id', $id, PDO::PARAM_INT);
		$req->execute() or die(error_log(serialize($req->errorIngo())));
		
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list = array(
				'application_id'=> $do->application_id,
				'room_device_id'=> $do->room_device_id,
				'room_id'       => $do->room_id,
				'device_id'     => $do->device_id,
				'protocol_id'   => $do->protocol_id,
				'name'          => $do->name,
				'addr'          => $do->addr,
				'plus1'         => $do->plus1,
				'plus2'         => $do->plus2,
				'plus3'         => $do->plus3,
				'device_opt'    => array()
			);
		}
		
		$user = new User($user_id);
		
		$sql = 'SELECT room_device.room_device_id, room_device.room_id, 
		               optiondef.hidden_arg, room_device.device_id, 
		               optiondef.option_id, room_device_option.addr,
		               if(optiondef.name'.$user->getLanguage().' = "", optiondef.name, optiondef.name'.$user->getLanguage().') as name,
		               room_device_option.addr_plus, room_device_option.valeur
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        WHERE room_device_option.status = 1 AND room_device.room_device_id = '.$id;
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if($do->hidden_arg & 4) {
				$list['device_opt'][$do->option_id] = array(
					'option_id'=> $do->option_id,
					'name'     => $do->name,
					'addr'     => $do->addr,
					'addr_plus'=> $do->addr_plus,
					'valeur'   => $do->valeur
				);
			}
		}
		
		$info = $list;
	
	$widget = array(
			2  => display_cam($info),
			3  => display_lampe($info),
			4  => display_lampe($info),
			5  => display_commande($info),
			6  => display_lampe($info),
			7  => display_warming($info),
			8  => display_warming($info),
			9  => display_warming($info),
			10 => display_shutter($info),
			11 => display_shutter($info),
			12 => display_clim($info),
			13 => display_clim($info),
			14 => display_audio($info),
			15 => display_audio($info),
			17 => display_audio($info),
			18 => display_garden($info),
			19 => display_aspiration($info),
			20 => display_furnace($info),
			21 => display_furnace($info),
			22 => display_garden($info),
			23 => display_garden($info),
			24 => display_warming($info),
			25 => display_fan($info),
			26 => display_fan($info),
			27 => display_spa($info),
			28 => display_spa($info),
			29 => display_cam($info),
			30 => display_alarm($info),
			31 => display_portal($info),
			32 => display_portal($info),
			33 => display_furnace($info),
			34 => display_clim($info),
			38 => display_commande($info),
			43 => display_commande($info),
			47 => display_consumption($info),
			48 => display_commande($info),
			49 => display_warming($info),
			50 => display_audio($info),
			51 => display_commande($info),
			52 => display_portal($info),
			53 => display_audio($info),
			54 => display_portal($info),
			55 => display_lampe($info),
			56 => display_lampe($info),
			57 => display_lampe($info),
			58 => display_warming($info),
			59 => display_warming($info),
			60 => display_commande($info),
			61 => display_commande($info),
			62 => display_clim($info),
			63 => display_clim($info),
			64 => display_clim($info),
			65 => display_clim($info),
			66 => display_clim($info),
			67 => display_clim($info),
			68 => display_clim($info),
			69 => display_clim($info),
			70 => display_clim($info),
			71 => display_clim($info),
			72 => display_clim($info),
			73 => display_clim($info),
			74 => display_clim($info),
			75 => display_clim($info),
			76 => display_clim($info),
			77 => display_clim($info),
			78 => display_lampe($info),
			80 => display_audio($info),
			81 => display_cam($info)
	);
	
	echo $widget[$info['device_id']];
	
	/*   Widget   */

// Widget alarm
function display_alarm($info){
	$display = '
			<div class="info-widget">
				
			</div>
			<h3 class="title">'.$info['name'].'</h3>';
	
	return $display;
}

// Widget fan
function display_fan($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

// Widget warming
function display_warming($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	if (!empty($info['device_opt'][388])){
		$display.=display_minusplus($info);
	}
	
	return $display;
}

// Widget garden
function display_garden($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';

	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}

	return $display;
}

// Widget Spa
function display_spa($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

// Widget Clim
function display_clim($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	if (!empty($info['device_opt'][388])){
		$display.=display_minusplus($info);
	}
	
	return $display;
}

// Widget Aspiration
function display_aspiration($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

// widget Audio
function display_audio($info){
	$display = '';

	switch($info['protocol_id']){
		case 1:
			// KNX
			break;
		case 6:
			if (!empty($info['device_opt'][383])){
			echo '
				<div class="center col-xs-12"><output id="vol-popup-'.$info['room_device_id'].'" for="volume-popup-'.$info['room_device_id'].'">50%</output></div>'.
				'<div class="center col-xs-12">'.
					'<div class="col-xs-2 cursor" onclick="Volume(\'popup-'.$info['room_device_id'].'\', \''.$info['device_opt'][383]['option_id'].'\', -1)"><i class="glyphicon glyphicon-volume-down"></i></div>'.
					'<div class="col-xs-8" ><input onchange="SetVolume(\'popup-'.$info['room_device_id'].'\', \''.$info['device_opt'][383]['option_id'].'\')" value="50" min="0" step="1" max="100" id="volume-popup-'.$info['room_device_id'].'" oninput="UpdateVol(\'popup-'.$info['room_device_id'].'\', value)" type="range"></div>'.
					'<div class="col-xs-2 cursor" onclick="Volume(\'popup-'.$info['room_device_id'].'\', \''.$info['device_opt'][383]['option_id'].'\', 1)"><i class="glyphicon glyphicon-volume-up"></i></div>'.
				'</div>'.
			 '<div class="clearfix"></div>&nbsp;';
		}
		echo	'<div class="center">'.
			 '<div class="btn-group">';
		if (!empty($info['device_opt'][367])){
			echo '<button onclick="RemoteAudio(\'prev\', \''.$info['room_device_id'].'\', \''.$info['device_opt'][367]['option_id'].'\')" class="btn btn-info"><span class="glyphicon glyphicon-backward"></span></button>';
		}
		if (!empty($info['device_opt'][364])){
			echo '<button onclick="RemoteAudio(\'pause\', \''.$info['room_device_id'].'\', \''.$info['device_opt'][364]['option_id'].'\')" class="btn btn-info"><span class="glyphicon glyphicon-pause"></span></button>';
		}
		if (!empty($info['device_opt'][363])){
		 echo '<button onclick="RemoteAudio(\'play\', \''.$info['room_device_id'].'\', \''.$info['device_opt'][363]['option_id'].'\')" class="btn btn-info"><span class="glyphicon glyphicon-play"></span></button>';
		}
		if (!empty($info['device_opt'][365])){
			echo '<button onclick="RemoteAudio(\'stop\', \''.$info['room_device_id'].'\', \''.$info['device_opt'][365]['option_id'].'\')" class="btn btn-info"><span class="glyphicon glyphicon-stop"></span></button>';
		}
		if (!empty($info['device_opt'][368])){
			echo '<button onclick="RemoteAudio(\'mute\', \''.$info['room_device_id'].'\', \''.$info['device_opt'][368]['option_id'].'\')" class="btn btn-info"><span class="glyphicon glyphicon-volume-off"></span></button>';
		}
		if (!empty($info['device_opt'][366])){
			echo '<button onclick="RemoteAudio(\'next\', \''.$info['room_device_id'].'\', \''.$info['device_opt'][366]['option_id'].'\')" class="btn btn-info"><span class="glyphicon glyphicon-forward"></span></button>';
		}
	 echo '</div>'.
		'</div>
		<div class="center">'.
			'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
			break;
		default :
			// TODO
			// infra rouge
			break;
	}
	
	return $display;
}

//widget portail
function display_portal($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][96])){
		$display.=display_OpenClose($info);
	}
	
	return $display;
}

//widget store
function display_shutter($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][54])){
		$display.=display_UpDown($info);
	}
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	
	return $display;
}

//widget commande
function display_commande($info){
	$display = '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][72])){
		$display.=display_temperature($info);
	}
	if (!empty($info['device_opt'][6])){
		$display.=display_hygrometry($info);
	}
	if (!empty($info['device_opt'][79])){
		$display.=display_luminosity($info);
	}
	return $display;
}

//widget lampe
function display_lampe($info){
	if (!empty($info['device_id']) && $info['device_id'] == 3){
		$display ='<div id="colorpicker"></div>'.
'<form><input type="text" id="color" name="color" value="#123456" disabled="disabled" /></form>'.
'<script type="text/javascript">'.
'$("#colorpicker").farbtastic("#color");'.
'</script>';
	}
	else
	{
		$display = '<div class="info-widget"></div>
					<h3 class="title">'.$info['name'].'</h3>';
	}

	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	if (!empty($info['device_opt'][13])){
		$display.=display_varie($info);
	}
	
	$display.=display_ChromatiqueWheel($info);

	return $display;
}

//widget camera
function display_cam($info){
	return 'CALL::popup_camera_view.php';
}

//widget chaudière
function display_furnace($info){
	$display= '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	if (!empty($info['device_opt'][12])){
		$display.=display_OnOff($info);
	}
	
	return $display; 
}

//widget consommation électrique
function display_consumption($info){
	$display= '<h3 class="title margin-top">'.$info['name'].'</h3>';
	
	return $display;
}

/*   Option   */

// Chromatique wheel
function display_ChromatiqueWheel($info){
	$display = '';
	
	return $display;
}

//Open Close
function display_OpenClose($info){
	$display ='
			<div class="margin-bottom btn-group btn-group-greenleaf">
				<button type="button" class="btn btn-onoff-widget btn-primary" onclick="onOff(\''.$info['room_device_id'].'\', 1, \''.$info['device_opt'][96]['option_id'].'\')">'._('Open').'</button>
				<button type="button" class="btn btn-onoff-widget btn-default" onclick="onOff(\''.$info['room_device_id'].'\', 0, \''.$info['device_opt'][96]['option_id'].'\')">'._('Close').'</button>
			</div>';
	
	return $display;
}

//Up Down
function display_UpDown($info){
	$display ='
			<div class="margin-bottom btn-group btn-group-greenleaf">
				<button type="button" class="btn btn-warning" onclick="onOff(\''.$info['room_device_id'].'\', 1, \''.$info['device_opt'][54]['option_id'].'\')"><span class="fa fa-angle-double-up lg"></span></button>';
				if (!empty($info['device_opt'][365])){
					$display.='<button type="button" class="btn btn-warning" onclick="onOff(\''.$info['room_device_id'].'\', 0, \''.$info['device_opt'][365]['option_id'].'\')" ><span class="fa fa-pause lg"></button>';
				}
	$display.='<button type="button" class="btn btn-warning" onclick="onOff(\''.$info['room_device_id'].'\', 0, \''.$info['device_opt'][54]['option_id'].'\')" ><span class="fa fa-angle-double-down lg"></button>
			</div>';
	
	return $display;
}

// On/Off
function display_OnOff($info){
	$display = '';
	switch($info['protocol_id']){
		// KNX
		case 1:
			if (!empty($info['device_opt'][12]['addr_plus'])){
				$display .= '<div class="checkbox">';
						if (!empty($info['device_opt'][12]['valeur'])){
						 	$display.='<input data-toggle="toggle" data-onstyle="greenleaf" checked id="onoff-'.$info['room_device_id'].'" type="checkbox" onchange="onOffToggle(\''.$info['room_device_id'].'\', \''.$info['device_opt'][12]['option_id'].'\')" />';
						}
						else {
						$display.='<input data-toggle="toggle" data-onstyle="greenleaf" id="onoff-'.$info['room_device_id'].'" type="checkbox" onchange="onOffToggle(\''.$info['room_device_id'].'\', \''.$info['device_opt'][12]['option_id'].'\')" />';
						}
				$display.='</div>';
			}
			else {
				$display .= '<div class="margin-bottom btn-group btn-group-greenleaf">
								<button type="button" class="btn btn-onoff-widget btn-greenleaf" onclick="onOff(\''.$info['room_device_id'].'\', 1, \''.$info['device_opt'][12]['option_id'].'\')">'._('On').'</button>
								<button type="button" class="btn btn-onoff-widget btn-danger" onclick="onOff(\''.$info['room_device_id'].'\', 0, \''.$info['device_opt'][12]['option_id'].'\')" >'._('Off').'</button>
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
	return $display;
}

//Varie
function display_varie($info){
	$display = '<div class="col-xs-12">';
	switch($info['protocol_id']){
		// KNX
		case 1:
				$display .= '<div onclick="Variation(\''.$info['room_device_id'].'\', \'13\', -1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor">
								<i class="fa fa-certificate"></i>
							</div>';
					if ($info['device_opt'][13]['valeur'] > 0){
						$val = ceil(($info['device_opt'][13]['valeur']*100)/255);
							$display.='<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<output id="range-'.$info['room_device_id'].'" for="slider-value-'.$info['room_device_id'].'">'.$val.'%</output>
										</div>';
					}
					else {
						$display.='<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
									  <output id="range-'.$info['room_device_id'].'" for="slider-value-'.$info['room_device_id'].'">50%</output>
								   </div>';
					}
					$display.= '<div onclick="Variation(\''.$info['room_device_id'].'\', \'13\', 1)" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor">
									<i class="fa fa-sun-o"></i>
								</div>';
					if (!empty($info['device_opt'][13]['valeur'])){
						$display.='<div class="row">
									<input value="'.$info['device_opt'][13]['valeur'].'" min="0" step="1" max="255" oninput="outputUpdate(\''.$info['room_device_id'].'\', value)" onchange="getVariation(\''.$info['room_device_id'].'\', \''.$info['device_opt'][13]['option_id'].'\')" id="slider-value-'.$info['room_device_id'].'" type="range">
								   </div>';
					}
					else {
						$display.='<div class="row">
									<input value="128" min="0" step="1" max="255" oninput="outputUpdate(\''.$info['room_device_id'].'\', value)" onchange="getVariation(\''.$info['room_device_id'].'\', \''.$info['device_opt'][13]['option_id'].'\')" id="slider-value-'.$info['room_device_id'].'" type="range">
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

function display_minusplus($info){
	$temp = $info['device_opt'][388]['valeur'];
	if (empty($temp)){
		$temp = '0.0';
	}
	$display = '<div class="input-group">
					<span onclick="UpdateTemp(\''.$info['room_device_id'].'\', 388, -1)" class="btn btn-warning input-group-addon"><i class="fa fa-minus md"></i></span>
					<output class="margin-top-4" id="output-mp-'.$info['room_device_id'].'">'.$temp.'</output>
					<span onclick="UpdateTemp(\''.$info['room_device_id'].'\', 388, 1)" class="btn btn-warning input-group-addon"><i class="fa fa-plus md"></i></span>
			    </div>';
	
	return  $display;
}

//Temperature
function display_temperature($info){
	$tmp = '0';
	if (!empty($info['device_opt'][72]['valeur'])){
		$tmp = $info['device_opt'][72]['valeur'];
	}
	$display = '<div>
					<i class="fi flaticon-thermometer2"></i>
					<span id="widget-'.$info['room_device_id'].'-'.$info['device_opt'][72]['option_id'].'">'.$tmp.'</span>
					<span>'.$info['device_opt'][72]['addr_plus'].'</span>
				</div>';

	return $display;
}

//Hygrometry
function display_hygrometry($info){
	$hygro = '0';
	if (!empty($info['device_opt'][6]['valeur'])){
		$hygro = $info['device_opt'][6]['valeur'];
	}
	$display = '<div>
					<i class="fa fa-tint"></i>
					<span id="widget-'.$info['room_device_id'].'-'.$info['device_opt'][6]['option_id'].'">'.$hygro.'</span> %
				</div>';

	return $display;
}

//Luminosity
function display_luminosity($info){
	$current_id = 79;
	$lum = '0';
	if (!empty($info['device_opt'][$current_id]['valeur'])){
		$lum = $info['device_opt'][$current_id]['valeur'];
	}
	$display = '<div>
					<i class="fa fa-sun-o"></i>
					<span id="widget-'.$info['room_device_id'].'-'.$current_id.'">'.$lum.'</span>
					<span>'.$info['device_opt'][$current_id]['addr_plus'].'</span>
				</div>';

	return $display;
}

?>