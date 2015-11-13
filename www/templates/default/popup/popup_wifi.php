<?php

include('header.php');

if (!empty($_GET['daemon_id'])){

	$request =  new Api();
	$request -> add_request('confWifi', array($_GET['daemon_id']));
	$result  =  $request ->	send_request();
	
	$wifi = $result->confWifi;
	
	echo '<form id="form_wifi" class="cmxform">'.
					'<div id="uploadError" class="alert alert-danger center" role="alert" hidden>'.
						_('File must be JPG or PNG, less than 1MB').
					'</div>'.
			'<div id="wifiError" hidden>'._('The SSID is not valid').'</div>'.
			'<div class="control-group">'.
				'<label class="control-label" for="wifiMode">'._('Mode').'</label>'.
				'<select class="selectpicker form-control" id="wifiMode" value="'.$wifi->mode.'">'.
					'<option value="0">'._('Disabled').'</option>'.
					'<option value="1">'._('Client').'</option>'.
					'<option value="2">'._('Access Point').'</option>'.
				'</select>'.
			'</div>'.
			'<div class="control-group">'.
				'<label class="control-label" for="ssid">'._('SSID').'</label>'.
				'<input id="ssid" required name="ssid" title="'._('').'" type="text" placeholder="SSID" value="'.$wifi->ssid.'" class="form-control">'.
			'</div>'.
			'<div class="control-group">'.
				'<label class="control-label" for="wifiPassword">'._('Password').'</label>'.
				'<input id="wifiPassword" name="wifiPassword" title="'._('Password').'" type="password" placeholder="Password" value="'.$wifi->password.'" class="form-control">'.
			'</div>'.
			'<div class="control-group">'.
				'<label class="control-label" for="wifiSecurity">'._('Security').'</label>'.
				'<input id="wifiSecurity" name="wifiSecurity" title="'._('Security').'" type="text" value="'.$wifi->security.'" class="form-control">'.
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
			'$(document).ready(function(){'.
				'validate_ssid();'.
			'});'.
		 '</script>';

}

?>