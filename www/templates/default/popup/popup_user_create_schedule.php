<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

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
		'<div class ="center">'._('Please enter the Schedule name :').'</div>'.
		'<div class="input-group margin-top">'.
			'<label class="input-group-addon left" for="scheduleName">'._('Name').'</label>'.
			'<input id="scheduleName" name="scheduleName" title="'._('Schedule Name').'" '.
			'value="" placeholder="Schedule name" type="text" class="form-control">'.
		'</div>'.
	'</div>'.
	'<div class="controls center margin-top">'.
		'<button onclick="saveNewSchedule()" class="btn btn-success">'.
			_('Save').
			' <span class="glyphicon glyphicon-ok"></span>'.
		'</button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('Close').
			' <span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	'</div>';

echo
	'<script type="text/javascript">'.
	
	'$(document).ready(function(){'.
		'$("#popupTitle").html("'._("New Schedule").'");'.
		'setTimeout(function(){'.
			'$("#scheduleName").focus();'.
		'}, 400);'.
	'});'.
	
	'function saveNewSchedule() {'.
		'var name = "";'.
		
		'name = $("#scheduleName").val();'.
		'name = name.trim();'.
		
		'$.ajax({'.
			'type: "GET",'.
			'url: "/templates/default/form/form_create_new_schedule.php",'.
			'data: "schedule_name="+encodeURIComponent(name),'.
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
					'redirect("/profile_user_trigger_schedules/"+result+"/"+'.$id_scenario.');'.
				'}'.
			'}'.
		'});'.
	'}'.
	'</script>';
?>