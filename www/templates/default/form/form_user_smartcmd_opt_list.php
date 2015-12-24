<?php 

include('header.php');

if (!empty($_GET['room_id_device']) || !empty($_GET['id_smartcmd'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['room_id_device']));
	$request -> add_request('countElemSmartcmd', array($_GET['id_smartcmd']));
	
	$result  =  $request -> send_request();
	$listoptdevice = $result->confDeviceRoomOpt;
	$available_opt = array(
			12  => "12",
			13  => "13",
			54  => "54",
			96  => "96",
			357 => "357",
			358 => "358",
			359 => "359",
			360 => "360",
			361 => "361",
			363 => "363",
			364 => "364",
			365 => "365",
			366 => "366",
			367 => "367",
			368 => "368",
			383 => "383",
			388 => "388",
			392 => "392",
			393 => "393",
			394 => "394",
			400 => "400",
			401 => "401",
			402 => "402",
			403 => "403",
			404 => "404",
			405 => "405",
			406 => "406",
			410 => "410");
	
	$idexec = $result->countElemSmartcmd + 1;
	if (empty($listoptdevice)) {
		return;
	}
	$display_rgb = '';
	foreach ($listoptdevice as $option) {
		$option_id = $option->option_id;
		
		if (!empty($available_opt[$option_id])) {
			if (($option_id == 392 || $option_id == 393 || $option_id == 394 || $option_id == 410)) { //RGBW
				if ($option_id == 410) {
					$display_rgb =
					'<li class="list-item">
						<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
						     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', 410, '.$idexec.')">
							<input type="text" value="410" hidden>
							'._('RGBW').'
						</div>
					</li>';
				}
				else if (empty($display_rgb)) { //RGB
					$display_rgb =
					'<li class="list-item">
						<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
						     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', 392, '.$idexec.')">
							<input type="text" value="392" hidden>
							'._('RGB').'
						</div>
					</li>';
				}
			}
			else if (($option_id != 392 && $option_id != 393 && $option_id != 394 && $option_id != 410)){ //OTHER
				echo '
					<li class="list-item">
						<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
						     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', '.$option_id.', '.$idexec.')">
							<input type="text" value="'.$option_id.'" hidden>
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