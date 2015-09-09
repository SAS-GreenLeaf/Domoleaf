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
					'<h3 id="uploadSuccess">Success</h3>'.
					'<h3 id="uploadFail">Fail</h3>'.
						'<div class="cd-panel">'.
						'<div class="cd-panel-content">'.
							'<label for="image">'.
								'<form id="formUpload" action="" method="post" enctype="multipart/form-data" class="image-select" data-droppable="">'.
									'<input id="image" type="file" name="fileToUpload" data-droppable-input=""/>'.
									'<input id="iddevice" type="hidden" value="'.$_GET['iddevice'].'"/>'.
									'<input id="userid" type="hidden" value="'.$iduser.'"/>'.
									'<i class="fa fa-camera fa-2x image-select__icon"></i>'.
									'<div class="image-select__message"></div>'.
									'<div class="bg-image aspect-square"'.
										  'data-droppable-image="" ';
									if (!empty($device->device_bgimg)){
										echo 'style="background-image: url(\''.$target_dir.$device->device_bgimg.'\')"';
									}
									echo '>'.
									'</div>'.
								'</form>'.
								'<div class="center padding-top">'.
									'<button class="btn btn-greenleaf" onclick="$(\'#formUpload\').submit()">Upload Image</button>'.
									'<button id="deleteBtn" class="btn btn-danger margin-left">Delete Image</button>'.
								'</div>'.
							'</label>'.
						'</div>'.
					'</div>'.
				'</div>';
		echo
			'<script type="text/javascript">$("#popupTitle").html("'._("Click or drag file").'");</script>';
		if (!empty($device->device_bgimg)){
			echo '<script type="text/javascript">$("#deleteBtn").show();</script>';
		}
	}
}

?>