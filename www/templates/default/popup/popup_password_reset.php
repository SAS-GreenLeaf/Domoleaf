<?php

include('header.php');

if (empty($_POST['resetKeyval'])){
	$_POST['resetKeyval'] = '';
}

$request =  new Api();
$result  =  $request -> send_request();

echo '<div id="messagePasswordReset"></div>';
echo '
<div class="center">'._('Enter your new password desired').'';

echo '
	<div class="input-group margin-top">'.
		'<label class="input-group-addon left" id="labresetKey" for="resetKey">'._('Reset Key').'</label>'.
		'<input id="resetKey" name="resetKey" title="'._('Reset Key').'" value="'.$_POST['resetKeyval'].'" placeholder="Type your key" type="text" class="form-control">'.
	'</div>'.
	'<br/><br/><div class="form-group">'.
		'<div class="input-group">'.
			'<label class="input-group-addon left" id="labnewPassword" for="newPassword">'._('New password').'</label>'.
			'<input id="newPassword" name="newPassword" title="'._('New password').'" value="" placeholder="Type your password" type="password" class="form-control">'.
		'</div>'.
	'</div>'.
	'<div class="input-group">'.
		'<label class="input-group-addon left" id="labnewPasswordBis" for="newPasswordBis">'._('Confirmation').'</label>'.
		'<input id="newPasswordBis" name="newPasswordBis" title="'._('Confirmation').'" value="" placeholder="Valid your password" type="password" class="form-control">'.
	'</div>'.
	'<br/><br/><div class="controls center">'.
		'<button onclick="CheckResetKey()" id="checkResetKey" class="btn btn-success">'._('Send').' <span class="glyphicon glyphicon-ok"></span></button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	'</div>'.
'</div>';

echo 

'<script type="text/javascript">'.

'$(document).ready(function(){'.
	'setTimeout(function(){ SetGoodWidth(); }, 150);'.
	'setTimeout(function(){'.
		'$("#newPassword").focus();'.
	'}, 400);'.
'});'.

'function SetGoodWidth(){'.
	'var widhtresetKey = $("#labresetKey").width();'.
	'var widhtnewPassword = $("#labnewPassword").width();'.
	'var widhtnewPasswordBis = $("#labnewPasswordBis").width();'.
	'var widhtmax = widhtresetKey;'.
	
	'if (widhtnewPassword >= widhtresetKey){'.
		'widhtmax = widhtnewPassword;'.
	'}'.
	'else if(widhtnewPasswordBis >= widhtresetKey){'.
		'widhtmax = widhtnewPasswordBis;'.
	'}'.
	'$("#labnewPasswordBis").width(widhtmax);'.
	'$("#labnewPassword").width(widhtmax);'.
	'$("#labresetKey").width(widhtmax);'.
'}'.

'function CheckResetKey(){'.
	'var resetKeyval = $("#resetKey").val();'.
	'var newPasswordval = $("#newPassword").val();'.
	'var newPasswordBisval = $("#newPasswordBis").val();'.

	'$.ajax({'.
		'type: "POST",'.
		'data: "resetKeyval="+encodeURIComponent(resetKeyval)+"&newPasswordval="+encodeURIComponent(newPasswordval)+"&newPasswordBisval="+encodeURIComponent(newPasswordBisval),'.
		'url: "/templates/'.TEMPLATE.'/form/form_reset_password.php",'.
		'success: function(result) {'.
			'if (result[0] == "1"){'.
				'PopupShowUsername(result.split("1")[1]);'.
			'}'.
			'$("#messagePasswordReset").html(result);'.
		'}'.
	'});'.
'}'.

'</script>';

?>