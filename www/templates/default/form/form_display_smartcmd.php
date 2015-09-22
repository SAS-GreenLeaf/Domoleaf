<?php

include('header.php');
include('../function/display_widget.php');

if (!empty($_GET['id_smartcmd'])) {
	$request = new Api();
	$request -> add_request('getSmartcmdElems', array($_GET['id_smartcmd']));
	$result  =  $request -> send_request();
	$smartcmd_info = $result->getSmartcmdElems;
	
	echo '
		<div id="listSmartcmdElems" class="listSmartcmd">
			<div id="smartcmdElemDrop-0" class="col-xs-12 smartcmdElemDrop">
			</div>';
			if (!empty($smartcmd_info)) {
				foreach ($smartcmd_info as $elem) {
					echo '
					<div class="col-xs-12 smartcmdElem">
						<div id="smartcmdElem-'.$elem->exec_id.'" class="">';
							displayDelay($elem->smartcmd_id, $elem->time_lapse, $elem->exec_id, $elem->room_device_id, $elem->option_id);
							echo '
							<div class="col-xs-6 right smartcmd-device-name">
								<i class="'.getIcon($elem->device_id).'"></i>
								'.$elem->device_name.'
							</div>
							<div id="smartcmdElemOption-'.$elem->exec_id.'" class="col-xs-6 left">';
								echo display_option($elem->exec_id, $elem->option_id, $elem->option_value, $elem->room_device_id);
								echo '
							</div>
						</div>
					</div>
					<div id="smartcmdElemDrop-'.$elem->exec_id.'" class="col-xs-12 smartcmdElemDrop">
					</div>';
				}
			}
			echo '
		</div>
		<script type="text/javascript">
			
			setElemDraggable();
					
			function setElemDraggable() {
				$(".smartcmdElem").draggable({
					appendTo: "#drop-smartcmd",
					helper: "clone",
					revert: "invalid",
					delay: 300,
					start: function() {
						dropZoneAnimate();
					},
					stop: function() {
						dropZoneStop();
					}
				});
			}
		</script>';
}


function displayDelay($smartcmd_id, $delay, $exec_id, $room_device_id, $option_id) {
	$without_params = [363, 364, 365, 366, 367, 368];
	
	echo '
		<script type="text/javascript">
			$(".selectpicker").selectpicker();
		</script>';
			
	if (!empty($delay)) {
		$hours = (int) ($delay / 3600);
		$minutes = (int) (($delay - $hours * 3600) / 60);
		$secondes = (int) ($delay - $hours * 3600 - $minutes * 60);
		echo '
		<script type="text/javascript">
			$("#selectHours-'.$exec_id.'").selectpicker("val", "'.$hours.'");
			$("#selectMinutes-'.$exec_id.'").selectpicker("val", "'.$minutes.'");
			$("#selectSeconds-'.$exec_id.'").selectpicker("val", "'.$secondes.'");
		</script>';
	}
	echo '
	<div class="delay">
		<span class="btn btn-greenleaf disabled-with-opacity" disabled>Delay :</span>
		<div class="timePicker">
			<select class="selectpicker" id="selectHours-'.$exec_id.'" data-width="auto" data-size="10"
			        onchange="selectDelay('.$smartcmd_id.', '.$exec_id.')">';
				for ($i = 0; $i < 24; $i++) {
					echo '<option value="'.$i.'">'.$i.'h</option>';
				}
				echo '
			</select>
			<select class="selectpicker" id="selectMinutes-'.$exec_id.'" data-width="auto" data-size="10"
			        onchange="selectDelay('.$smartcmd_id.', '.$exec_id.')">';
				for ($i = 0; $i < 60; $i++) {
					echo '<option value="'.$i.'">'.$i.'min</option>';
				}
				echo '
			</select>
			<select class="selectpicker" id="selectSeconds-'.$exec_id.'" data-width="auto" data-size="10"
			        onchange="selectDelay('.$smartcmd_id.', '.$exec_id.')">';
				for ($i = 0; $i < 60; $i++) {
					echo '<option value="'.$i.'">'.$i.'s</option>';
				}
				echo '
			</select>
		</div>';
		if (!(in_array($option_id, $without_params))) { 
			echo '
				<button type="button"
				        title="'._('Edit Smartcommand Element').'"
				        class="btn btn-primary btn-elem-smartcmd"
				        onclick="smartcmdUpdateValue('.$smartcmd_id.', '.$room_device_id.','.$option_id.','.$exec_id.')">
					<i class="glyphicon glyphicon-edit"></i>
				</button>';
		}
		echo '
			<button type="button"
			        title="'._('Delete Smartcommand Element').'"
			        class="btn btn-danger btn-elem-smartcmd"
			        onclick="PopupRemoveSmartcmdElem('.$exec_id.')">
				<i class="fa fa-trash-o"></i>
			</button>
	</div>';
				
}

function display_option($exec_id, $option_id, $option_value, $room_device_id) {
	
	$display = '';
	if (empty($option_id)) {
		return $display;
	}
	$tab_func = array(
			12 => "display_option_on_off",
			13 => "display_option_varie",
			54 => "display_option_up_down",
			96 => "display_option_open_close",
			363 => "display_option_play",
			364 => "display_option_pause",
			365 => "display_option_stop",
			366 => "display_option_next",
			367 => "display_option_prev",
			368 => "display_option_mute",
			383 => "display_option_volume",
			388 => "display_option_temp",
			392 => "display_option_color",
			393 => "display_option_color",
			394 => "display_option_color"
	);
	
	$display.=$tab_func[$option_id]($exec_id, $room_device_id, $option_value);
	return $display;
}

