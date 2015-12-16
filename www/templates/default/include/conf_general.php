<?php

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">
	<div class="center">
		<h2>'._('General configuration').'</h2>
	</div><br/><br/>

	<div class="col-xs-12 col-md-6">
		<div id="noUpdate" class="alert alert-warning center" role="alert" hidden>
			<p>'._('No update available').'<p>
		</div>
		<b>'._('Version').'</b><br/>
		'._('Current:').' '.$mastersversion.'
		<button id="checkUpdateBtn" type="button" class="btn btn-info" onclick="popupCheckUpdates()" title="'._('Check Updates').'">
			<i class="fa fa-refresh"></i>
		</button>
		<button id="updateBtn" type="button" class="btn btn-info hide" onclick="popupUpdateVersion()" title="'._('Update').'">
			<i class="fa fa-download"></i>
		</button>
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
		<input id="securemode" type="checkbox" '.(!empty($forcessl)?'checked="checked"':'').'> '.
		_('Force securised mode').'
		<div class="center">
			<button type="button" class="btn btn-greenleaf" onclick="SaveChange()">'._('Save').'</button>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-xs-12 col-md-6">
		<br/>
		<div class="center">
			<h3>'
				._('Email configuration').'
				<button type="button" class="btn btn-primary margin-left" title="'._('Pre-configuration email').'" onclick="PopupPreConfigurationMail()"><span class="fa fa-cog"></span></button>
			</h3>
		</div>
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
				'._('None').' 
			<input name="smtpSecure" id="smtpSecure-1" value="1" type="radio">
				'._('SSL').' 
			<input name="smtpSecure" id="smtpSecure-2" value="2" type="radio">
				'._('TLS').'
		</div>
		<div class="control-group">
			<label class="control-label" for="smtpPort">'._('SMTP Port').'</label>
			<div class="controls">
				<input name="smtpPort" id="smtpPort-25" value="25" type="radio">
					'._('25').'
				<input name="smtpPort" id="smtpPort-465" value="465" type="radio">
					'._('465').'
				<input name="smtpPort" id="smtpPort-587" value="587" type="radio">
					'._('587').'
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

	<div class="col-xs-12 col-md-6">
		<br/>
		<div class="center">
			<h3>'._('Price for electricity').'</h3>
		</div>
		<div class="control-group">
			<label class="control-label" for="currency">'._('Currency').'</label>
				<select id="currency" name="selectbasic" class="input-xlarge center form-control selectpicker medium-input">';
				foreach ($allCurrency as $k => $curr){
					if ($k == $currency){
						echo '<option value="'.$k.'" selected="selected">'.$curr.'</option>';
					}
					else
						echo '<option value="'.$k.'">'.$curr.'</option>';
				}
				echo
				'</select>
		</div>
		<div class="control-group">
			<label class="control-label" for="highCost">'._('High cost').'</label>
			<input id="highCost" name="highCost" min="0" step="0.01" title="'._('High cost').'" type="number" value="'.$highCost.'" class="form-control">
		</div>
		<div class="control-group">
			<label class="control-label" for="lowCost">'._('Low cost').'</label>
			<input id="lowCost" name="lowCost" min="0" step="0.01" title="'._('Low cost').'" type="number" value="'.$lowCost.'" class="form-control">
		</div>
		<div class="control-group">
			<label class="control-label" for="lowField1">'._('Low field').' 1</label>
			<div style="display:inline-flex">
				<input id="lowField1_1" name="lowField1_1" min="0" max="23" title="'._('Low field').' 1" type="number" value="'.$lowField1_1.'" class="form-control">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>-</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input id="lowField1_2" name="lowField1_2" min="0" max="23" title="'._('Low field').' 1" type="number" value="'.$lowField1_2.'" class="form-control">
			</div>
		</div>
		<div class="control-group" style="float:left;">
			<label class="control-label" for="lowField2">'._('Low field').' 2</label>
			<div style="display:inline-flex">
				<input id="lowField2_1" name="lowField2_1" min="0" max="23" title="'._('Low field').' 2" type="number" value="'.$lowField2_1.'" class="form-control">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>-</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input id="lowField2_2" name="lowField2_2" min="0" max="23" title="'._('Low field').' 2" type="number" value="'.$lowField2_2.'" class="form-control">
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="center">
			<br/>
			<button type="button" class="btn btn-greenleaf" onclick="SaveChangePriceElec()">'._('Save').'</button>
		</div>
	</div>

