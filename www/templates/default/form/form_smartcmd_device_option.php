<?php 

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['room_id_device'])
	&& !empty($_GET['id_option']) && !empty($_GET['id_exec'])
	&& !empty($_GET['modif'])) {
	showPopup($_GET['id_smartcmd'], $_GET['room_id_device'], $_GET['id_option'], $_GET['id_exec'], $_GET['modif']);
}

function showPopup($id_smartcmd, $room_id_device, $id_option, $id_exec, $modif) {
	$display = '';
	if (empty($id_option) || empty($room_id_device)) {
		return $display;
	}
	$tab_func = array(
			12 => "display_scenar_on_off",
			13 => "display_scenar_varie"
	);
	$display.='<input id="smartcmdPopupValue-'.$room_id_device.'" value="0" hidden>';
	$display.=$tab_func[$id_option]($room_id_device);
	
	$display.='
				</br>
				<div>
					<button class="btn btn-greenleaf margin-top"
					        onclick="saveSmartcmdOption('.$id_smartcmd.', '.$room_id_device.', '.$id_option.', '.$id_exec.', '.$modif.')">
						'._("Save").'
					</button>
				</div>';
	echo $display;
}

function display_scenar_on_off($room_id_device) {
	
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

function display_scenar_varie($room_id_device) {
	
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

?>