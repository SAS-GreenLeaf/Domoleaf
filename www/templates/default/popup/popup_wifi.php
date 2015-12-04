<?php

include('header.php');

if (!empty($_GET['daemon_id'])){

	$request =  new Api();
	$request -> add_request('confWifi', array($_GET['daemon_id']));
	$result  =  $request -> send_request();
	
	$wifi = $result->confWifi;
	
	echo '<form id="form_wifi" class="cmxform">'.
			'<div id="wifiError" hidden>'._('The SSID is not valid').'</div>'.
			'<div class="control-group">'.
				'<label class="control-label" for="wifiMode">'._('Mode').'</label>'.
				'<select class="selectpicker form-control" id="wifiMode" onchange="CheckWifiMode()" value="'.$wifi->mode.'">'.
					'<option value="0">'._('Disabled').'</option>'.
					'<option value="1">'._('Client').'</option>'.
					'<option value="2">'._('Access Point').'</option>'.
				'</select>'.
			'</div>'.
			'<div id="div-ssid" class="control-group">'.
				'<label class="control-label" for="ssid">'._('SSID').'</label>'.
				'<input id="ssid" required name="ssid" title="" type="text" placeholder="'._('SSID').'" value="'.$wifi->ssid.'" class="form-control">'.
			'</div>'.
			'<div id="div-wifiPassword" class="control-group">'.
				'<label id="LabelWifiPassword" class="control-label" for="wifiPassword">'._('Password').'</label>'.
				'<input id="wifiPassword" name="wifiPassword" title="" type="password" placeholder="'._('Password').'" value="'.$wifi->password.'" class="form-control">'.
			'</div>'.
			'<div id="div-wifiSecurity" class="control-group">'.
				'<label class="control-label" for="wifiSecurity">'._('Security').'</label>'.
				'<select class="selectpicker form-control" id="wifiSecurity" value="'.$wifi->security.'">'.
					'<option id="wifiOptionWEP" value="1">'._('WEP').'</option>'.
					'<option id="wifiOptionWPA" value="2">'._('WPA').'</option>'.
					'<option id="wifiOptionWPA2" value="3">'._('WPA2').'</option>'.
				'</select>'.
			'</div>'.
		 '</form>'.
		 '<input hidden id="inputDaemonId" value="'.$_GET['daemon_id'].'"/>'.
		 '<div class="clearfix"></div>'.
		 '<div class="center">'.
			'<br/>'.
			'<button type="submit" class="btn btn-greenleaf" onclick="submitFormWifi(event)">'._('Save').'</button>'.
		 '</div>';
	echo '<script type="text/javascript">'.
			'$(".selectpicker").selectpicker();'.
			'$(".selectpicker").selectpicker(\'refresh\');'.
			'$("#wifiMode").selectpicker(\'val\', '.$wifi->mode.');'.
			'$("#wifiSecurity").selectpicker(\'val\', '.$wifi->security.');'.
			'CheckWifiMode();'.
			'$(document).ready(function(){'.
				'validate_ssid();'.
			'});'.
			'function CheckWifiMode(){'.
				'if ($("#wifiMode").val() == 0){'.
					'$("#div-ssid").hide();'.
					'$("#div-wifiPassword").hide();'.
					'$("#div-wifiSecurity").hide();'.
				'}'.
				'else if ($("#wifiMode").val() == 1){'.
					'$("#div-ssid").show();'.
					'$("#div-wifiPassword").show();'.
					'$("#div-wifiSecurity").show();'.
					'$("#wifiSecurity").html("<option id=\"wifiOptionWEP\" value=\"1\">'._('WEP').'</option><option value=\"2\">'._('WPA').'</option><option value=\"3\">'._('WPA2').'</option>");'.
					'$("#wifiSecurity").selectpicker(\'refresh\');'.
					'$("#wifiSecurity").selectpicker(\'val\', '.$wifi->security.');'.
				'}'.
				'else if ($("#wifiMode").val() == 2){'.
					'$("#div-ssid").show();'.
					'$("#div-wifiPassword").show();'.
					'$("#div-wifiSecurity").show();'.
					'if ($("#wifiSecurity").val() == 1){'.
						'$("#wifiSecurity").selectpicker(\'val\', 3);'.
					'}'.
					'$("#wifiOptionWEP").remove();'.
					'$("#wifiSecurity").selectpicker(\'refresh\');'.
					'}'.
			'}'.
		 '</script>';

}

?>