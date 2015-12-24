<?php

include('header.php');
include('../function/display_widget.php');

if (!empty($_GET['id_trigger'])) {
	$request = new Api();
	$request -> add_request('getTriggerElems', array($_GET['id_trigger']));
	$result  =  $request -> send_request();
	$trigger_info = $result->getTriggerElems;

	echo '
		<div id="listTriggerElems" class="listTrigger">
			<div id="triggerElemDrop-0" class="col-xs-12 triggerElemDrop">
			</div>';
			if (!empty($trigger_info)) {
				$count = 0;
				foreach ($trigger_info as $elem) {
					echo '
					<div class="col-xs-5 triggerElem">
						<div id="triggerElem-'.$elem->condition_id.'" class="">';
							
							echo '
							<div class="center trigger-device-name">
								<i class="'.getIcon($elem->device_id).'"></i>
								'.$elem->device_name.'
							</div>
							<div id="triggerElemOption-'.$elem->condition_id.'" class="center">';
								echo display_option($elem->condition_id, $elem->option_id, $elem->option_value, $elem->room_device_id, $elem->operator);
								echo '
							</div>
						</div>';
					echo displayBtns($elem->trigger_id, $elem->condition_id, $elem->room_device_id, $elem->option_id);
					echo '</div>';
					if ($count % 2 == 1 || $elem === end($trigger_info)) {
						echo
						'<div id="triggerElemDrop-'.$elem->condition_id.'" class="col-xs-12 triggerElemDrop">
							</div>';
					}
					else {
						echo
							'<div id="triggerElemDrop-'.$elem->condition_id.'" class="col-xs-2 triggerElemDrop">
							</div>';
					}
					$count++;
				}
			}
			echo '
		</div>
		<script type="text/javascript">
			
			setElemDraggable();
					
			function setElemDraggable() {
				$(".triggerElem").draggable({
					appendTo: "#drop-conditions",
					helper: "clone",
					revert: "invalid",
					delay: 300,
					start: function() {
						dropZoneAnimate(1);
					},
					stop: function() {
						dropZoneStop(1);
					}
				});
			}
		</script>';
}

function displayBtns($trigger_id, $condition_id, $room_device_id, $option_id) {
	$without_params = [363, 364, 365, 366, 367, 368];
	
	$display ='<div class="btn-group-vertical trigger-btns">';
	if (!(in_array($option_id, $without_params))) { 
		$display.='
			<button type="button"
			        title="'._('Edit Condition').'"
			        class="btn btn-primary btn-elem-trigger margin-btn-group"
			        onclick="triggerUpdateValue('.$trigger_id.', '.$room_device_id.','.$option_id.','.$condition_id.')">
				<i class="glyphicon glyphicon-edit"></i>
			</button>';
	}
	$display.=
		'<button type="button"
		        title="'._('Delete Condition').'"
		        class="btn btn-danger btn-elem-trigger"
		        onclick="PopupRemoveTriggerElem('.$condition_id.')">
			<i class="fa fa-trash-o"></i>
		</button>
	</div>';
	
	return $display;
}

function display_option($condition_id, $option_id, $option_value, $room_device_id, $operator) {
	
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
	
	$display.=$tab_func[$option_id]($condition_id, $room_device_id, $option_value, $operator);
	return $display;
}

function display_option_on_off($condition_id, $room_device_id, $option_value) {
	$display = '<div class="checkbox">
					<input data-label-width="0"
					       data-on-color="greenleaf disabled-with-opacity"
					       data-off-color="disabled-with-opacity"
					       data-on-text="'._('On').'"
					       data-off-text="'._('Off').'"
					       id="triggerOnOff-'.$room_device_id.''.$condition_id.'" ';
					       if ($option_value == 1) {
					        	$display.= ' checked ';
					       }
					       $display.= '
					       type="checkbox"
					       disabled/>
				</div>
			<script>
				$("#triggerOnOff-'.$room_device_id.''.$condition_id.'").bootstrapSwitch();
			</script>';
	return $display;
}

function display_option_varie($condition_id, $room_id_device, $option_value) {
	$display = '
				<div class="col-xs-6 center-div">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<i class="fa fa-certificate"></i>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<output id="range-'.$room_id_device.''.$condition_id.'"
						        for="slider-value-'.$room_id_device.''.$condition_id.'">
							50%
						</output>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<i class="fa fa-sun-o"></i>
					</div>
					<div class="row">
						<input value="128" min="0" step="1" max="255"
						       id="slider-value-'.$room_id_device.''.$condition_id.'"
						       type="range"
						       disabled>
					</div>
				</div>
				<script type="text/javascript">
					$("#slider-value-'.$room_id_device.''.$condition_id.'").val('.$option_value.');
					outputUpdate('.$room_id_device.''.$condition_id.', '.$option_value.')
				</script>';

	return $display;
}

