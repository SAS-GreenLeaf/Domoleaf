<?php

include('header.php');

if (!empty($_GET['smartcmd_id'])) {
	$request =  new Api();
	$result  =  $request -> send_request();
	
	echo
		'<div>'.
			'<div id="errorMsg"></div>'.
			'<div class ="center">'._('Please enter the new Smartcommand name :').'</div>'.
			'<div class="input-group margin-top">'.
				'<label class="input-group-addon left" for="smartcmdName">'._('Name').'</label>'.
				'<input id="smartcmdName" name="smartcmdName" title="'._('Smartcommand Name').'" '.
				'value="" placeholder="Smartcommand name" type="text" class="form-control">'.
			'</div>'.
		'</div>'.
		'<br/>'.
		'<div class="controls center">'.
			'<button onclick="updateSmartcmdName()" class="btn btn-success">'.
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
		
			'$("#popupTitle").html("'._("Rename Smartcommand").'");'.
			
			'function updateSmartcmdName() {'.
				'var name = "";'.
				
				'name = $("#smartcmdName").val();'.
				'name = name.trim();'.
				
				'$.ajax({'.
					'type: "GET",'.
					'url: "/templates/default/form/form_update_smartcmd_name.php",'.
					'data: "smartcmd_id="+'.$_GET['smartcmd_id'].'+"&smartcmd_name="+encodeURIComponent(name),'.
					'success: function(result) {'.
						'if (result && result.split("-1")[1]) {'.
							'$("#errorMsg").html(result.split("-1")[1]);'.
						'}'.
						'else if (result) {'.
							'popup_close();'.
							'$("#navbarSmartcmdName").html(name);'.
						'}'.
					'}'.
				'});'.
			'}'.
			
		'</script>';
}
?>