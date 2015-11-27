<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

echo '
<div class="center">'._('Please, enter your reset key for reinitialising your admin password').'';
echo '
	<br/><div class="input-group margin-top">'.
		'<label class="input-group-addon left" for="resetKey">'._('Reset Key').'</label>'.
		'<input id="resetKey" name="resetKey" title="'._('Reset Key').'" value="" placeholder="Type your key" type="text" class="form-control">'.
	'</div>'.
	'<div id="wrongKey" class="hide">'.
		'<br/>'.
		'<div class="alert alert-danger alert-dismissible alert-backup center" role="alert" id ="signerr">'.
			'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> '._('Bad key, try again').
		'</div>'.
	'</div>'.
	'<br/><div class="controls center">'.
		'<button onclick="CheckResetKey()" id="checkResetKey" class="btn btn-success">'._('Send').' <span class="glyphicon glyphicon-ok"></span></button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	'</div>'.
'</div>';


echo 

'<script type="text/javascript">'.

'$(document).ready(function(){'.
	'setTimeout(function(){'.
		'$("#resetKey").focus();'.
	'}, 400);'.
'});'.

'function CheckResetKey(){'.
	'var resetKeyval = $("#resetKey").val();'.

	'$.ajax({'.
		'type: "POST",'.
		'data: "resetKeyval="+encodeURIComponent(resetKeyval),'.
		'url: "/form/form_check_reset_key.php",'.
		'success: function(result) {'.
			'if (result){'.
				'PopupPasswordReset(resetKeyval)'.
			'}'.
			'else{'.
				'$("#wrongKey").removeClass("hide");'.
			'}'.
		'}'.
	'});'.
'}'.

'</script>';

?>