<?php 

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['room_id_device'])
	&& !empty($_GET['id_option']) && !empty($_GET['id_exec'])
	&& !empty($_GET['modif'])) {
	$request =  new Api();
	$result  =  $request -> send_request();
	showPopup($_GET['id_smartcmd'], $_GET['room_id_device'], $_GET['id_option'], $_GET['id_exec'], $_GET['modif']);
}

function showPopup($id_smartcmd, $room_id_device, $id_option, $id_exec, $modif) {
	$display = '';
	if (empty($id_option) || empty($room_id_device)) {
		return $display;
	}
	$tab_func = array(
			12 => "display_smartcmd_on_off",
			13 => "display_smartcmd_varie",
			54 => "display_smartcmd_up_down",
			96 => "display_smartcmd_open_close",
			383 => "display_smartcmd_set_volume",
			388 => "display_smartcmd_set_temp",
			392 => "display_smartcmd_color_wheel",
			393 => "display_smartcmd_color_wheel",
			394 => "display_smartcmd_color_wheel",
			410 => "display_smartcmd_color_wheel"
	);
	$display.='<p class="center margin-bottom">'._('Choose the option state for this device.').'</p></br>';
	$display.='<input id="smartcmdPopupValue-'.$room_id_device.'" value="0" hidden>';
	if (empty($tab_func[$id_option])) {
		echo '
			<div class="alert alert-danger center" role="alert">
				'._('Option not available').'
			</div>';
		return;
	}
	$display.=$tab_func[$id_option]($room_id_device);
	
	$display.='
				<br/>
				<div>
					<button class="btn btn-greenleaf margin-top"
					        onclick="saveSmartcmdOption('.$id_smartcmd.', '.$room_id_device.', '.$id_option.', '.$id_exec.', '.$modif.')">
						'._("Save").'
					</button>
				</div>';
	echo $display;
}

function display_smartcmd_on_off($room_id_device) {
	
	$display = '
				<input type="checkbox"
				       data-on-color="greenleaf"
				       data-label-width="0"
				       checked
				       id="smartcmdOnOff-'.$room_id_device.'"
				       onchange="smartcmdOnOff('.$room_id_device.')">
				<script type="text/javascript">
					$("#smartcmdOnOff-"+'.$room_id_device.').bootstrapSwitch();
					smartcmdOnOff('.$room_id_device.');
				</script>';
	return $display;
}

function display_smartcmd_varie($room_id_device) {
	
	$display = '
				<div class="col-xs-6 center-div">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor"
					     onclick="Variation('.$room_id_device.', 13, -1)">
						<i class="fa fa-certificate"></i>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<output id="range-'.$room_id_device.'"
						        for="slider-value-'.$room_id_device.'">50%</output>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor"
					     onclick="Variation('.$room_id_device.', 13, 1)">
						<i class="fa fa-sun-o"></i>
					</div>
					<div class="row">
						<input value="128" min="0" step="1" max="255"
						       oninput="outputUpdate('.$room_id_device.', value)"
						       onchange="smartcmdVarie('.$room_id_device.')"
						       id="slider-value-'.$room_id_device.'"
						       type="range">
					</div>
				</div>
				<script type="text/javascript">
					smartcmdVarie('.$room_id_device.');
				</script>';
	
	return $display;
}

function display_smartcmd_up_down($room_id_device) {
	$display = '
				<input type="checkbox"
				       data-on-color="greenleaf"
				       data-label-width="0"
				       data-on-text="Up"
				       data-off-text="Down"
				       checked
				       id="smartcmdOnOff-'.$room_id_device.'"
				       onchange="smartcmdOnOff('.$room_id_device.')">
				<script type="text/javascript">
					$("#smartcmdOnOff-"+'.$room_id_device.').bootstrapSwitch();
					smartcmdOnOff('.$room_id_device.');
				</script>';
	return $display;
}

function display_smartcmd_open_close($room_id_device) {

	$display = '
				<input type="checkbox"
				       data-on-color="greenleaf"
				       data-label-width="0"
				       data-on-text="Open"
				       data-off-text="Close"
				       checked
				       id="smartcmdOnOff-'.$room_id_device.'"
				       onchange="smartcmdOnOff('.$room_id_device.')">
				<script type="text/javascript">
					$("#smartcmdOnOff-"+'.$room_id_device.').bootstrapSwitch();
					smartcmdOnOff('.$room_id_device.');
				</script>';
	return $display;
}

function display_smartcmd_set_volume($room_id_device) {
	
	$display = '
				<div class="col-xs-6 center-div">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor"
					     onclick="Volume(\''.$room_id_device.'\', 383, -1)">
						<i class="glyphicon glyphicon-volume-down"></i>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<output id="vol-'.$room_id_device.'"
						        for="volume-'.$room_id_device.'">
							50%
						</output>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cursor"
					     onclick="Volume(\''.$room_id_device.'\', 383, 1)">
						<i class="glyphicon glyphicon-volume-up"></i>
					</div>
					<div class="row">
						<input value="50" min="0" step="1" max="100"
						       oninput="UpdateVol('.$room_id_device.', value)"
						       onchange="smartcmdVolume('.$room_id_device.')"
						       id="volume-'.$room_id_device.'"
						       type="range">
					</div>
				</div>
				<script type="text/javascript">
					smartcmdVolume('.$room_id_device.');
				</script>';
	
	return $display;
}

function display_smartcmd_set_temp($room_id_device) {
	$temp = 20.0;
	
	$display =
			'<div class="input-group col-xs-6">
				<span onclick="smartcmdUpdateTemp(\''.$room_id_device.'\', -1)"
				      class="btn btn-warning input-group-addon">
					<i class="fa fa-minus md"></i>
				</span>
				<output class="margin-top-4" id="temp-'.$room_id_device.'"
				        for="output-temp-'.$room_id_device.'">
					'.$temp.'
				</output>
				<span onclick="smartcmdUpdateTemp(\''.$room_id_device.'\', 1)"
				      class="btn btn-warning input-group-addon">
					<i class="fa fa-plus md"></i>
				</span>
			</div>
			<script type="text/javascript">
				$("#smartcmdPopupValue-"+'.$room_id_device.').val('.$temp.');
			</script>';
	
	return $display;
}

function display_smartcmd_color_wheel($room_id_device) {
	$display = 
			'<form class="center padding-bottom">
				<input type="text" id="color" name="color" value="#123456" disabled="disabled" />
			</form>
			<div id="colorpicker"></div>
	
			<script type="text/javascript">
				$("#colorpicker").on("mouseup touchend", function(event) {
					smartcmdUpdateRGBColor('.$room_id_device.', $("#color").val());
				});
				$("#colorpicker").farbtastic("#color");
				smartcmdUpdateRGBColor('.$room_id_device.', $("#color").val());
			</script>';
	
	return $display;
}


?>