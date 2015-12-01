<?php

include('header.php');

if (!empty($_GET['trigger_id'])) {
	$request =  new Api();
	$result  =  $request -> send_request();
	echo
		'<div>'.
			'<div id="errorMsg"></div>'.
			'<div class ="center">'._('Please enter the new Trigger name :').'</div>'.
			'<div class="input-group margin-top">'.
				'<label class="input-group-addon left" for="triggerName">'._('Name').'</label>'.
				'<input id="triggerName" name="triggerName" title="'._('Trigger Name').'" '.
				'value="" placeholder="Trigger name" type="text" class="form-control">'.
			'</div>'.
		'</div>'.
		'<br/>'.
		'<div class="controls center">'.
			'<button onclick="updateTriggerName()" class="btn btn-success">'.
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
				'$("#popupTitle").html("'._("Rename Trigger").'");'.
				'setTimeout(function(){'.
					'$("#triggerName").focus();'.
				'}, 400);'.
			'});'.

			'function updateTriggerName() {'.
				'var name = "";'.
				
				'name = $("#triggerName").val();'.
				'name = name.trim();'.
				
				'$.ajax({'.
					'type: "GET",'.
					'url: "/templates/default/form/form_update_trigger_name.php",'.
					'data: "trigger_id="+'.$_GET['trigger_id'].'+"&trigger_name="+encodeURIComponent(name),'.
					'success: function(result) {'.
						'if (result && result.split("-1")[1]) {'.
							'$("#errorMsg").html(result.split("-1")[1]);'.
						'}'.
						'else if (result) {'.
							'popup_close();'.
							'$("#navbarTriggerName").html(name);'.
						'}'.
					'}'.
				'});'.
			'}'.
			
		'</script>';
}
?>