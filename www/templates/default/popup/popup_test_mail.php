<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

echo '<div id="messageTestMail"></div>';
echo '
<div class="center">';
printf(_('After the validation of the configuration emai, you will receive a test email. If it not come, go check your configuration of your mail and your server SMTP'));
echo
	'<br/><br/><div class="controls center">'.
		'<button onclick="SendTestMail()" id="sendTestMail" class="btn btn-success">'._('Send').' <span class="glyphicon glyphicon-ok"></span></button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	'</div>'.

'<script type="text/javascript">'.

'setTimeout(function(){ SetGoodWidth(); }, 100);'.

'function SetGoodWidth(){'.
	'var widhtDest = $("#spanDestinatorMail").width();'.
	'var widhtObj = $("#spanObjectMail").width();'.

	'if (widhtDest > widhtObj){'.
		'$("#spanObjectMail").width(widhtDest);'.
	'}'.
	'else{'.
		'$("#spanDestMail").width(widhtObj);'.
	'}'.
	
'}'.

'</script>';

?>