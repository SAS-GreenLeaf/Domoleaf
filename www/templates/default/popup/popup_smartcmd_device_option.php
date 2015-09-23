<?php

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['id_option'])
	&& !empty($_GET['room_id_device']) && !empty($_GET['id_exec'])
	&& !empty($_GET['modif'])) {
	
	echo
	'<div id="popup_smartcmd_content" class="center"></div>'.
	
	'<script type="text/javascript">'.
	
	'$("#popupTitle").html("'._("Option value").'");'.
	
	'setTimeout(function(){ popupSmartcmdContent(); }'.
				', 150);'.
	
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