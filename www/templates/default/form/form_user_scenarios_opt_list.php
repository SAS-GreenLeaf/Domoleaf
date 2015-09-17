<?php 

include('header.php');

if (!empty($_GET['room_id_device'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['room_id_device']));
	
	$result  =  $request -> send_request();
	$listoptdevice = $result->confDeviceRoomOpt;
	$option_rgb = 0;
	$available_opt = array ("12", "13", "54", "96", "363", "364", "365", "366",
							"367", "368", "383", "388", "392", "393", "394");
	
	foreach ($listoptdevice as $option) {
		if (in_array($option->option_id, $available_opt)) {
			if (($option->option_id == 392 || $option->option_id == 393 || $option->option_id == 394) && $option_rgb == 0) {
				echo '
					<div id="btn-option-'.$_GET['room_id_device'].'" class="btn btn-greenleaf btn-draggable">
						<input type="text" value="'.$option->option_id.'" hidden>
						RGB
					</div>';
				$option_rgb = 1;
			}
			else if(($option->option_id != 392 && $option->option_id != 393 && $option->option_id != 394)){
				echo '
					<div id="btn-option-'.$_GET['room_id_device'].'" class="btn btn-greenleaf btn-draggable">
						<input type="text" value="'.$option->option_id.'" hidden>
						'.$option->namefr.'
					</div>';
			}
		}
		
	}
	echo '
		<script type="text/javascript">
			$(".btn-draggable").draggable({
				appendTo: "#drop-smartcmd",
				helper: "clone",
				revert: true,
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