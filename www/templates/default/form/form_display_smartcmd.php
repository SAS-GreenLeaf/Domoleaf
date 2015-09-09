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
		<span class="btn btn-greenleaf disabled">Delay :</span>
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
		</div>
		<button type="button"
		        title="'._('Edit Smartcommand Element').'"
		        class="btn btn-primary btn-elem-smartcmd"
		        onclick="updateValue('.$smartcmd_id.', '.$room_device_id.','.$option_id.','.$exec_id.')">
			<i class="glyphicon glyphicon-edit"></i>
		</button>
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
		return;
	}
	$tab_func = array(
			12 => "display_option_on_off",
			13 => "display_option_varie"
	);
	
	$display.=$tab_func[$option_id]($exec_id, $room_device_id, $option_value);
	return $display;
}

function display_option_on_off($exec_id, $room_device_id, $option_value) {
	$display = '';
	$display = '<div class="checkbox">
					<input data-toggle="toggle"
					      data-onstyle="greenleaf"
					      id="smartcmdOnOff-'.$room_device_id.''.$exec_id.'" ';
							if ($option_value == 0) {
								$display.= ' checked ';
							}
							$display.= '
					      type="checkbox"
					      onchange=""/>
				</div>
			<script>
				$("#smartcmdOnOff-'.$room_device_id.''.$exec_id.'").bootstrapToggle();
				$("#smartcmdOnOff-'.$room_device_id.''.$exec_id.'").attr("disabled", "");
			</script>';
	return $display;
}

function display_option_varie($exec_id, $room_id_device, $option_value) {
	$display = '';

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
					</script>
				';

	return $display;
}

echo
'<script type="text/javascript">
		
	function updateValue(id_smartcmd, room_id_device, id_option, id_exec) {
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