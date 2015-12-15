<?php

include('header.php');

$request =  new Api();
$request -> add_request('confUserInstallation');
$result  =  $request -> send_request();

$installation_info = $result->confUserInstallation;

if (empty($_GET['id_scenario'])) {
	$id_scenario = 0;
}
else {
	$id_scenario = $_GET['id_scenario'];
}

echo
	'<div>'.
		'<div id="popupError" class="alert alert-danger alert-dismissible center" role="alert" hidden>'.
			'<p id="errorMsg"><p>'.
		'</div>'.
		'<div class ="center"><b>'._('Please enter the Smartcommand name :').'</b></div>'.
		'<div class="input-group margin-top">'.
			'<label class="input-group-addon left" for="smartcmdName">'._('Name').'</label>'.
			'<input id="smartcmdName" name="smartcmdName" title="'._('Smartcommand Name').'" '.
			'value="" placeholder="Smartcommand name" type="text" class="form-control">'.
		'</div>'.
	'</div>'.
	'</br>'.
	'<p class="center"><b>'._('Please choose the Smartcommand\'s Linked Room :
			(no Room selected = Smartcommand not visible)').'</b></p>'.
	'<div class="center">'.
		'<select class="selectpicker span2 margin-right" id="selectFloor-0" data-size="10"'.
			'onchange="listRoomsOfFloor(0, 1)">'.
			'<option value="0">'._('No floor').'</option>';
			foreach ($installation_info as $floor) {
				echo '<option value="'.$floor->floor_id.'">'.$floor->floor_name.'</option>';
			}
			echo
		'</select>'.
		'<select class="selectpicker span2" id="selectRoom-0" data-size="10">'.
			'<option value="0">'._('No floor selected').'</option>'.
		'</select>'.
	'</div>'.
	'</br>'.
	'<div class="controls center">'.
		'<button onclick="saveNewSmartcommand()" class="btn btn-success">'.
			_('Save').
			' <span class="glyphicon glyphicon-ok"></span>'.
		'</button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('Close').
			' <span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	'</div>';

if (!empty($installation_info)) {
	$first_floor = reset($installation_info)->floor_id;
}
else {
	$first_floor = 0;
}

echo
	'<script type="text/javascript">'.

		'$(document).ready(function(){'.
			'$("#popupTitle").html("'._("New Smartcommand").'");'.
			'setTimeout(function(){'.
				'$("#smartcmdName").focus();'.
			'}, 400);'.
			'$(".selectpicker").selectpicker();'.
			'setDefaultRoom('.$first_floor.')'.
		'});'.

		'function saveNewSmartcommand() {'.
			'var name = "";'.
			
			'name = $("#smartcmdName").val();'.
			'name = name.trim();'.
			'room_id = parseInt($("#selectRoom-0").val());'.
			
			'$.ajax({'.
				'type: "GET",'.
				'url: "/templates/default/form/form_create_new_smartcmd.php",'.
				'data: "smartcmd_name="+encodeURIComponent(name)'.
						'+"&room_id="+room_id,'.
				'success: function(result) {'.
					'if (result && result == -1) {'.
						'$("#popupError").show();'.
						'$("#errorMsg").html("'._('Name already existing').'");'.
					'}'.
					'else if (result && result == -2) {'.
						'$("#popupError").show();'.
						'$("#errorMsg").html("'._('Invalid Name').'");'.
					'}'.
					'else if (result) {'.
						'popup_close();'.
						'redirect("/profile_user_smartcmd/"+result+"/"+'.$id_scenario.');'.
					'}'.
				'}'.
			'});'.
		'}'.
		
		'function setDefaultRoom(floor_id) {'.
			'$("#selectFloor-0").selectpicker(\'val\', floor_id);'.
			'listRoomsOfFloor(0, 1);'.
			'setTimeout(function() {'.
						'room_id = $("#selectRoom-0 .option_ok").first().val();'.
						'$("#selectRoom-0").selectpicker(\'val\', room_id);'.
					'}, 200);'.
		'}'.
		
	'</script>';
?>