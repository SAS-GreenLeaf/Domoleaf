<?php

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">
	<div class="center"><h2>'._('General configuration').'</h2></div><br/><br/>

	<div class="col-xs-12 col-md-6">
		<b>'._('Version').'</b><br/>
		'._('Current:').' '.$mastersversion.'
	</div>
	<div class="col-xs-12 col-md-6">
		<input id="checkboxaccess" type="checkbox"> '._('Activate remote access').'
		<div id="remoteaccess">
			<div class="control-group">
				<label class="control-label" for="http">'._('Remote access port').'</label>
				<input id="http" name="http" min="0" max="65535" title="'._('Remote access port').'" type="number" value="'.$httpport.'" class="form-control">
			</div>
			<div class="control-group">
				<label class="control-label" for="https">'._('Secure remote access port').'</label>
				<input id="https" name="https" min="0" max="65535" title="'._('Secure remote access port').'" type="number" value="'.$httpsport.'" class="form-control">
			</div>
		</div>
		<div class="clearfix"></div>
		<input id="securemode" type="checkbox"> '._('Force securised mode').'
		<div class="center">
			<button type="button" class="btn btn-greenleaf" onclick="SaveChange()">'._('Save').'</button>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-xs-12 col-md-6">
		<div class="center"><h3>'._('Email configuration').'</h3></div>
		<div class="control-group">
			<label class="control-label" for="fromMail">'._('From Mail').'</label>
			<input id="fromMail" name="fromMail" title="'._('From Mail').'" type="email" value="'.$fromMail.'" class="form-control">
		</div>
		<div class="control-group">
			<label class="control-label" for="fromName">'._('From Name').'</label>
			<input id="fromName" name="fromName" title="'._('From Name').'" type="text" value="'.$fromName.'" class="form-control">
		</div>
		<div class="control-group">
			<label class="control-label" for="smtpHost">'._('SMTP Host').'</label>
			<input id="smtpHost" name="smtpHost" title="'._('SMTP Host').'" type="text" value="'.$smtpHost.'" class="form-control">
		</div>
		<label class="control-label" for="smtpSecure">'._('SMTP Secure').'</label>
		<div class="controls">
			<input name="smtpSecure" id="smtpSecure-0" value="0" type="radio">
				'._('None ').'
			<input name="smtpSecure" id="smtpSecure-1" value="1" type="radio">
				'._('SSL ').'
			<input name="smtpSecure" id="smtpSecure-2" value="2" type="radio">
				'._('TLS').'
		</div>
		<div class="control-group">
			<label class="control-label" for="smtpPort">'._('SMTP Port').'</label>
			<div class="controls">
				<input name="smtpPort" id="smtpPort-25" value="25" type="radio">
					'._('25 ').'
				<input name="smtpPort" id="smtpPort-465" value="465" type="radio">
					'._('465 ').'
				<input name="smtpPort" id="smtpPort-587" value="587" type="radio">
					'._('587 ').'
				<input name="smtpPort" id="smtpPort-0" value="0" type="radio">
					'._('Other').'
			</div>
			<div id="checkotherportaccess">
				<input id="smtpPortDef" name="smtpPortDef" min="0" max="65535" title="'._('SMTP Port').'" type="number" value="'.$smtpPort.'" class="form-control">
			</div>
		</div>
		<input id="checkauthentificationaccess" type="checkbox"> '._('Activate authentification access').'
		<div id="authentificationaccess">
			<div class="control-group">
				<label class="control-label" for="smtpUsername">'._('SMTP Username').'</label>
				<input id="smtpUsername" name="smtpUsername" title="'._('SMTP Username').'" type="text" value="'.$smtpUsername.'" class="form-control">
			</div>
			<div class="control-group">
				<label class="control-label" for="smtpPassword">'._('SMTP Password').'</label>
				<input id="smtpPassword" name="smtpPassword" title="'._('SMTP Password').'" type="password" value="" class="form-control">
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="center">
			<br/>
			<button type="button" class="btn btn-greenleaf" onclick="SaveChangeMail()">'._('Save').'</button>
			<button type="button" class="btn btn-greenleaf" onclick="PopupTestMail()">'._('Test').'</button>
		</div>
	</div>
</div>

