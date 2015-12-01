<?php

include('header.php');

if (!empty($_GET['id_trigger']) && !empty($_GET['id_option'])
	&& !empty($_GET['room_id_device']) && !empty($_GET['id_condition'])
	&& !empty($_GET['modif'])) {
	
	$request =  new Api();
	$request -> add_request('mcDeviceInfo', array($_GET['room_id_device']));
	$result  =  $request -> send_request();
	
	$device_info = $result->mcDeviceInfo;
	$device_name = $device_info->name;
	$option_name = $device_info->device_opt->{$_GET['id_option']}->name;
	if ($_GET['id_option'] == 392) {
		$option_name = _('RGB');
	}
	else if ($_GET['id_option'] == 410) {
		$option_name = _('RGBW');
	}
	echo
	'<div id="popup_trigger_content" class="center"></div>'.
	
	'<script type="text/javascript">'.
		'$("#popupTitle").html("'.$device_name.' : '.$option_name.'");'.
		
		'setTimeout(function(){ popuptriggerContent(); }, 150);'.
		
		'function popuptriggerContent() {'. 
			'$.ajax({'.
				'type: "GET",'.
				'url: "/templates/default/form/form_trigger_device_option.php",'.
				'data: "room_id_device="+'.$_GET['room_id_device'].''.
						'+"&id_option="+'.$_GET['id_option'].''.
						'+"&id_trigger="+'.$_GET['id_trigger'].''.
						'+"&id_condition="+'.$_GET['id_condition'].''.
						'+"&modif="+'.$_GET['modif'].','.
				'success: function(result) {'.
					'$("#popup_trigger_content").html(result);'.
				'}'.
			'});'.
		'}'.
	'</script>';
}
?>