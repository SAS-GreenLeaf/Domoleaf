<?php 

include('header.php');

if (!empty($_GET['room_id_device']) || !empty($_GET['id_smartcmd'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['room_id_device']));
	$request -> add_request('countElemSmartcmd', array($_GET['id_smartcmd']));
	
	$result  =  $request -> send_request();
	$listoptdevice = $result->confDeviceRoomOpt;
	$available_opt = array ("12", "13", "54", "96", "363", "364", "365", "366",
							"367", "368", "383", "388", "392", "393", "394", "410");
	
	$idexec = $result->countElemSmartcmd + 1;
	if (empty($listoptdevice)) {
		return;
	}
	$display_rgb = '';
	foreach ($listoptdevice as $option) {
		
		if (in_array($option->option_id, $available_opt)) {
			if (($option->option_id == 392 || $option->option_id == 393
				|| $option->option_id == 394 || $option->option_id == 410)) {
				if ($option->option_id == 410) {
					$display_rgb = '
					<li class="list-item">
						<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
						     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', 410, '.$idexec.')">
							<input type="text" value="410" hidden>
							'._('RGBW').'
						</div>
					</li>';
				}
				else if (empty($display_rgb)) {
					$display_rgb = '
					<li class="list-item">
						<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
						     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', 392, '.$idexec.')">
							<input type="text" value="392" hidden>
							'._('RGB').'
						</div>
					</li>';
				}
			}
			else if(($option->option_id != 392 && $option->option_id != 393 && $option->option_id != 394 && $option->option_id != 410)){
				echo '
					<li class="list-item">
						<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
						     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', '.$option->option_id.', '.$idexec.')">
							<input type="text" value="'.$option->option_id.'" hidden>
							'.$option->name.'
						</div>
					</li>';
			}
		}
		
	}
	echo $display_rgb;
	
	echo '
		<script type="text/javascript">
			$(".btn-draggable").draggable({
				appendTo: "#drop-smartcmd",
				helper: "clone",
				revert: "invalid",
				start: function() {
					dropZoneAnimate();
				},
				stop: function() {
					dropZoneStop();
				}
			});
		</script>';
			
}
?>