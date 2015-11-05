<?php

include('header.php');

if (empty($_GET['userid'])) {
	$_GET['userid'] = 0;
}
$request =  new Api();
$request -> add_request('mcAllowed');
$request -> add_request('confUserDeviceEnable', array($_GET['userid']));
$result  =  $request -> send_request();

if (empty($result -> confUserDeviceEnable) || sizeof($result -> confUserDeviceEnable) == 0) {
	$listAllVisible = $result->mcAllowed;
	$deviceallowed = $listAllVisible->ListDevice;
}
else {
	$deviceallowed = $result->confUserDeviceEnable;
}

$target_dir = "/templates/default/custom/device/";

$iduser = $_GET['userid'];


if (!empty($_GET['iddevice']) && $_GET['iddevice'] > 0) {
	if (!empty($deviceallowed) && !empty($deviceallowed->$_GET['iddevice'])){
		$device = $deviceallowed->$_GET['iddevice'];
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
						_('File must be JPG or PNG, less than 1MB').
					'</div>'.
					'<div class="cd-panel">'.
						'<div class="cd-panel-content">'.
							'<label for="image">'.
								'<form id="uploadFileForm" action="" method="post" enctype="multipart/form-data" class="image-select cmxform">'.
									'<div id="uploadMsg" class="center">'.
										_('Click or Drag file here').
										'</br>'.
										'<i id="uploadFileIcon" class="fa fa-cloud-upload lg"></i>'.
									'</div>'.
									'<input id="image" type="file" name="fileToUpload" data-droppable-input="" class="required" accept="image/*"/>'.
									'<input id="iddevice" type="hidden" value="'.$_GET['iddevice'].'"/>'.
									'<input id="userid" type="hidden" value="'.$iduser.'"/>'.
									'<i class="fa fa-camera fa-2x image-select__icon"></i>'.
									'<div class="image-select__message"></div>'.
									'<div id="previewImg" class="bg-image aspect-square-little"'.
									     'data-droppable-image="" ';
										if (!empty($device->device_bgimg)){
											echo 'style="background-image: url(\''.$target_dir.$device->device_bgimg.'\')"';
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
									        'onclick="deleteDeviceImg('.$_GET['iddevice'].', '.$iduser.', event)">'.
										_('Delete Image').
									'</button>'.
								'</div>'.
							'</label>'.
						'</div>'.
					'</div>'.
				'</div>';
		echo
			'<script type="text/javascript">'.
				
				'$(document).ready(function() {'.
					'$("#popupTitle").html("'._("Upload Device Image").'");';
					if (!empty($device->device_bgimg)){
						echo
						'$("#deleteBtn").show();'.
						'$("#uploadBtn").hide();';
					}
					echo
				'});'.
			'</script>';
	}
}

?>