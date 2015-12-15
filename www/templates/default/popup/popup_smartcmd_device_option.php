<?php

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['id_option'])
	&& !empty($_GET['room_id_device']) && !empty($_GET['id_exec'])
	&& !empty($_GET['modif'])) {
	
	$request =  new Api();
	$request -> add_request('mcDeviceInfo', array($_GET['room_id_device']));
	$result  =  $request -> send_request();
	
	$device_info = $result->mcDeviceInfo;
	
	if (empty($device_info) or empty($device_info->device_opt->{$_GET['id_option']})) {
		echo 
		'<div class="alert alert-danger center" role="alert">'.
			_('Option not available').
		'</div>'.
		'<script type="text/javascript">'.
			'$("#popupTitle").html("'._('Option not available').'");'.
		'</script>';
		return;
	}
	$device_name = $device_info->name;
	$option_name = $device_info->device_opt->{$_GET['id_option']}->name;
	if ($_GET['id_option'] == 392) {
		$option_name = _('RGB');
	}
	else if ($_GET['id_option'] == 410) {
		$option_name = _('RGBW');
	}
	echo
	'<div id="popup_smartcmd_content" class="center"></div>'.
	
	'<script type="text/javascript">'.
		'$("#popupTitle").html("'.$device_name.' : '.$option_name.'");'.
		
		'setTimeout(function(){ popupSmartcmdContent(); }, 150);'.
		
		'function popupSmartcmdContent() {'. 
			'$.ajax({'.
				'type: "GET",'.
				'url: "/templates/default/form/form_smartcmd_device_option.php",'.
				'data: "room_id_device="+'.$_GET['room_id_device'].''.
						'+"&id_option="+'.$_GET['id_option'].''.
						'+"&id_smartcmd="+'.$_GET['id_smartcmd'].''.
						'+"&id_exec="+'.$_GET['id_exec'].''.
						'+"&modif="+'.$_GET['modif'].','.
				'success: function(result) {'.
					'$("#popup_smartcmd_content").html(result);'.
				'}'.
			'});'.
		'}'.
	'</script>';
}
?>