</div>

<script type="text/javascript">

	$(document).ready(function(){
		activateMenuElem(\'general\');
		$(".selectpicker").selectpicker();
		$(".selectpicker").selectpicker(\'refresh\');
	});

	$("#checkboxaccess").click(function () {
		if($(this).is(":checked")){
			$("#remoteaccess").show("slow");
			if ($("#http").val() == "0" || $("#http").val() == ""){
				$("#http").val("6980");
			}
			if ($("#https").val() == "0" || $("#https").val() == ""){
				$("#https").val("6924");
			}
		}
		else {
			$("#remoteaccess").hide("slow");
		}
	});';
	if (empty($httpport) && empty($httpsport)){
		echo '$("#remoteaccess").hide()';
	}
	else {
		echo '$("#checkboxaccess").attr("checked", "checked");';
	}
echo '
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

function PopupPreConfigurationMail(){
	var fromMailval = $("#fromMail").val();

	$.ajax({
		type: "GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_pre_configuration_mail.php",
		data: "fromMailval="+encodeURIComponent(fromMailval),
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Pre-configuration email').'",
				message: result
			});
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

function SaveChangePriceElec(){
	var highCostval = $("#highCost").val();
	var lowCostval = $("#lowCost").val();
	var lowField1val = $("#lowField1_1").val() + \'-\' + $("#lowField1_2").val();
	var lowField2val = $("#lowField2_1").val() + \'-\' + $("#lowField2_2").val()
	var currencyval = $("#currency").val();

	$.ajax({
		type:"POST",
		url: "/form/form_general_price_elec.php",
		data: "highCostval="+encodeURIComponent(highCostval)+"&lowCostval="+encodeURIComponent(lowCostval)+"&lowField1val="+encodeURIComponent(lowField1val)+"&lowField2val="+encodeURIComponent(lowField2val)+"&currencyval="+encodeURIComponent(currencyval),
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

var idIntervalCheck = null;

function popupCheckUpdates(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_check_updates.php",
		success: function(result) {
			idIntervalCheck = setInterval(function() { getEndCheck(); }, 10000);
			BootstrapDialog.show({
				title: "'._('Checking updates').'",
				message: result,
				closable: false,
			});
		}
	});
}

function getEndCheck(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/form/form_conf_check_version.php",
		data:"idVersion="+13,
		success: function(result){
			if (result != "") {
				clearInterval(idIntervalCheck);
				if (parseInt(result) >= 0 && result != "'.$mastersversion.'") {
					$("#checkUpdateBtn").addClass("hide");
					$("#updateBtn").removeClass("hide");
					$("#noUpdate").hide();
				}
				else {
					$("#updateBtn").addClass("hide");
					$("#checkUpdateBtn").removeClass("hide");
					$("#noUpdate").show();
				}
				popup_close();
			}
		}
	});
}

function popupUpdateVersion(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_conf_update_version.php",
		success: function(result) {
			idIntervalUpdate = setInterval(function() { getEndUpdate(); }, 10000);
			BootstrapDialog.show({
				title: "'._('Updating box').'",
				message: result,
				closable: false,
			});
		}
	});
}

function getEndUpdate(){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/form/form_conf_check_version.php",
		data:"idVersion="+4,
		success: function(result){
			if (parseInt(result) >= 0 && result != "'.$mastersversion.'") {
				clearInterval(idIntervalUpdate);
				popup_close();
				location.reload();
			}
		}
	});
}

</script>';

?>