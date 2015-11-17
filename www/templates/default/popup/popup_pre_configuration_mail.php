<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

if (empty($_GET['fromMailval'])){
	$_GET['fromMailval'] = '';
}

echo '<div class="center">'._('Enter your email to complete your configuration').'';

echo '<div id="errorPreConfigurationMail"></div>';

echo '
	<div class="input-group margin-top">'.
		'<input id="fromMailpopup" name="fromMailpopup" title="'._('From Mail').'" value="'.$_GET['fromMailval'].'" placeholder="Type your email" type="email" class="form-control">'.
	'</div>'.
	'<div class="clearfix"></div>'.
	'<br/><br/><div class="controls center">'.
		'<button onclick="PreConfigurationMail()" id="PreConfigurationMail" class="btn btn-success">'._('Pre-configure').' <span class="glyphicon glyphicon-ok"></span></button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	'</div>'.
'</div>';

echo 

'<script type="text/javascript">'.

'function PreConfigurationMail(){'.
	'var fromMailval = $("#fromMailpopup").val();'.
	
	'$.ajax({'.
		'type: "GET",'.
		'url: "/templates/'.TEMPLATE.'/form/form_pre_configuration_mail.php",'.
		'data: "fromMailval="+encodeURIComponent(fromMailval),'.
		'success: function(result) {'.
			'if (result[0] == "|"){'.
				'$("#fromMail").val(result.split("|")[1]);'.
				'$("#fromName").val(result.split("|")[2]);'.
				'$("#smtpHost").val(result.split("|")[3]);'.
				'$("#smtpSecure-"+result.split("|")[4]).attr("checked", "checked");'.
				'$("#smtpPortDef").val(result.split("|")[5]);'.
				'checkSmtpPort();'.
				'popup_close();'.
			'}'.
			'else{'.
				'$("#errorPreConfigurationMail").html(result);'.
			'}'.
		'}'.
	'});'.
'}'.

'</script>';

?>