<?php

include('header.php');

if (!empty($_GET['type_elem']) && ($_GET['type_elem'] == 1 || $_GET['type_elem'] == 2)
	&& !empty($_GET['idelem']) && $_GET['idelem'] > 0) {
	
	$type_elem = $_GET['type_elem'];
	$target_dir_device = "/templates/default/custom/device/";
	$target_dir_room = "/templates/default/custom/room/";
	
	$request = new Api();
	
	$request -> add_request('mcAllowed');
	$request -> add_request('confUserDeviceEnable', array($_GET['userid']));
	$request -> add_request('confUserRoomEnable', array($_GET['userid']));
	$result  =  $request -> send_request();
	
	$listAllVisible = $result->mcAllowed;
	
	if ($type_elem == 1) {
		if (empty($result -> confUserDeviceEnable) || sizeof($result -> confUserDeviceEnable) == 0) {
			$deviceallowed = $listAllVisible->ListDevice;
		}
		else {
			$deviceallowed = $result->confUserDeviceEnable;
		}
	}
	
	if ($type_elem == 2) {
		if (empty($result -> confUserRoomEnable) || sizeof($result -> confUserRoomEnable) == 0) {
			$roomallowed = $listAllVisible->ListRoom;
		}
		else {
			$roomallowed = $result->confUserRoomEnable;
		}
	}
	$elem = "";
	if ($type_elem == 1 && !empty($deviceallowed) && !empty($deviceallowed->$_GET['idelem'])) {
		$elem = $deviceallowed->$_GET['idelem'];
	}
	else if ($type_elem == 2 && !empty($roomallowed) && !empty($roomallowed->$_GET['idelem'])) {
		$elem = $roomallowed->$_GET['idelem'];
	}
	if (!empty($elem)) {
		echo
			'<script type="text/javascript" src="/templates/default/popup/popup_custom_device.js"></script>'.
			'<div class="cd-body">'.
				'<div id="uploadSuccess" class="alert alert-success center" role="alert" hidden>'.
					'<p>'._('Success').'<p>'.
				'</div>'.
				'<div id="uploadFail" class="alert alert-danger center" role="alert" hidden>'.
					'<p>'._('Fail').'<p>'.
				'</div>'.
				'<div id="uploadError" class="alert alert-danger center" role="alert" hidden>'.
					_('Image must be JPG or PNG, and size less than 1MB').
				'</div>'.
				'<p class="center">'._('Image must be JPG or PNG, and size less than 1MB').'</p>'.
				'<div class="cd-panel">'.
					'<div class="cd-panel-content">'.
						'<label for="image">'.
							'<form id="uploadFileForm" action="" method="post" enctype="multipart/form-data" class="image-select cmxform">'.
								'<div id="uploadMsg" class="center">'.
									_('Click or Drag image here').
									'</br>'.
									'<i id="uploadFileIcon" class="fa fa-cloud-upload lg"></i>'.
								'</div>'.
								'<input id="image" type="file" name="fileToUpload" data-droppable-input="" class="required" accept="image/*"/>'.
								'<input id="userid" type="hidden" value="'.$_GET['userid'].'"/>'.
								'<input id="type_elem" type="hidden" value="'.$type_elem.'"/>'.
								'<input id="id_elem" type="hidden" value="'.$_GET['idelem'].'"/>'.
								'<i class="fa fa-camera fa-2x image-select__icon"></i>'.
								'<div class="image-select__message"></div>'.
								'<div id="previewImg" class="bg-image aspect-square-little"'.
								     'data-droppable-image="" ';
									if ($_GET['type_elem'] == 1 && !empty($elem->device_bgimg)){
										echo 'style="background-image: url(\''.$target_dir_device.$elem->device_bgimg.'\')"';
									}
									else if ($_GET['type_elem'] == 2 && !empty($elem->room_bgimg)){
										echo 'style="background-image: url(\''.$target_dir_room.$elem->room_bgimg.'\')"';
									}
									echo '>'.
								'</div>'.
							'</form>'.
							'<div class="center padding-top">'.
								'<button id="uploadBtn" type="submit"'.
								        'class="btn btn-greenleaf" '.
								        'onclick="submitFormUpload(event)">'.
									_('Upload Image').
								'</button>'.
								'<button id="deleteBtn" '.
								        'class="btn btn-danger margin-left" '.
								        'onclick="deleteDeviceImg('.$_GET['type_elem'].', '.$_GET['idelem'].', event)">'.
									_('Delete Image').
								'</button>'.
							'</div>'.
						'</label>'.
					'</div>'.
				'</div>'.
			'</div>';
		echo
			'<script type="text/javascript">'.
				'$(document).ready(function(event) {';
					if (!empty($elem->device_bgimg) || !empty($elem->room_bgimg)) {
						echo 
						'$("#uploadMsg").hide();'.
						'$("#previewImg").removeClass("aspect-square-little");'.
						'$("#previewImg").addClass("aspect-square");';
					}
					if ($_GET['type_elem'] == 1){
						echo
						'$("#popupTitle").html("'._("Upload Device Image").'");';
						if (!empty($elem->device_bgimg)){
							echo
							'$("#deleteBtn").show();'.
							'$("#uploadBtn").hide();';
						}
					}
					else if ($_GET['type_elem'] == 2){
						echo
						'$("#popupTitle").html("'._("Upload Room Image").'");';
						if (!empty($elem->room_bgimg)) {
							echo
							'$("#deleteBtn").show();'.
							'$("#uploadBtn").hide();';
						}
					}
					echo
				'});'.
			'</script>';
	}
}

?>