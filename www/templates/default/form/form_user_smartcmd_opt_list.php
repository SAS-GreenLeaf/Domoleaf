<?php 

include('header.php');

if (!empty($_GET['room_id_device']) || !empty($_GET['id_smartcmd'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['room_id_device']));
	$request -> add_request('countElemSmartcmd', array($_GET['id_smartcmd']));
	
	$result  =  $request -> send_request();
	$listoptdevice = $result->confDeviceRoomOpt;
	$option_rgb = 0;
	$available_opt = array ("12", "13", "54", "96", "363", "364", "365", "366",
							"367", "368", "383", "388", "392", "393", "394");
	
	$idexec = $result->countElemSmartcmd + 1;
	
	foreach ($listoptdevice as $option) {
		if (in_array($option->option_id, $available_opt)) {
			if (($option->option_id == 392 || $option->option_id == 393 || $option->option_id == 394) && $option_rgb == 0) {
				echo '
					<div id="btn-option-'.$_GET['room_id_device'].'" class="btn btn-greenleaf btn-draggable"
					     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', '.$option->option_id.', '.$idexec.')">
						<input type="text" value="'.$option->option_id.'" hidden>
						RGB
					</div>';
				$option_rgb = 1;
			}
			else if(($option->option_id != 392 && $option->option_id != 393 && $option->option_id != 394)){
				echo '
					<div id="btn-option-'.$_GET['room_id_device'].'" class="btn btn-greenleaf btn-draggable"
					     onclick="onclickDropNewElem('.$_GET['id_smartcmd'].', '.$_GET['room_id_device'].', '.$option->option_id.', '.$idexec.')">
						<input type="text" value="'.$option->option_id.'" hidden>
						'.$option->name.'
					</div>';
			}
		}
		
	}
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