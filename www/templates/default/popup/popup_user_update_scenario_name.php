<?php

include('header.php');

if (!empty($_GET['scenario_id'])) {
	echo
		'<div>'.
			'<div id="errorMsg"></div>'.
			'<div class ="center">'._('Please enter the new Scenario name :').'</div>'.
			'<div class="input-group margin-top">'.
				'<label class="input-group-addon left" for="scenarioName">'._('Name').'</label>'.
				'<input id="scenarioName" name="scenarioName" title="'._('Scenario Name').'" '.
				'value="" placeholder="Scenario name" type="text" class="form-control">'.
			'</div>'.
		'</div>'.
		'<br/>'.
		'<div class="controls center">'.
			'<button onclick="updateScenarioName()" class="btn btn-success">'.
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
		
			'$("#popupTitle").html("'._("New scenario").'");'.
			
			'function updateScenarioName() {'.
				'var name = "";'.
				
				'name = $("#scenarioName").val();'.
				'name = name.trim();'.
				
				'$.ajax({'.
					'type: "GET",'.
					'url: "/templates/default/form/form_update_scenario_name.php",'.
					'data: "scenario_id="+'.$_GET['scenario_id'].'+"&scenario_name="+name,'.
					'success: function(result) {'.
						'if (result && result.split("-1")[1]) {'.
							'$("#errorMsg").html(result.split("-1")[1]);'.
						'}'.
						'else if (result) {'.
							'popup_close();'.
							'$("#summaryScenarioName").html(name);'.
						'}'.
					'}'.
				'});'.
			'}'.
			
		'</script>';
}
?>