<script type="text/javascript">

	$("#checkboxaccess").click(function () {
		if($(this).is(":checked")){
			$("#remoteaccess").show("slow");
			if ($("#http").val() == "0" || $("#http").val() == ""){
				$("#http").val("6980");
			}
			if ($("#https").val() == "0" || $("#https").val() == ""){
				$("https").val("6924");
			}
		}
		else {
			$("#remoteaccess").hide("slow");
		}
	});';
	if (empty($httpport) && empty($httpsport)){
		echo '$("#remoteaccess").hide()';
	}

echo '

	$("#smtpSecure-'.$smtpSecure.'").attr("checked", "checked");

function checkSmtpPort(){
	var port = $("#smtpPortDef").val();

	if (port == 25 || port == 465 || port == 587){
		$("#smtpPort-"+port).attr("checked", "checked");
		$("#checkotherportaccess").hide();
	}
	else{
		$("#smtpPort-0").attr("checked", "checked");
		$("#checkotherportaccess").show();
	}
}

	$("input[name=smtpPort]").change(function() {
		$("#smtpPortDef").val($(this).val());
		checkSmtpPort();
	});
	
	$("#smtpPortDef").val('.$smtpPort.');
	checkSmtpPort();

	$("#checkauthentificationaccess").click(function () {
		if($(this).is(":checked")){
			$("#authentificationaccess").show("slow");
			if ($("#smtpUsername").val() == "0" || $("#smtpUsername").val() == ""){
				$("#smtpUsername").val("");
			}
			if ($("#smtpPassword").val() == "0" || $("#smtpPassword").val() == ""){
				$("smtpPassword").val("");
			}
		}
		else {
			$("#authentificationaccess").hide("slow");
		}
	});';
	if (empty($smtpUsername)){
		echo '$("#authentificationaccess").hide()';
	}
	else{
		echo '$("#checkauthentificationaccess").attr("checked", "checked");
			  $("#authentificationaccess").show();';
	}

echo'

function SaveChange(){
	var httpval = $("#http").val();
	var httpsval = $("#https").val();
	var securemode = $("#securemode").prop("checked") ? 1 : 0;
	var checkboxaccess = $("#checkboxaccess").prop("checked") ? 1 : 0;

	if (checkboxaccess == 0){
		httpval = 0;
		httpsval = 0;
	}
			
	$.ajax({
			type:"GET",
			url: "/form/form_general.php",
			data: "httpval="+httpval+"&httpsval="+httpsval+"&securemode="+securemode,
			complete: function(result, status) {
				
			}
		});
}

function SaveChangeMail(){
	var fromMailval = $("#fromMail").val();
	var fromNameval = $("#fromName").val();
	var smtpHostval = $("#smtpHost").val();
	var smtpSecureval = $("input[name=smtpSecure]:checked").attr(\'value\');
	var smtpPortval = $("#smtpPortDef").val();
	var smtpUsernameval = $("#smtpUsername").val();
	var smtpPasswordval = $("#smtpPassword").val();
	var checkauthentificationaccess = $("#checkauthentificationaccess").prop("checked") ? 1 : 0;

	if (checkauthentificationaccess == 0){
		smtpUsernameval = "";
		smtpPassword = "";
	}

	$.ajax({
		type:"POST",
		url: "/form/form_general_mail.php",
		data: "fromMailval="+encodeURIComponent(fromMailval)+"&fromNameval="+encodeURIComponent(fromNameval)+"&smtpHostval="+smtpHostval+"&smtpSecureval="+smtpSecureval+"&smtpPortval="+smtpPortval+"&smtpUsernameval="+encodeURIComponent(smtpUsernameval)+"&smtpPasswordval="+encodeURIComponent(smtpPasswordval),
		complete: function(result, status) {
		}
	});
}

function PopupTestMail(){
	$.ajax({
		type: "GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_test_mail.php",
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Test mail').'",
				message: result
			});
		}
	});
}

function SendTestMail(){
	if (!$("#sendTestMail").hasClass("m-progress")){
		$("#sendTestMail").addClass("m-progress");
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_send_test_mail.php",
			success: function(result){
				$("#sendTestMail").removeClass("m-progress");
				$("#messageTestMail").html(result);
			}
		});
	}
}

</script>';

?>