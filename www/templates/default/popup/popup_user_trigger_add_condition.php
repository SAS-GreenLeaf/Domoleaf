<?php

include('header.php');

$request = new Api();
$request -> add_request('confUserInstallation');
$request -> add_request('mcVisible');
$result  =  $request -> send_request();

$listAllVisible = $result->mcVisible;

$floorallowed = $listAllVisible->ListFloor;
$roomallowed = $listAllVisible->ListRoom;
$deviceallowed = $listAllVisible->ListDevice;

$installation_info = $result->confUserInstallation;

if (!empty($_GET['id_trigger'])) {
	$trigger_id = $_GET['id_trigger'];
	echo
		'<div id="errorMsg"></div>'.
		'<div class ="center">'._('Please define a new condition :').'</div>'.
		'<form class="form-inline">'.
			'<div class="input-group margin-top">'.
				'<span class="input-group-addon">'._('Floor').'</span>'.
				'<select class="selectpicker form-control" id="selectFloor-'.$trigger_id.'" data-size="10" '.
				        'onchange="listRoomsOfFloor('.$trigger_id.')">'.
					'<option value="0">'._('No Floor selected').'</option>';
					foreach ($installation_info as $floor) {
						echo '<option value="'.$floor->floor_id.'">'.$floor->floor_name.'</option>';
					}
					echo
				'</select>'.
			'</div>'.
		'</form>'.
		'<form class="form-inline">'.
			'<div class="input-group margin-top">'.
				'<span class="input-group-addon">'._('Room').'</span>'.
				'<select class="selectpicker form-control" id="selectRoom-'.$trigger_id.'" data-size="10" '.
				        'onchange="listDevicesOfRoom('.$trigger_id.')">'.
					'<option value="0">'._('No Room selected').'</option>'.
				'</select>'.
			'</div>'.
		'</form>'.
		'<form class="form-inline">'.
			'<div class="input-group margin-top">'.
				'<span class="input-group-addon">'._('Device').'</span>'.
				'<select class="selectpicker form-control" id="selectDevice-'.$trigger_id.'" data-size="10">'.
					'<option value="0">'._('No Device selected').'</option>'.
				'</select>'.
			'</div>'.
		'</form>'.
		'<div class="controls center margin-top">'.
			'<button onclick="updateSmartcmdName()" class="btn btn-success">'.
				''._('Save ').''.
				'<span class="glyphicon glyphicon-ok"></span>'.
			'</button> '.
			'<button onclick="popup_close()" class="btn btn-danger">'.
				''._('Close ').''.
				'<span class="glyphicon glyphicon-remove"></span>'.
			'</button>'.
		'</div>';
	
	echo
		'<script type="text/javascript">'.
		
		'	$(".selectpicker").selectpicker();'.
		
		'</script>';
}
?>