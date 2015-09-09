<?php

include('header.php');


echo
	'<div>'.
		'<div id="errorMsg"></div>'.
		'<div class ="center">'._('Please enter the Smartcommand name :').'</div>'.
		'<div class="input-group margin-top">'.
			'<label class="input-group-addon left" for="smartcmdName">'._('Name').'</label>'.
			'<input id="smartcmdName" name="smartcmdName" title="'._('Smartcommand Name').'" '.
			'value="" placeholder="Smartcommand name" type="text" class="form-control">'.
		'</div>'.
	'</div>'.
	'<br/>'.
	'<div class="controls center">'.
		'<button onclick="saveNewSmartcommand()" class="btn btn-success">'.
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
	
		'$("#popupTitle").html("'._("New SmartCommand").'");'.
		
		'function saveNewSmartcommand() {'.
			'var name = "";'.
			
			'name = $("#smartcmdName").val();'.
			'name = name.trim();'.
			
			'$.ajax({'.
				'type: "GET",'.
				'url: "/templates/default/form/form_create_new_smartcmd.php",'.
				'data: "smartcmd_name="+name,'.
				'success: function(result) {'.
					'if (result && result.split("-1")[1]) {'.
						'$("#errorMsg").html(result.split("-1")[1]);'.
					'}'.
					'else if (result) {'.
						'popup_close();'.
						'redirect("/profile_user_scenarios/"+result);'.
					'}'.
				'}'.
			'});'.
		'}'.
		
	'</script>';
?>