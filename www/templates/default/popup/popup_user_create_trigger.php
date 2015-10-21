<?php

include('header.php');

$request = new Api();
$request -> add_request('listSmartcmd');
$result  =  $request -> send_request();

$smartcmdList = $result->listSmartcmd;

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
	'<div class="input-group margin-top">'.
		'<select class="selectpicker form-control margin-bottom" id="selectLinkedSmartcmd" data-size="10">'.
			'<option value="0">'._('No Smartcommand selected').'</option>';
			foreach ($smartcmdList as $elem) {
				echo '<option value="'.$elem->smartcommand_id.'">'.$elem->name.'</option>';
			}
			echo
		'</select>'.
	'</div>'.
	'<div class="controls center">'.
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
		
		'$(".selectpicker").selectpicker();'.
		
		'function saveNewTrigger() {'.
			'var name = "";'.
			
			'name = $("#triggerName").val();'.
			'name = name.trim();'.
			
			'var smartcmd_id = parseInt($("#selectLinkedSmartcmd").val());'.
			'if (smartcmd_id == 0) {'.
				'$("#popupError").show();'.
				'$("#errorMsg").html("'._('No Smartcommand selected').'");'.
			'}'.
			'else {'.
				'$.ajax({'.
					'type: "GET",'.
					'url: "/templates/default/form/form_create_new_trigger.php",'.
					'data: "trigger_name="+name+"&smartcmd_id="+smartcmd_id,'.
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
							'redirect("/profile_user_trigger_events/"+result);'.
						'}'.
					'}'.
				'});'.
			'}'.
		'}'.
		
	'</script>';
?>