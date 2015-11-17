<?php

include('header.php');

if (!empty($_GET['id_trigger']) && !empty($_GET['id_option'])
	&& !empty($_GET['room_id_device']) && !empty($_GET['id_condition'])
	&& !empty($_GET['modif'])) {
	
	$request =  new Api();
	$result  =  $request -> send_request();
	
	echo
	'<div id="popup_trigger_content" class="center"></div>'.
	
	'<script type="text/javascript">'.
	
	'$("#popupTitle").html("'._("Option value").'");'.
	
	'setTimeout(function(){ popuptriggerContent(); }'.
				', 150);'.
	
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