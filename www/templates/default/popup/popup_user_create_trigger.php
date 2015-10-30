<?php

include('header.php');

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
		'<div class ="center">'._('Please enter the Trigger name :').'</div>'.
		'<div class="input-group margin-top">'.
			'<label class="input-group-addon left" for="triggerName">'._('Name').'</label>'.
			'<input id="triggerName" name="triggerName" title="'._('Trigger Name').'" '.
			'value="" placeholder="Trigger name" type="text" class="form-control">'.
		'</div>'.
	'</div>'.
	'<div class="controls center margin-top">'.
		'<button onclick="saveNewTrigger()" class="btn btn-success">'.
			''._('Save').''.
			'<span class="glyphicon glyphicon-ok"></span>'.
		'</button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			''._('Close').''.
			'<span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
	'</div>';

echo
	'<script type="text/javascript">'.
	
		'$("#popupTitle").html("'._("New Trigger").'");'.
		
		'function saveNewTrigger() {'.
			'var name = "";'.
			
			'name = $("#triggerName").val();'.
			'name = name.trim();'.
			
			'$.ajax({'.
				'type: "GET",'.
				'url: "/templates/default/form/form_create_new_trigger.php",'.
				'data: "trigger_name="+name,'.
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
						'redirect("/profile_user_trigger_events/"+result+"/"+'.$id_scenario.');'.
					'}'.
				'}'.
			'});'.
		'}'.
		
	'</script>';
?>