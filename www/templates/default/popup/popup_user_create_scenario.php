<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

echo
	'<div>'.
		'<div id="popupError" class="alert alert-danger alert-dismissible center" role="alert" hidden>'.
			'<p id="errorMsg"><p>'.
		'</div>'.
		'<div class ="center">'._('Please enter the Scenario name :').'</div>'.
		'<div class="input-group margin-top">'.
			'<label class="input-group-addon left" for="scenarioName">'._('Name').'</label>'.
			'<input id="scenarioName" name="scenarioName" title="'._('Scenario Name').'" '.
			'value="" placeholder="Scenario name" type="text" class="form-control">'.
		'</div>'.
	'</div>'.
	'<div class="controls center margin-top">'.
		'<button onclick="saveNewScenario()" class="btn btn-success">'.
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
			'$("#popupTitle").html("'._("New Scenario").'");'.
		'});'.
		
		'function saveNewScenario() {'.
			'var name = "";'.
			
			'name = $("#scenarioName").val();'.
			'name = name.trim();'.
			
			'$.ajax({'.
				'type: "GET",'.
				'url: "/templates/default/form/form_create_new_scenario.php",'.
				'data: "scenario_name="+encodeURIComponent(name),'.
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
						'redirect("/profile_user_scenarios/"+result+"/"+1);'.
					'}'.
				'}'.
			'});'.
		'}'.
		
	'</script>';
?>