function display_option_on_off($exec_id, $room_device_id, $option_value) {
	$display = '<div class="checkbox">
					<input data-label-width="0"
					       data-on-color="greenleaf disabled-with-opacity"
					       data-off-color="disabled-with-opacity"
					       data-on-text="On"
					       data-off-text="Off"
					       id="smartcmdOnOff-'.$room_device_id.''.$exec_id.'" ';
					       if ($option_value == 1) {
					        	$display.= ' checked ';
					       }
					       $display.= '
					       type="checkbox"
					       disabled/>
				</div>
			<script>
				$("#smartcmdOnOff-'.$room_device_id.''.$exec_id.'").bootstrapSwitch();
			</script>';
	return $display;
}

function display_option_varie($exec_id, $room_id_device, $option_value) {
	$display = '
				<div class="col-xs-6 center-div">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<i class="fa fa-certificate"></i>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<output id="range-'.$room_id_device.''.$exec_id.'"
						        for="slider-value-'.$room_id_device.''.$exec_id.'">
							50%
						</output>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<i class="fa fa-sun-o"></i>
					</div>
					<div class="row">
						<input value="128" min="0" step="1" max="255"
						       id="slider-value-'.$room_id_device.''.$exec_id.'"
						       type="range"
						       disabled>
					</div>
				</div>
				<script type="text/javascript">
					$("#slider-value-'.$room_id_device.''.$exec_id.'").val('.$option_value.');
					outputUpdate('.$room_id_device.''.$exec_id.', '.$option_value.')
				</script>';

	return $display;
}

function display_option_up_down($exec_id, $room_device_id, $option_value) {
	$display = '<div class="checkbox">
					<input data-label-width="0"
					       data-on-color="greenleaf disabled-with-opacity"
					       data-off-color="disabled-with-opacity"
					       data-on-text="Up"
					       data-off-text="Down"
					       id="smartcmdUpDown-'.$room_device_id.''.$exec_id.'" ';
					       if ($option_value == 1) {
					        	$display.= ' checked ';
					       }
					       $display.= '
					       type="checkbox"
					       disabled/>
				</div>
			<script>
				$("#smartcmdUpDown-'.$room_device_id.''.$exec_id.'").bootstrapSwitch();
			</script>';
	return $display;
}

function display_option_open_close($exec_id, $room_device_id, $option_value) {
	$display = '<div class="checkbox">
					<input data-label-width="0"
					       data-on-color="greenleaf disabled-with-opacity"
					       data-off-color="disabled-with-opacity"
					       data-on-text="Open"
					       data-off-text="Close"
					       id="smartcmdOpenClose-'.$room_device_id.''.$exec_id.'" ';
					       if ($option_value == 1) {
					        	$display.= ' checked ';
							}
							$display.= '
					      type="checkbox"
					      disabled/>
				</div>
			<script>
				$("#smartcmdOpenClose-'.$room_device_id.''.$exec_id.'").bootstrapSwitch();
			</script>';
	return $display;
}

function display_option_play($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-play">
				</button>';
	return $display;
}

function display_option_pause($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-pause">
				</button>';
	return $display;
}

function display_option_stop($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-stop">
				</button>';
	return $display;
}

function display_option_next($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-forward">
				</button>';
	return $display;
}

function display_option_prev($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-backward">
				</button>';
	return $display;
}

function display_option_mute($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-volume-off">
				</button>';
	return $display;
}

function display_option_volume($exec_id, $room_id_device, $option_value) {
	$display = '
				<div class="col-xs-6 center-div">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<i class="glyphicon glyphicon-volume-down"></i>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<output id="vol-'.$room_id_device.''.$exec_id.'"
						        for="volume-'.$room_id_device.''.$exec_id.'">
							50%
						</output>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<i class="glyphicon glyphicon-volume-up"></i>
					</div>
					<div class="row">
						<input value="50" min="0" step="1" max="100"
						       id="volume-'.$room_id_device.''.$exec_id.'"
						       type="range" disabled>
					</div>
				</div>
				<script type="text/javascript">
					$("#volume-'.$room_id_device.''.$exec_id.'").val('.$option_value.');
					Volume(\''.$room_id_device.''.$exec_id.'\', '.$option_value.', 0)
				</script>';

	return $display;
}

function display_option_temp($exec_id, $room_device_id, $option_value) {
	$display.='<button type="button" class="btn btn-info disabled-with-opacity btn-lg" disabled>
					'.$option_value.'
				</button>';
	return $display;
}

function display_option_color($exec_id, $room_device_id, $option_value) {
	$display =
			'<div class="lg smartcmd-color-option" id="color-'.$room_device_id.''.$exec_id.'"></div>
			<script type="text/javascript">
				$("#color-'.$room_device_id.''.$exec_id.'").css("background-color", "'.$option_value.'");
			</script>';
	return $display;
}

echo
'<script type="text/javascript">
		
	function smartcmdUpdateValue(id_smartcmd, room_id_device, id_option, id_exec) {
		$.ajax({
			type:"GET",
			url: "/templates/default/popup/popup_smartcmd_device_option.php",
			data: "id_smartcmd="+id_smartcmd
					+"&room_id_device="+room_id_device
					+"&id_option="+id_option
					+"&id_exec="+id_exec
					+"&modif="+2,
			success: function(msg) {
				BootstrapDialog.show({
					title: \'<div id="popupTitle" class="center"></div>\',
					message: msg
				});
			}
		});
	}
</script>'

?>