function display_option_up_down($condition_id, $room_device_id, $option_value) {
	$display = '<div class="checkbox">
					<input data-label-width="0"
					       data-on-color="greenleaf disabled-with-opacity"
					       data-off-color="disabled-with-opacity"
					       data-on-text="'._('Up').'"
					       data-off-text="'._('Down').'"
					       id="triggerUpDown-'.$room_device_id.''.$condition_id.'" ';
					       if ($option_value == 1) {
					        	$display.= ' checked ';
					       }
					       $display.= '
					       type="checkbox"
					       disabled/>
				</div>
			<script>
				$("#triggerUpDown-'.$room_device_id.''.$condition_id.'").bootstrapSwitch();
			</script>';
	return $display;
}

function display_option_open_close($condition_id, $room_device_id, $option_value) {
	$display =
				'<div class="checkbox">
					<input data-label-width="0"
					       data-on-color="greenleaf disabled-with-opacity"
					       data-off-color="disabled-with-opacity"
					       data-on-text="'._('Open').'"
					       data-off-text="'._('Close').'"
					       id="triggerOpenClose-'.$room_device_id.''.$condition_id.'" ';
					       if ($option_value == 1) {
					        	$display.= ' checked ';
							}
							$display.= '
					      type="checkbox"
					      disabled/>
				</div>
			<script>
				$("#triggerOpenClose-'.$room_device_id.''.$condition_id.'").bootstrapSwitch();
			</script>';
	return $display;
}

function display_option_play($condition_id, $room_device_id, $option_value) {
	$display ='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-play">
				</button>';
	return $display;
}

function display_option_pause($condition_id, $room_device_id, $option_value) {
	$display ='<button type="button" class="btn btn-warning disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-pause">
				</button>';
	return $display;
}

function display_option_stop($condition_id, $room_device_id, $option_value) {
	$display ='</br><button type="button" class="btn btn-warning disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-pause">
				</button></br></br>';
	return $display;
}

function display_option_next($condition_id, $room_device_id, $option_value) {
	$display ='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-forward">
				</button>';
	return $display;
}

function display_option_prev($condition_id, $room_device_id, $option_value) {
	$display ='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-backward">
				</button>';
	return $display;
}

function display_option_mute($condition_id, $room_device_id, $option_value) {
	$display ='<button type="button" class="btn btn-primary disabled-with-opacity" disabled>
					<span class="glyphicon glyphicon-volume-off">
				</button>';
	return $display;
}

function display_option_volume($condition_id, $room_id_device, $option_value) {
	$display = '
				<div class="col-xs-6 center-div">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<i class="glyphicon glyphicon-volume-down"></i>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<output id="vol-'.$room_id_device.''.$condition_id.'"
						        for="volume-'.$room_id_device.''.$condition_id.'">
							50%
						</output>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<i class="glyphicon glyphicon-volume-up"></i>
					</div>
					<div class="row">
						<input value="50" min="0" step="1" max="100"
						       id="volume-'.$room_id_device.''.$condition_id.'"
						       type="range" disabled>
					</div>
				</div>
				<script type="text/javascript">
					$("#volume-'.$room_id_device.''.$condition_id.'").val('.$option_value.');
					Volume(\''.$room_id_device.''.$condition_id.'\', '.$option_value.', 0)
				</script>';

	return $display;
}

function display_option_temp($condition_id, $room_device_id, $option_value, $operator) {
	$opList = array(
			1 => _('Greater or Equal'),
			2 => _('Lesser or Equal'),
	);
	
	$display =
				'<button type="button" class="btn btn-info disabled-with-opacity value-trigger" disabled>
					'.$opList[$operator].'
				</button>
				<button type="button" class="btn btn-info disabled-with-opacity value-trigger" disabled>
					'.$option_value.'
				</button>';
	return $display;
}

function display_option_color($condition_id, $room_device_id, $option_value) {
	$display =
			'<div class="lg trigger-color-option" id="color-'.$room_device_id.''.$condition_id.'"></div>
			<script type="text/javascript">
				$("#color-'.$room_device_id.''.$condition_id.'").css("background-color", "'.$option_value.'");
			</script>';
	return $display;
}

echo
'<script type="text/javascript">
		
	function triggerUpdateValue(id_trigger, room_id_device, id_option, id_condition) {
		$.ajax({
			type:"GET",
			url: "/templates/default/popup/popup_trigger_device_option.php",
			data: "id_trigger="+id_trigger
					+"&room_id_device="+room_id_device
					+"&id_option="+id_option
					+"&id_condition="+id_condition
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